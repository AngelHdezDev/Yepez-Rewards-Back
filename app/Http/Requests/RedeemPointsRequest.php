<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class RedeemPointsRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado a realizar esta solicitud.
     */
    public function authorize(): bool
    {
        // Solo los usuarios autenticados con el rol 'client' pueden canjear.
        // Asumiendo que el middleware 'auth:sanctum' ya se encarga de que esté logueado,
        // esto verifica el rol.
        return $this->user() && $this->user()->hasRole('client');
    }

    /**
     * Obtiene las reglas de validación que se aplican a la solicitud.
     */
    public function rules(): array
    {
        return [
            // El monto debe ser un número entero, requerido y mayor que 0.
            'amount' => ['required', 'integer', 'min:1'],
            // La descripción es opcional, pero si existe, debe ser una cadena.
            'description' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Prepara el validador con lógica adicional después de las reglas básicas.
     * Aquí verificamos el saldo.
     */
    public function withValidator($validator)
    {
        // Añadir una validación after hook para verificar el saldo
        $validator->after(function ($validator) {
            $user = $this->user();
            $amountToRedeem = $this->input('amount');
            
            // Si las reglas básicas ya fallaron, no intentamos esta verificación.
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            // El balance es un Accessor en el modelo User, ya debe estar disponible.
            if ($user->balance < $amountToRedeem) {
                // Si el saldo es insuficiente, añadimos un error al validador.
                $validator->errors()->add(
                    'amount',
                    'Saldo insuficiente. Su saldo actual es de ' . $user->balance . ' puntos.'
                );
            }
        });
    }

    /**
     * Maneja un fallo de autorización.
     */
    protected function failedAuthorization()
    {
        throw new \Illuminate\Auth\Access\AuthorizationException('No tienes permiso para realizar esta acción de canje. Se requiere el rol de cliente.');
    }
    
    /**
     * Define el tipo de respuesta si falla la validación (para API).
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw (new ValidationException($validator))
            ->errorBag($this->errorBag)
            ->redirectTo($this->getRedirectUrl());
    }
}