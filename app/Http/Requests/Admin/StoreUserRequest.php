<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Solo un administrador autenticado puede crear usuarios.
        // Asumimos que el middleware 'role:admin' ya lo verificó.
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', 'min:8'], // 'confirmed' valida contra password_confirmation
            
            // Nuevo campo: 'role'
            // Debe ser requerido y solo puede ser 'admin' o 'client'
            'role' => [
                'required',
                'string',
                Rule::in(['admin', 'client']), 
            ],
        ];
    }

    /**
     * Personaliza los mensajes de error.
     */
    public function messages(): array
    {
        return [
            'role.required' => 'El campo Rol es obligatorio.',
            'role.in' => 'El Rol seleccionado no es válido. Debe ser "admin" o "client".',
            'email.unique' => 'El correo electrónico ya ha sido registrado.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            // ... puedes añadir más mensajes si lo deseas
        ];
    }
}