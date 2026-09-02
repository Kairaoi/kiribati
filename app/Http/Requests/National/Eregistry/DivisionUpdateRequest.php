<?php

namespace App\Http\Requests\National\Eregistry;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DivisionUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $authenticatedUser = auth()->user();
        $targetDivision = $this->route('division');

        if (!$authenticatedUser || !$targetDivision) {
            return false;
        }

        if ($authenticatedUser->hasRole('system-admin')) {
            return true;
        }

        //ensure a user does not update a division that does not belong to their ministry
        return $authenticatedUser->hasRole('registry')
            && $authenticatedUser->ministry_id === $targetDivision->ministry_id;
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'ministry_id' => [
                'required',
                'exists:ministries,id', // Validates that the ministry_id exists in the ministries table
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
            'location' => [
                'required',
                'string',
                'max:255',
            ],
            'email' => [
                'nullable',
                'email',
                'max:255',
            ],
            'phone' => [
                'nullable',
                'string',
                'max:10',
            ],
        ];
            
    }
}
