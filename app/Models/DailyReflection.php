<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DailyReflection extends Model
{
    /** @use HasFactory<\Database\Factories\DailyReflectionFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'paragrafo',
        'descricao_paragrafo',
        'reflexao',
    ];

    protected function casts(): array
    {
        return [
            'paragrafo' => 'integer',
        ];
    }
}
