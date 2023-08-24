<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asignatura extends Model
{
    protected $table = 'asignaturas';

    protected $fillable = [
        'user_id',
        'nombre',
        'descripcion',
        'slug',
        'imagen',
        'publicada',
        'destacada'
    ];

    public function profesor(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function unidades(): HasMany
    {
        return $this->hasMany(Unidad::class);
    }

    public function estudiantes(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot('aprobada');
    }
}
