<?php

namespace App\Http\Requests\National\Eregistry;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OutWardFileStoreRequest extends FormRequest
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
            'folder_id' => [
                'required',
                'exists:folders,id', // Validates that the folder_id exists in the folders table
            ],
            'ministry_id' => [
                'required',
                'exists:ministries,id', // Validates that the ministry_id exists in the ministries table
            ],
            'division_id' => [
                'required',
                'exists:divisions,id', // Validates that the division_id exists in the divisions table
            ],
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'path' => [
                'required',
                'string',
                'max:255',
            ],
            'send_date' => [
                'required',
                'date',
            ],
            'letter_date' => [
                'required',
                'date',
            ],
            'letter_ref_no' => [
                'required',
                'string',
                'max:255',
            ],
            'details' => [
                'nullable',
                'string',
            ],
            'from_details_name' => [
                'required',
                'string',
                'max:255',
            ],
            'to_details_name' => [
                'required',
                'string',
                'max:255',
            ],
            // 'vessel_name' => [
            //     'required',
            //     'string',
            //     'max:255',
            // ],
            'security_level' => [
                'required',
                'in:public,internal,confidential,strictly_confidential',
            ],
            // 'circulation_status' => [
            //     'nullable',
            //     'boolean',
            // ],
            // 'is_active' => [
            //     'nullable',
            //     'boolean',
            // ],
            'file_type_id' => [
                'required',
                'exists:file_types,id', // Validates that the file_type_id exists in the file_types table
            ],
        ];
    }
}
