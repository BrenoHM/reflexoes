<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class HealthController extends Controller
{
    /**
     * Health check para o Render (e outros orquestradores) verificarem se a
     * aplicação está respondendo. Propositalmente não consulta o banco: uma
     * instabilidade momentânea no MySQL não deve derrubar o container.
     */
    public function __invoke(): JsonResponse
    {
        return response()->json(['status' => 'ok']);
    }
}
