<?php

namespace App\Http\Requests\National\Eregistry;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Models\National\Eregistry\IdentityOrganisation;


class IdentityOrganisationUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {

        return auth()->user()->hasRole('system-admin');
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
        $organisationId = $organisation instanceof IdentityOrganisation
            ? $organisation->id
            : $organisation;

        return [
            'name' => [
                'required',
                'string',
                function ($attribute, $value, $fail) use ($organisationId) {
                    $name = trim(strtolower($value));

                    $existsInOrgs = DB::table('identity_organisations')
                        ->whereRaw('LOWER(TRIM(name)) = ?', [$name])
                        ->where('id', '!=', $organisationId)
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
                Rule::unique('identity_organisations', 'code')
                    ->ignore($organisationId),
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
