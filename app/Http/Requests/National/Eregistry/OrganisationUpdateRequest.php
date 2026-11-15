<?php

namespace App\Http\Requests\National\Eregistry;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Models\National\Eregistry\Organisation;


class OrganisationUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {

        return auth()->user()->hasRole(['system-admin']);
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'code' => strtoupper(trim((string) $this->code)),
            'name' => trim((string) $this->name),
        ]);
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $organisation = $this->route('organisation');

        // Supports either route-model binding or a direct ID.
        $organisationId = $organisation instanceof Organisation
            ? $organisation->id
            : $organisation;

        return [
            'name' => [
                'required',
                'string',
                function ($value, $fail) use ($organisationId) {
                    $name = trim(strtolower($value));

                    $existsInOrgs = DB::table('organisations')
                        ->whereRaw('LOWER(TRIM(name)) = ?', [$name])
                        ->where('id', '!=', $organisationId)
                        ->exists();

                    if ($existsInOrgs) {
                        $fail('This name already exists as a registered organisation');
                    }
                },
            ],
            'code' => [
                'required',
                'string',
                'min:2',
                'max:5',
                'regex:/^[A-Z]{2,5}$/',
                Rule::unique('organisations', 'code')
                    ->ignore($organisationId),
            ],
            'reviewer_title' => [
                'nullable',
                'string',
                'max:255',
            ],
            'address' => [
                'nullable',
                'string',
                'max:255',
            ],
            'phone' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^\+?[0-9\s\-()]+$/',
            ],
            'email' => [
                'nullable',
                'email',
                'unique:organisations,email,' . $organisationId,
                'max:255',
            ],
            'website' => [
                'nullable',
                'string',
                'max:255',  
            ],
            'po_box' => [
                'nullable',
                'integer',
                'max:255',
            ],
            'organisation_type_id' => [
                'required',
                'exists:organisation_types,id'
            ],
            'logo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
            
           
        ];
    }
}
