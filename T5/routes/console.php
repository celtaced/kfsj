<?php

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('user:make-admin {email}', function () {
    $user = User::where('email', $this->argument('email'))->first();
    if (! $user) {
        $this->error('Usuario no encontrado.');

        return 1;
    }
    $user->forceFill(['is_admin' => true])->save();
    $this->info('Usuario convertido en administrador.');

    return 0;
})->purpose('Conceder permisos de administrador a un usuario existente');
