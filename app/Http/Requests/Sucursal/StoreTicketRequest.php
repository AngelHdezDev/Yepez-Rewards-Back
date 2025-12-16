<?php

namespace App\Http\Requests\Sucursal;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
// Importaciones necesarias para manejar el error de validación
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreTicketRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
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
                    // Aseguramos que solo se compare con el branch_id enviado en la solicitud
                    return $query->where('branch_id', $this->branch_id);
                }),
            ],

            // Fecha de emisión (formato yyyy-mm-dd hh:mm:ss)
            'issue_date' => ['required', 'date_format:Y-m-d H:i:s'],
        ];
    }

    /**
     * Define los mensajes de error personalizados.
     */
    public function messages(): array
    {
        return [
            'user_id.required' => 'El ID del usuario es obligatorio.',
            'branch_id.required' => 'El ID de la sucursal es obligatorio.',
            'amount.required' => 'El monto de la compra es obligatorio.',
            'amount.numeric' => 'El monto debe ser un valor numérico.',
            'amount.min' => 'El monto debe ser al menos de 0.01.',
            'ticket_number.required' => 'El número de ticket es obligatorio.',
            'ticket_number.unique' => 'Este número de ticket ya ha sido registrado para esta sucursal.',
            'issue_date.required' => 'La fecha de emisión del ticket es obligatoria.',
            'issue_date.date_format' => 'La fecha debe tener el formato YYYY-MM-DD HH:MM:SS.',
        ];
    }

    /**
     * Sobreescribe el manejo de validación fallida para devolver el JSON detallado.
     * * @param \Illuminate\Contracts\Validation\Validator $validator
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        // Lanzamos una excepción que devuelve la respuesta JSON con el código 422.
        throw new HttpResponseException(response()->json([
            // Mensaje principal
            'message' => 'La solicitud de ticket contiene datos inválidos.',
            
            // ESTO ES LO CRUCIAL: 'errors' con todos los mensajes detallados.
            'errors' => $validator->errors(), 
            
        ], 422)); // Status 422 Unprocessable Content
    }
}