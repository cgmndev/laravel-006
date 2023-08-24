<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'rol_id',
        'avatar',
        'name',
        'email',
        'password',
        'activo',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function rol(): BelongsTo
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }

    // Relación muchos a muchos
    // withPivot('activo') es para indicar que la tabla pivote tiene un campo llamado activo.
    // Con esto, este campo podrá ser utilizado mas adelante en las consultas.
    public function carreras(): BelongsToMany
    {
        return $this->belongsToMany(Carrera::class)->withPivot('activo');
    }

    public function asignaturas(): BelongsToMany
    {
        return $this->belongsToMany(Asignatura::class)->withPivot('aprobada');
    }


    // Los scope son métodos que se pueden encadenar en las consultas
    // Ejemplo: User::estudiantes()->get();
    //
    // Importante el uso de las constantes de los Roles, se encuentra centralizada tanto para creación y consulta.
    // Debe llamarse así a través de todo el proyecto, es una buena práctica ante eventuales cambios.
    public function scopeEstudiantes(Builder $builder): Builder
    {
        return $builder->where('rol_id', Rol::ESTUDIANTE);
    }

    public function scopeProfesores(Builder $builder): Builder
    {
        return $builder->where('rol_id', Rol::PROFESOR);
    }

    public function scopeAdministradores(Builder $builder): Builder
    {
        return $builder->where('rol_id', Rol::ADMIN);
    }

    public function scopeActivo(Builder $builder): Builder
    {
        return $builder->where('activo', true);
    }
}
