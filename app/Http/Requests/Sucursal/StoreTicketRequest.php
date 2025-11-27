<?php

// ESTE ES EL CAMBIO CLAVE: El namespace debe reflejar la ubicación de la carpeta
namespace App\Http\Requests\Sucursal;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTicketRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * El middleware de rutas debe asegurar que solo el personal de la sucursal pueda acceder.
     */
    public function authorize(): bool
    {
        return true; // La autorización se maneja en el middleware de rutas 
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // El ID del usuario (cliente) debe existir
            'user_id' => ['required', 'integer', Rule::exists('users', 'id')],

            // El ID de la sucursal debe existir
            'branch_id' => ['required', 'integer', Rule::exists('branches', 'id')],

            // El monto de la compra (debe ser un número, al menos 0.01)
            'amount' => ['required', 'numeric', 'min:0.01'],

            // El número de ticket (debe ser único por sucursal para evitar duplicados)
            'ticket_number' => [
                'required', 
                'string', 
                'max:255',
                // Validación de unicidad compuesta: ticket_number debe ser único junto con branch_id
                Rule::unique('tickets')->where(function ($query) {
                    return $query->where('branch_id', $this->branch_id);
                }),
            ],

            // Fecha de emisión (formato yyyy-mm-dd hh:mm:ss)
            'issue_date' => ['required', 'date_format:Y-m-d H:i:s'],
        ];
    }
}