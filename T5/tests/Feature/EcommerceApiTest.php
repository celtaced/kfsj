<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Stripe\ApiRequestor;
use Stripe\HttpClient\ClientInterface;
use Symfony\Component\Yaml\Yaml;
use Tests\TestCase;

class EcommerceApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_openapi_documents_every_api_route(): void
    {
        $document = Yaml::parseFile(public_path('openapi.yaml'));
        foreach (app('router')->getRoutes() as $route) {
            $path = '/'.$route->uri();
            if (! str_starts_with($path, '/api/') || $path === '/api/documentation') {
                continue;
            }
            foreach (array_diff($route->methods(), ['HEAD']) as $method) {
                $this->assertArrayHasKey(
                    strtolower($method), $document['paths'][$path] ?? [],
                    "OpenAPI no documenta $method $path"
                );
            }
        }
        $this->get('/api/documentation')->assertOk()->assertSee('swagger-ui-bundle.js');
        $this->get('/docs-assets/swagger-ui-bundle.js')->assertOk();
    }

    public function test_swagger_assets_have_browser_compatible_content_types(): void
    {
        $css = $this->get('/docs-assets/swagger-ui.css')->assertOk();
        $js = $this->get('/docs-assets/swagger-ui-bundle.js')->assertOk();

        $this->assertStringStartsWith('text/css', $css->headers->get('Content-Type'));
        $this->assertStringStartsWith('application/javascript', $js->headers->get('Content-Type'));
        $this->get('/docs-assets/unknown.css')->assertNotFound();
    }

    public function test_registration_login_and_token_revocation(): void
    {
        $this->get('/api/orders')->assertUnauthorized()->assertJsonStructure(['message']);
        $credentials = [
            'name' => 'Ana',
            'email' => 'ana@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ];
        $register = $this->postJson('/api/register', $credentials)->assertCreated()
            ->assertJsonStructure(['user' => ['id', 'email'], 'token']);
        $token = $register->json('token');
        $this->postJson('/api/register', $credentials)->assertUnprocessable();
        $this->postJson('/api/login', ['email' => $credentials['email'], 'password' => 'wrong'])
            ->assertUnprocessable();
        $this->postJson('/api/login', ['email' => $credentials['email'], 'password' => $credentials['password']])
            ->assertOk()->assertJsonStructure(['token']);
        $this->withToken($token)->postJson('/api/logout')->assertOk();
        app('auth')->forgetGuards();
        $this->withToken($token)->getJson('/api/orders')->assertUnauthorized();
    }

    public function test_product_crud_requires_authentication_and_validates_input(): void
    {
        $product = ['name' => 'Mochila', 'description' => 'Prueba', 'price_cents' => 3500];
        $this->postJson('/api/products', $product)->assertUnauthorized();
        $user = User::factory()->create();
        $customerToken = $user->createToken('test')->plainTextToken;
        $this->withToken($customerToken)->postJson('/api/products', $product)->assertForbidden();
        $user->forceFill(['is_admin' => true])->save();
        app('auth')->forgetGuards();
        $token = $user->createToken('test')->plainTextToken;
        $this->withToken($token)->postJson('/api/products', $product + ['active' => true])->assertCreated();
        $this->getJson('/api/products')->assertOk()->assertJsonPath('data.0.name', 'Mochila');
        $this->withToken($token)->patchJson('/api/products/1', ['price_cents' => -10])->assertUnprocessable();
        $this->withToken($token)->patchJson('/api/products/1', ['price_cents' => 4500])
            ->assertOk()->assertJsonPath('price_cents', 4500);
        $this->withToken($token)->deleteJson('/api/products/1')->assertOk();
        $this->getJson('/api/products/1')->assertNotFound();
    }

    public function test_orders_use_server_prices_preserve_snapshots_and_are_private(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $product = Product::create(['name' => 'Camiseta', 'description' => 'Prueba', 'price_cents' => 1500]);
        $token = $user->createToken('test')->plainTextToken;
        $response = $this->withToken($token)->postJson('/api/orders', [
            'items' => [['product_id' => $product->id, 'quantity' => 2, 'price_cents' => 1]],
        ])->assertCreated()->assertJsonPath('total_cents', 3000)
            ->assertJsonPath('items.0.unit_price_cents', 1500);
        $id = $response->json('id');
        $product->update(['price_cents' => 2000, 'active' => false]);
        $this->withToken($token)->postJson('/api/orders', [
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
        ])->assertUnprocessable();
        $this->withToken($token)->getJson('/api/orders/'.$id)
            ->assertOk()->assertJsonPath('items.0.unit_price_cents', 1500);
        app('auth')->forgetGuards();
        $otherToken = $other->createToken('test')->plainTextToken;
        $this->withToken($otherToken)->getJson('/api/orders/'.$id)->assertNotFound();
        $this->withToken($otherToken)->postJson('/api/orders/'.$id.'/payment-intent')->assertNotFound();
        $this->withToken($otherToken)->getJson('/api/orders')->assertJsonCount(0, 'data');
    }

    public function test_payment_requires_stripe_configuration(): void
    {
        config(['services.stripe.secret' => null]);
        $user = User::factory()->create();
        $order = Order::create([
            'uuid' => (string) Str::uuid(), 'user_id' => $user->id,
            'total_cents' => 1500, 'currency' => 'usd', 'status' => 'pending',
        ]);
        $token = $user->createToken('test')->plainTextToken;
        $this->withToken($token)->postJson('/api/orders/'.$order->id.'/payment-intent')
            ->assertStatus(503)->assertJsonPath('message', 'Stripe no esta configurado.');
    }

    public function test_payment_intent_uses_order_amount_and_is_reused(): void
    {
        config(['services.stripe.secret' => 'sk_test_fake']);
        $user = User::factory()->create();
        $order = Order::create([
            'uuid' => (string) Str::uuid(), 'user_id' => $user->id,
            'total_cents' => 1500, 'currency' => 'usd', 'status' => 'pending',
        ]);
        $client = \Mockery::mock(ClientInterface::class);
        $client->shouldReceive('request')->twice()->andReturnUsing(
            function ($method, $url, $headers, $params) use ($order) {
                if ($method === 'post') {
                    $this->assertSame(1500, $params['amount']);
                    $this->assertSame('usd', $params['currency']);
                    $this->assertSame((string) $order->id, $params['metadata']['order_id']);
                    $this->assertContains('Idempotency-Key: t5-order-'.$order->uuid, $headers);
                } else {
                    $this->assertSame('get', $method);
                    $this->assertStringContainsString('pi_test', $url);
                }

                return [json_encode([
                    'id' => 'pi_test', 'object' => 'payment_intent',
                    'client_secret' => 'pi_test_secret_test', 'amount' => 1500, 'currency' => 'usd',
                ], JSON_THROW_ON_ERROR), 200, []];
            }
        );
        ApiRequestor::setHttpClient($client);
        try {
            $token = $user->createToken('test')->plainTextToken;
            $this->withToken($token)->postJson('/api/orders/'.$order->id.'/payment-intent')
                ->assertOk()->assertJsonPath('client_secret', 'pi_test_secret_test');
            $this->withToken($token)->postJson('/api/orders/'.$order->id.'/payment-intent')->assertOk();
            $this->assertDatabaseCount('payments', 1);
        } finally {
            ApiRequestor::setHttpClient(null);
        }
    }

    public function test_signed_webhooks_record_failure_and_success_without_regressing(): void
    {
        config(['services.stripe.webhook_secret' => 'whsec_test']);
        $user = User::factory()->create();
        $order = Order::create([
            'uuid' => (string) Str::uuid(), 'user_id' => $user->id,
            'total_cents' => 1500, 'currency' => 'usd', 'status' => 'pending',
        ]);
        $order->payment()->create([
            'stripe_payment_intent_id' => 'pi_test', 'amount_cents' => 1500,
            'currency' => 'usd', 'status' => 'pending',
        ]);

        $this->sendStripeEvent('payment_intent.payment_failed')->assertOk();
        $this->assertDatabaseHas('payments', ['stripe_payment_intent_id' => 'pi_test', 'status' => 'failed']);
        $this->sendStripeEvent('payment_intent.succeeded')->assertOk();
        $this->sendStripeEvent('payment_intent.payment_failed')->assertOk();
        $this->assertDatabaseHas('payments', ['stripe_payment_intent_id' => 'pi_test', 'status' => 'succeeded']);
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'paid']);

        $this->postJson('/api/stripe/webhook', ['id' => 'invalid'])->assertBadRequest();
        $this->sendStripeEvent('payment_intent.succeeded', 999)->assertBadRequest();
    }

    public function test_stripe_api_failure_returns_json_without_marking_order_paid(): void
    {
        config(['services.stripe.secret' => 'sk_test_fake']);
        $user = User::factory()->create();
        $order = Order::create([
            'uuid' => (string) Str::uuid(), 'user_id' => $user->id,
            'total_cents' => 1500, 'currency' => 'usd', 'status' => 'pending',
        ]);
        $client = \Mockery::mock(ClientInterface::class);
        $client->shouldReceive('request')->once()->andReturn([
            '{"error":{"type":"api_error","message":"Stripe unavailable"}}', 500, [],
        ]);
        ApiRequestor::setHttpClient($client);
        try {
            $token = $user->createToken('test')->plainTextToken;
            $this->withToken($token)->postJson('/api/orders/'.$order->id.'/payment-intent')
                ->assertStatus(502)->assertJsonPath('message', 'No fue posible comunicarse con Stripe.');
            $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'pending']);
        } finally {
            ApiRequestor::setHttpClient(null);
        }
    }

    private function sendStripeEvent(string $type, int $amount = 1500)
    {
        $payload = json_encode([
            'id' => 'evt_test',
            'object' => 'event',
            'type' => $type,
            'data' => ['object' => [
                'id' => 'pi_test', 'object' => 'payment_intent', 'amount' => $amount,
                'amount_received' => $amount, 'currency' => 'usd',
                'last_payment_error' => ['code' => 'card_declined'],
            ]],
        ], JSON_THROW_ON_ERROR);
        $timestamp = time();
        $signature = hash_hmac('sha256', $timestamp.'.'.$payload, 'whsec_test');

        return $this->call('POST', '/api/stripe/webhook', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_STRIPE_SIGNATURE' => "t=$timestamp,v1=$signature",
        ], $payload);
    }
}
