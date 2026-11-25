<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'description', 'price_cents', 'active'];

    protected function casts(): array
    {
        return ['price_cents' => 'integer', 'active' => 'boolean'];
    }
}
