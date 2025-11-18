<?php

namespace Monarul007\LaravelModularSystem\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'module_settings';
    protected $fillable = [
        'key',
        'value',
        'type',
        'group'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
