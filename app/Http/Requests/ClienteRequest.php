<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClienteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'tipo_documento' => 'required|in:dni,ruc',
            'ciudad' => 'required|bail',
            'departamento_codigo' => 'required',
            'provincia_codigo' => 'required',
            'distrito_codigo' => 'required',
            'comentario' => 'required|bail',
            'etapa_id' => 'required|bail',
            'estadodito_id' => 'required|bail',
        ];

        if ($this->input('tipo_documento') === 'ruc') {
            $rules['ruc'] = 'required|numeric|digits:11|starts_with:20,10|unique:clientes,ruc';
            $rules['razon_social'] = 'required|string';
        }

        if ($this->input('tipo_documento') === 'dni') {
            $rules['dni_cliente'] = 'required|numeric|digits:8|unique:clientes,dni_cliente';
            $rules['nombre_cliente'] = 'required|string';
            $rules['apellido_paterno_cliente'] = 'required|string';
        }
        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'tipo_documento.required' => 'Debe seleccionar el tipo de documento.',
            'tipo_documento.in' => 'El tipo de documento debe ser DNI o RUC.',

            'ruc.required' => 'El "Ruc" es obligatorio.',
            'ruc.numeric' => 'El "Ruc" debe ser numérico.',
            'ruc.digits' => 'El "Ruc" debe tener exactamente 11 dígitos.',
            'ruc.starts_with' => 'El "Ruc" debe iniciar con 20 o 10.',
            'ruc.unique' => 'El "Ruc" ya se encuentra registrado.',
            'razon_social.required' => 'La "Razón Social" es obligatorio.',

            'dni_cliente.required' => 'El "DNI" es obligatorio.',
            'dni_cliente.numeric' => 'El "DNI" debe ser numérico.',
            'dni_cliente.digits' => 'El "DNI" debe tener exactamente 8 dígitos.',
            'dni_cliente.unique' => 'El "DNI" ya se encuentra registrado.',
            'nombre_cliente.required' => 'El "Nombre" es obligatorio.',
            'apellido_paterno_cliente.required' => 'El "Apellido Paterno" es obligatorio.',
            'apellido_materno_cliente.required' => 'El "Apellido Materno" es obligatorio.',

            'ciudad.required' => 'La "Ciudad" es obligatorio.',
            'departamento_codigo.required' => 'El "Departamento" es obligatorio.',
            'provincia_codigo.required' => 'La "Provincia" es obligatoria.',
            'distrito_codigo.required' => 'El "Distrito" es obligatorio.',
            'comentario.required' => 'El "Comentario" es obligatorio.',
            'etapa_id.required' => 'La "Etapa" es obligatorio.',
            'estadodito_id.required' => 'El "Estado Dito" es obligatorio.',
        ];
    }
}
