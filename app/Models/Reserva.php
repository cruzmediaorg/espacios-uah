<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property Carbon $fecha_cancelacion
 * @property Carbon $fecha_aprobacion
 * @property Carbon $fecha_rechazo
 * @property Carbon $fecha
 * @property string $hora_inicio
 * @property string $hora_fin
 * @property string $comentario
 * @property int $cancelado_por
 * @property int $reservable_id
 * @property string $reservable_type
 * @property User $usuario
 */
class Reserva extends Model
{
    const TABLA = 'reservas';
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reservable_id',
        'reservable_type',
        'asignado_a',
        'fecha',
        'hora_inicio',
        'hora_fin',
        'comentario',
        'fecha_aprobacion',
        'fecha_rechazo',
        'fecha_cancelacion',
        'cancelado_por'
    ];
    /**
     * Get the model that owns the reserva.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function reservable(): MorphTo
    {
        return $this->morphTo();
    }


    /**
     * Obtener el usuario al que se le asignó la reserva
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'asignado_a');
    }

    /**
     * Obtener si la reserva está aprobada, o cancelada o rechazada
     */
    public function estado()
    {
        return match (true) {
            $this->fecha < now() => 'cerrada',
            $this->fecha_aprobacion !== null => 'aprobada',
            $this->fecha_cancelacion !== null => 'cancelada',
            $this->fecha_rechazo !== null => 'rechazada',
            default => 'pendiente',
        };
    }

    /**
     * Scope para obtener las reservas de un usuario
     */
    public function scopeDeUsuario($query, $usuario)
    {
        return $query->where('asignado_a', $usuario);
    }
}
