<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Carrera extends Model
{
    protected $table = 'carreras';

    protected $fillable = [
        'nombre',
        'slug',
        'arancel',
        'descripcion',
        'orden',
        'activo',
        'destacada',
    ];

    // Indicamos que el campo activo es booleano.
    protected $casts = [
        'activo' => 'boolean',
        'destacada' => 'boolean',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot('activo');
    }
}
