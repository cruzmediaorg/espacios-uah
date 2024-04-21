<?php

namespace App\Http\Controllers;

use App\Http\Resources\EspacioCalendarResource;
use App\Http\Resources\ReservaCalendarResource;
use App\Models\Espacio;
use App\Models\Localizacion;
use App\Models\Reserva;
use App\Models\TipoEspacio;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CalendarioController extends Controller
{
    public function index(Request $request)
    {

        $espacios = Espacio::all();

        if ($request->has('tipo')) {
            if ($request->tipo !== 'all') {
                $tipo = TipoEspacio::where('nombre', $request->tipo)->first();
                $espacios = $espacios->where('tiposespacios_id', $tipo->id);
            }
        }

        if ($request->has('localizacion')) {
            $localizacion = $request->localizacion;
            $espacios = $espacios->whereIn('localizacion_id', $localizacion);
        }

        $reservas = Reserva::where('reservable_type', 'App\Models\Espacio')
            ->whereIn('reservable_id', $espacios->pluck('id'))
            ->get();

        $localizaciones = Localizacion::all()->pluck('nombre', 'id');

        return Inertia::render('Calendario/Index', [
            'espacios' => EspacioCalendarResource::collection($espacios),
            'reservas' => ReservaCalendarResource::collection($reservas),
            'tipo' => $request->tipo ?? null,
            'localizacion' => $request->localizacion ?? null,
            'localizaciones' => $localizaciones,
        ]);
    }
}