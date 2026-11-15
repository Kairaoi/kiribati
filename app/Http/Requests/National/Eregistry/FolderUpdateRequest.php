<?php

namespace App\Http\Requests\National\Eregistry;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FolderUpdateRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {

        $authenticatedUser = auth()->user();

        return $authenticatedUser
            && $authenticatedUser->hasAnyRole([
                'system-admin',
                'registry',
            ]);
    }

    
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        return [
            'organisation_id' => [
                'required',
                'exists:organisations,id', // Validates that the organisation_id exists in the organisations table
            ],
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'description' => [
                'nullable',
                'string',
                'max:255',
            ],
            'parent_id' => [
                'nullable',
                'exists:folders,id', // Validates that the parent_id exists in the folders table
            ],

        ];
    }
}
