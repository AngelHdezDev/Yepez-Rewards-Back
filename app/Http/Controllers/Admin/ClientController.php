<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
// La dependencia Spatie/Laravel-Permission se asume aquí
// ya que el modelo User usa el trait HasRoles.

class ClientController extends Controller
{
    /**
     * [ADMIN] Obtiene una lista paginada de usuarios que son clientes (con el rol 'client').
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Resources\Json\ResourceCollection
     */
    public function index(Request $request): ResourceCollection
    {
        // 1. Filtrar solo los usuarios que tienen el rol 'client'
        $clientsQuery = User::role('client') // <-- Filtro actualizado a 'client'
            ->with('transactions'); // Cargamos las transacciones para calcular el balance

        // 2. Aplicar la paginación: 50 usuarios por página
        $clients = $clientsQuery->paginate(50);
        
        // 3. Devolver la colección usando el Resource
        return \App\Http\Resources\UserResource::collection($clients);
    }
}