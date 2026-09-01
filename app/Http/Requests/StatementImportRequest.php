<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StatementImportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
        // return false;

    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'statement_import' => 'required|file|mimes:csv|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'statement_import.required' => 'The statement file is required.',
            'statement_import.mimes' => 'Please upload a CSV file for the statement import.',
        ];
    }
}
