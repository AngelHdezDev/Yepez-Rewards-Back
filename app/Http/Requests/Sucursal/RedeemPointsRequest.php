<?php

namespace App\Http\Requests\Sucursal;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Clase para validar la solicitud de canje de un premio por parte de una Sucursal.
 * Requiere el ID del cliente y el ID del premio.
 */
class RedeemPointsRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado a realizar esta solicitud.
     * * @return bool
     */
    public function authorize(): bool
    {
        // Dado que esta solicitud pertenece a la API de la Sucursal,
        // asumimos que el middleware ya ha verificado que el usuario 
        // autenticado tiene el rol 'sucursal'.
        return true; 
    }

    /**
     * Obtiene las reglas de validación que se aplican a la solicitud.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Valida que el user_id exista en la tabla de usuarios.
            'cliente_id' => ['required', 'integer', 'exists:users,id'],
            
            // Valida que el reward_id exista en la tabla de premios.
            'premio_id' => ['required', 'integer', 'exists:rewards,id'], 
        ];
    }

    /**
     * Obtiene los mensajes de error personalizados para las reglas de validación.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'user_id.required' => 'El ID de cliente es obligatorio para el canje.',
            'user_id.exists' => 'El cliente especificado no existe en nuestros registros.',
            'reward_id.required' => 'El ID del premio es obligatorio.',
            'reward_id.exists' => 'El ID del premio no es válido o el premio no existe.',
        ];
    }
}