<?php

namespace App\Http\Requests\National\Eregistry;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExternalStoreRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {

                    $name = trim(strtolower($value));
                    $organisationId = Auth::user()->organisation_id;

                    // Check External Partners (scoped to organisation)
                    $existsInPartners = DB::table('external_partners')
                        ->whereRaw('LOWER(TRIM(name)) = ?', [$name])
                        ->where('organisation_id', $organisationId)
                        ->exists();

                    // Check Identity Organisations (global)
                    $existsInOrgs = DB::table('organisations')
                        ->whereRaw('LOWER(TRIM(name)) = ?', [$name])
                        ->exists();

                    if ($existsInPartners || $existsInOrgs) {
                        $fail('This name already exists as a registered organisation or external partner.');
                    }
                },
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
                'max:255',
            ],
            'organisation_type_id' => [
                'required',
                'exists:organisation_types,id'
            ],
        ];
    }
}

