<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transforma el recurso en un arreglo.
     *
     * @param \Illuminate\Http\Request $request
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
      
        $balance = $this->transactions->sum(function ($transaction) {
            // Suma si es CREDIT, resta si es DEBIT
            if ($transaction->type === 'CREDIT') {
                return $transaction->amount;
            } elseif ($transaction->type === 'DEBIT') {
                return -$transaction->amount;
            }
            return 0;
        });

 
        return [
            // Identificación y datos principales
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            
            // Datos calculados o formateados
            'points' => number_format($balance, 0, '', ','), // Formato 15,000 para display
            'registered_at' => $this->created_at->format('d M Y'), // Formato 14 Oct 2024
            
   
            'raw_points' => $balance, 
            'created_at_iso' => $this->created_at,
            

            'actions' => [
                'can_assign_points' => true,
                'can_edit' => true,
                'can_delete' => true,
            ]
        ];
    }
}