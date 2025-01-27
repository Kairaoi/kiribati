<?php

namespace App\Http\Requests\National\Eregistry;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FolderStoreRequest extends FormRequest
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
        return [
            'index_no' => [
                'required',
                'string',
                'max:255',
                Rule::unique('folders')->whereNull('deleted_at'), // Check for uniqueness in the 'folders' table
            ],
            'folder_name' => [
                'required',
                'string',
                'max:255',
            ],
            'folder_description' => [
                'nullable',
                'string',
            ],
            'is_public' => [
                'nullable',
                'boolean',
            ],
            'is_active' => [
                'nullable',
                'boolean',
            ],
        ];
    }
}
