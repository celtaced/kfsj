<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/api/documentation');
});

Route::view('/api/documentation', 'documentation');
Route::get('/docs-assets/{file}', function (string $file) {
    $assets = [
        'swagger-ui.css' => 'text/css',
        'swagger-ui-bundle.js' => 'application/javascript',
    ];
    abort_unless(isset($assets[$file]), 404);

    return response()->file(
        base_path('vendor/swagger-api/swagger-ui/dist/'.$file),
        ['Content-Type' => $assets[$file]]
    );
});
