<?php

namespace App\Http\Requests\Patient;

use Illuminate\Foundation\Http\FormRequest;

class StoreGrossesseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date_debut_grossesse'            => ['required', 'date', 'before_or_equal:today'],
            'date_accouchement_prevue'         => ['required', 'date', 'after:date_debut_grossesse'],
            'groupe_sanguin'                  => ['nullable', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
            'nombre_grossesses_anterieures'   => ['integer', 'min:0', 'max:20'],
            'nombre_accouchements_anterieurs' => ['integer', 'min:0', 'max:20'],
            'antecedents_obstetricaux'        => ['nullable', 'string', 'max:2000'],
        ];
    }
}
