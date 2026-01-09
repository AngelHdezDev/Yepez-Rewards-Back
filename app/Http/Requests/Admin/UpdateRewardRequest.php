<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRewardRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado a realizar esta solicitud.
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
        // Obtenemos el ID del modelo desde la ruta para las validaciones de unicidad
        // Asumiendo que en tu ruta el parámetro se llama {reward} o {id}
        $rewardId = $this->route('reward') ?? $this->route('id');

        return [
            'name' => [
                'sometimes', 
                'string', 
                'min:5', 
                'max:255'
            ],
            'description' => [
                'sometimes', 
                'string', 
                'min:10'
            ],
            'cost_points' => [
                'sometimes', 
                'integer', 
                'min:1'
            ],
            'stock' => [
                'sometimes', 
                'integer', 
                'min:0'
            ],
            'image_url' => [
                'nullable', 
                'image', 
                'mimes:jpeg,png,jpg,webp', 
                'max:2048'
            ],
            'code' => [
                'sometimes', 
                'string', 
                'min:5', 
                'max:255',
                // Ejemplo de cómo ignorar el registro actual si el código es único:
                // Rule::unique('rewards', 'code')->ignore($rewardId),
            ],
            'is_active' => [
                'sometimes', 
                'integer', 
                'min:0'
            ],
        ];
    }

    /**
     * Personaliza los mensajes de error.
     */
    public function messages(): array
    { 
        return [
            'name.min'             => 'El nombre debe tener al menos 5 caracteres.',
            'description.min'      => 'La descripción debe tener al menos 10 caracteres.',
            'cost_points.integer'  => 'Los puntos deben ser un número entero.',
            'stock.integer'        => 'El stock debe ser un número válido.',
            'image_url.image'      => 'El archivo debe ser una imagen válida.',
            'image_url.mimes'      => 'Formatos permitidos: jpeg, png, jpg, webp.',
            'image_url.max'        => 'La imagen no debe pesar más de 2MB.',
            'code.min'             => 'El código debe tener al menos 5 caracteres.',
            'is_active.integer'    => 'El estado activo debe ser un valor entero (0 o 1).',
        ];
    }
}