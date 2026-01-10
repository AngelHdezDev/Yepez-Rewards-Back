<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Cambiar a true si no tienes lógica de permisos específica aquí
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        // Obtenemos el ID del usuario desde el parámetro de la ruta {user}
        // Si tu ruta es /users/{id}, usa $this->route('id')
        $userId = $this->route('user') ?? $this->route('id');

        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                // Validamos que sea único pero ignoramos al usuario actual
                Rule::unique('users', 'email')->ignore($userId),
            ],
            // 'nullable' permite que el campo esté vacío sin error
            // 'sometimes' asegura que si el campo está presente, cumpla las reglas
            'password' => 'nullable|string|min:8|max:100',
        ];
    }

    /**
     * Mensajes personalizados (Opcional)
     */
    public function messages(): array
    {
        return [
            'email.unique' => 'Este correo electrónico ya está registrado en otra sucursal.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
        ];
    }
}