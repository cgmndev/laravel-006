<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Unidad extends Model
{
    protected $table = 'unidades';

    protected $fillable = [
        'asignatura_id',
        'nombre',
        'slug',
        'descripcion',
        'orden',
        'publicada',
        'electiva',
    ];

    protected $casts = [
        'publicada' => 'boolean',
        'electiva' => 'boolean',
    ];

    public function asignatura(): BelongsTo
    {
        return $this->belongsTo(Asignatura::class, 'asignatura_id');
    }
}
