<?php

namespace App\Http\Requests\National\Eregistry;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FileAccessStoreRequest extends FormRequest
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
            'file_id' => [
                'required',
                'exists:files,id', // Validates that the file_id exists in the files table
            ],
            'ministry_id' => [
                'required',
                'exists:ministries,id', // Validates that the ministry_id exists in the ministries table
            ],
            'division_id' => [
                'required',
                'exists:divisions,id', // Validates that the division_id exists in the divisions table
            ],
            'access_type' => [
                'required',
                'in:view,edit,full', // Ensures the access_type is one of the specified options
            ],
            'is_active' => [
                'nullable',
                'boolean', // Ensures is_active is a boolean value
            ],
            'created_by' => [
                'required',
                'exists:users,id', // Validates that the created_by user exists in the users table
            ],
            'updated_by' => [
                'required',
                'exists:users,id', // Validates that the updated_by user exists in the users table
            ],
        ];
    }
}
