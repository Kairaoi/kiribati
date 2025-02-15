<?php

namespace App\Http\Requests\National\Eregistry;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FileStoreRequest extends FormRequest
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
    public function rules()
    {
        return [
            'folder_id' => ['required', 'exists:folders,id'],
            'ministry_id' => ['required', 'exists:ministries,id'],
            'name' => ['required', 'string', 'max:255'],
            'receive_date' => ['required', 'date'],
            'letter_date' => ['required', 'date'],
            'letter_ref_no' => ['required', 'string', 'max:100'],
            'details' => ['nullable', 'string'],
            'from_details_name' => ['required', 'string', 'max:255'],
            'to_details_person_name' => ['required', 'string', 'max:255'],
            'security_level' => ['required', 'in:public,internal,confidential,strictly_confidential'],
            'file_type_id' => ['required', 'exists:file_types,id'],
            'file' => ['required', 'file', 'mimes:pdf,docx,xlsx', 'max:10240'], // 10MB max
        ];
    }
}
