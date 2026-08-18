<?php

namespace App\Http\Controllers;

class MovedLinkController extends Controller
{
    /**
     * Enlaces de /encuesta, /conformidad y /presupuesto de antes de migrar
     * el negocio (Customer/Survey/ServiceOrder/WorkOrder) a Altoparque
     * Central: el token/registro real ya no existe acá. Aviso simple en
     * vez de un 500, sin exponer detalle técnico.
     */
    public function show()
    {
        return response()->view('errors.moved', [], 410);
    }
}
