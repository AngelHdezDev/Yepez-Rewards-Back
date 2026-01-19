<?php

namespace App\Http\Controllers\Sucursal;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sucursal\RedeemPointsRequest;
use App\Models\Reward;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Exception;
use App\Jobs\RedeemRewardJob;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    /**
     * Procesa la solicitud de canje de un premio por puntos.
     *
     * @param RedeemPointsRequest $request
     * @return JsonResponse
     */
    
    

    public function lastTransactions()
    {
        $userId = Auth::id();
        if (!$userId) {
            return response()->json([
                'message' => 'Unauthorized: Authentication required.'
            ], 401);
        }

        try {
            $sucursalTransaction = Transaction::where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get();

            return response()->json([
                'message' => 'Transacciones recuperadas exitosamente.',
                'userId' => $userId,
                'transactions' => $sucursalTransaction,
            ], 200);

        } catch (\Exception $e) {
            // Manejo de errores de base de datos o consulta
            return response()->json([
                'message' => 'Error interno del servidor: No se pudieron recuperar los tickets.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function countTransactions()
    {
        $userId = Auth::id();
        if (!$userId) {
            return response()->json([
                'message' => 'Unauthorized: Authentication required.'
            ], 401);
        }

        try {
            $transactionCount = Transaction::where('user_id', $userId)->count();

            return response()->json([
                'message' => 'Conteo de transacciones recuperado exitosamente.',
                'userId' => $userId,
                'transaction_count' => $transactionCount,
            ], 200);

        } catch (\Exception $e) {
            // Manejo de errores de base de datos o consulta
            return response()->json([
                'message' => 'Error interno del servidor: No se pudo contar las transacciones.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function countCanjes()
    {
        $userId = Auth::id();
        if (!$userId) {
            return response()->json([
                'message' => 'Unauthorized: Authentication required.'
            ], 401);
        }

        try {
            $canjeCount = Transaction::where('user_id', $userId)
                ->where('type', 'DEBIT')
                ->count();


            return response()->json([
                'message' => 'Conteo de canjes recuperado exitosamente.',
                'userId' => $userId,
                'canje_count' => $canjeCount,
            ], 200);

        } catch (\Exception $e) {
            // Manejo de errores de base de datos o consulta
            return response()->json([
                'message' => 'Error interno del servidor: No se pudo contar los canjes.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getTotalCanjesByUser()
    {
        $userId = Auth::id();
        if (!$userId) {
            return response()->json([
                'message' => 'Unauthorized: Authentication required.'
            ], 401);
        }

        try {
            $canjeCount = Transaction::where('user_id', $userId)
                ->count();


            return response()->json([
                'message' => 'Conteo de canjes recuperado exitosamente.',
                'userId' => $userId,
                'canje_count' => $canjeCount,
            ], 200);

        } catch (\Exception $e) {
            // Manejo de errores de base de datos o consulta
            return response()->json([
                'message' => 'Error interno del servidor: No se pudo contar los canjes.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getTotalTransacitonsByUser(): JsonResponse
    {
        $userId = Auth::id();

        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Authentication required.'
            ], 401);
        }

        try {
            $paginatedTransactions = Transaction::where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return response()->json([
                'success' => true,
                'message' => 'Transacciones recuperadas exitosamente.',
                'data' => [
                    'userId' => $userId,
                    'transactions' => $paginatedTransactions->items(),
                ],
                'pagination' => [
                    'total' => $paginatedTransactions->total(),
                    'current_page' => $paginatedTransactions->currentPage(),
                    'last_page' => $paginatedTransactions->lastPage(),
                    'per_page' => $paginatedTransactions->perPage(),
                    'next_page' => $paginatedTransactions->nextPageUrl(),
                    'prev_page' => $paginatedTransactions->previousPageUrl(),
                ]
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor: No se pudieron recuperar las transacciones.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}


