<?php

namespace App\Http\Requests\National\Eregistry;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Database\Query\Builder;

class UserStoreRequest extends FormRequest
{
    
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $authenticatedUser = auth()->user();

        return $authenticatedUser && $authenticatedUser->hasAnyRole([
                'system-admin',
                'ministry-admin',
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
                'integer',
                'exists:organisations,id',
            ],
            'division_id' => [
                'required',
                'integer',
                Rule::exists('divisions', 'id')
                    ->where(fn (Builder $query) =>
                        $query->where('organisation_id', $this->input('organisation_id'))
                    ),
            ],
            'first_name' => [
                'required',
                'string',
                'max:255',
            ],
            'last_name' => [
                'required',
                'string',
                'max:255',
            ],
            'designation' => [
                'required',
                'string',
                'max:255',
            ],
            'email' => [
                'required',
                'email',
                'unique:users,email',
            ],
            'password' => [
                'required',
                'min:3',
            ],
            'role' => [
                'required',
                Rule::exists('roles', 'id'),
            ],
            
        ];
    }


    public function messages(): array
    {
        return [
            'organisation_id.required' =>
                'Please select an organisation.',

            'organisation_id.exists' =>
                'The selected organisation is invalid.',

            'division_id.required' =>
                'Please select a division.',

            'division_id.exists' =>
                'The selected division does not belong to the selected organisation.',
        ];
    }
}
