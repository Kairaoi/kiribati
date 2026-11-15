<?php

namespace App\Http\Requests\National\Eregistry;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Database\Query\Builder;
use App\Models\User;

class UserUpdateRequest extends FormRequest
{

    // protected function prepareForValidation(): void
    // {
    //     dd($this->all());
    // }

    
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $authenticatedUser = auth()->user();
        $targetUser = $this->route('user');

        if (!$authenticatedUser || !$targetUser) {
            return false;
        }

        if ($authenticatedUser->hasRole('system-admin')) {
            return true;
        }

        return $authenticatedUser->hasRole('admin')
            && $authenticatedUser->organisation_id === $targetUser->organisation_id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->route('user');

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
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'role' => [
                'required',
                Rule::exists('roles', 'id'),
            ],
            
        ];
    }


    // public function messages(): array
    // {
    //     return [
    //         'organisation_id.required' =>
    //             'Please select an organisation.',

    //         'organisation_id.exists' =>
    //             'The selected organisation is invalid.',

    //         'division_id.required' =>
    //             'Please select a division.',

    //         'division_id.exists' =>
    //             'The selected division does not belong to the selected organisation.',
    //     ];
    // }
}
