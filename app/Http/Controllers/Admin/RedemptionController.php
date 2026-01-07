<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Redemption;
use Exception;
use Illuminate\Support\Facades\Log;

class RedemptionController extends Controller
{
    public function getAllRedemptions(Request $request)
    {
        try {
            $query = Redemption::with(['user']);

            if ($request->filled('user_id')) {
                $query->where('user_id', $request->user_id);
            }

            if ($request->filled('start_date')) {
                $query->whereDate('created_at', '>=', $request->start_date);
            }

            if ($request->filled('end_date')) {
                $query->whereDate('created_at', '<=', $request->end_date);
            }

            $redemptions = $query->latest()
                ->paginate(20)
                ->withQueryString();

            return response()->json([
                'status' => 'success',
                'message' => 'Redenciones obtenidas correctamente',
                'data' => $redemptions->items(),
                'pagination' => [
                    'current_page' => $redemptions->currentPage(),
                    'last_page' => $redemptions->lastPage(),
                    'per_page' => $redemptions->perPage(),
                    'total' => $redemptions->total(),
                    'next_page_url' => $redemptions->nextPageUrl(),
                    'prev_page_url' => $redemptions->previousPageUrl(),
                ]
            ], 200);

        } catch (Exception $e) {
            
            Log::error("Error en getAllRedemptions: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

       
            return response()->json([
                'status' => 'error',
                'message' => 'Ocurrió un error al procesar la solicitud de redenciones.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal Server Error'
            ], 500);
        }
    }
}
