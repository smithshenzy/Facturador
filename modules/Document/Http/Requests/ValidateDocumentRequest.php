<?php

namespace Modules\Document\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ValidateDocumentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = $this->input('id');
        return [
            'month_start' => [
                'required',
            ],
            'document_type_id' => [
                'required',
            ],
           
        ];
    }

    
    public function messages()
    {
        return [
            'month_start.required' => 'El campo es obligatorio.',
            'document_type_id.required' => 'El campo es obligatorio.',
        ];
    }
}