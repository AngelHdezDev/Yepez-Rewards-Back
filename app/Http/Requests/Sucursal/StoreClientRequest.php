<?php

namespace App\Http\Requests\Sucursal;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado a hacer esta solicitud.
     * Solo se debe permitir si el usuario autenticado tiene el rol 'sucursal' o 'yepez'.
     * @return bool
     */
    public function authorize(): bool       
    {
        // Esta validación se puede gestionar a nivel de middleware en las rutas,
        // pero es buena práctica tenerla aquí también.
        // Asumiendo que el middleware 'role:sucursal' ya lo está filtrando:
        return true; 
    }

    /**
     * Obtiene las reglas de validación que se aplican a la solicitud.
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            // El nombre es obligatorio y no debe superar los 255 caracteres
            'name' => ['required', 'string', 'max:255'],
            
            // El email es obligatorio, único en la nueva tabla 'clients' y debe ser un formato válido
            'email' => ['required', 'string', 'email', 'max:255', 'unique:clients,email'],
            
            // La contraseña es obligatoria y debe tener al menos 8 caracteres
            'password' => ['required', 'string', 'min:8'], 
            
            // Opcional: Número de teléfono
            'phone' => ['nullable', 'string', 'max:20'],
        ];
    }

    /**
     * Obtiene los mensajes de error personalizados para las reglas de validación.
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del cliente es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser una dirección válida.',
            'email.unique' => 'Ya existe un cliente registrado con este correo electrónico.',
            'password.required' => 'La contraseña es obligatoria para el nuevo cliente.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
        ];
    }
}