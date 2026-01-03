<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreRewardRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado a realizar esta solicitud.
     * Normalmente se deja en true si el acceso se controla vía Middleware.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Define las reglas de validación que se aplicarán a los datos.
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required', 
                'string', 
                'min:5', 
                'max:255'
            ],
            'description' => [
                'required', 
                'string', 
                'min:10'
            ],
            'cost_points' => [
                'required', 
                'integer', 
                'min:1'
            ],
            'stock' => [
                'required', 
                'integer', 
                'min:0'
            ],
            'image_url' => [
                'nullable', 
                'image', 
                'mimes:jpeg,png,jpg,webp', 
                'max:2048' // Máximo 2MB
            ],
            'code' => [
                'required', 
                'string', 
                'min:5', 
                'max:255'
            ],
            'is_active' => [
                'required', 
                'integer', 
                'min:0'
            ],
        ];
    }

    /**
     * Personaliza los mensajes de error para que sean amigables al usuario.
     */
    public function messages(): array
    { 
        return [
            'name.required'        => 'El nombre de la recompensa es obligatorio.',
            'name.min'             => 'El nombre debe tener al menos 5 caracteres.',
            'description.required' => 'Debes añadir una descripción detallada.',
            'cost_points.required' => 'Indica cuántos puntos cuesta esta recompensa.',
            'cost_points.integer'  => 'Los puntos deben ser un número entero.',
            'stock.required'       => 'El stock es obligatorio (puedes poner 0).',
            'image_url.image'          => 'El archivo debe ser una imagen válida.',
            'image_url.mimes'          => 'Formatos permitidos: jpeg, png, jpg, webp.',
            'image_url.max'            => 'La imagen no debe pesar más de 2MB.',
            'code.required'        => 'El código de la recompensa es obligatorio.',
            'code.min'             => 'El código debe tener al menos 5 caracteres.',
            'is_active.required'   => 'Debes especificar si la recompensa está activa.',
            'is_active.integer'    => 'El estado activo debe ser un valor entero (0 o 1).',
        ];
    }
}