<?php

namespace App\Http\Requests\National\Eregistry;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class OrganisationStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->hasRole('system-admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    $name = trim(strtolower($value));

                    // Check Identity Organisations (global)
                    $existsInOrgs = DB::table('organisations')
                        ->whereRaw('LOWER(TRIM(name)) = ?', [$name])
                        ->exists();

                    if ($existsInOrgs) {
                        $fail('This name already exists as a registered organisation or external partner.');
                    }
                },
            ],
            'code' => [
                'required',
                'string',
                'min:2',
                'max:5',
                'regex:/^[A-Z]{2,5}$/',
                Rule::unique('organisations', 'code'),                   
            ],
            'description' => [
                'nullable',
                'string',
                'max:255',
            ],
            'address' => [
                'required',
                'string',
                'max:255',
            ],
            'organisation_type_id' => [
                'required',
                'exists:organisation_types,id'
            ],
        ];
    }
}
