<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

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
 * @property string $type
 */
class Reserva extends Model
{
    const TABLA = 'reservas';
    const ROUTE_PREFIX = 'reservas';

    use  SoftDeletes, LogsActivity;

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
        'cancelado_por',
        'type'
    ];

    protected static $logName = 'reservas';

    /**
     * Opciones de configuración para el log de actividad
     */

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->setDescriptionForEvent(fn (string $eventName) => "La reserva #" . $this->id . " (" . $this->reservable->nombre . ")
               ha sido " . __('messages.' . $eventName))
            ->useLogName('reservas')
            ->logOnly(['reservable_id', 'reservable_type', 'asignado_a', 'fecha', 'hora_inicio', 'hora_fin', 'comentario', 'fecha_aprobacion', 'fecha_rechazo', 'fecha_cancelacion', 'cancelado_por', 'type']);
    }

    /**
     * Obtener el modelo al que se le asignó la reserva.
     *
     * @return MorphTo
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
