<?php

namespace App\Http\Requests\National\Eregistry;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MovementUpdateRequest extends FormRequest
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
            'file_id' => [
                'required',
                'exists:files,id', // Validates that the file_id exists in the files table
            ],
            'from_ministry_id' => [
                'required',
                'exists:ministries,id', // Validates that the from_ministry_id exists in the ministries table
            ],
            'to_ministry_id' => [
                'required',
                'exists:ministries,id', // Validates that the to_ministry_id exists in the ministries table
            ],
            'from_division_id' => [
                'required',
                'exists:divisions,id', // Validates that the from_division_id exists in the divisions table
            ],
            'to_division_id' => [
                'required',
                'exists:divisions,id', // Validates that the to_division_id exists in the divisions table
            ],
            'from_user_id' => [
                'required',
                'exists:users,id', // Validates that the from_user_id exists in the users table
            ],
            'to_user_id' => [
                'required',
                'exists:users,id', // Validates that the to_user_id exists in the users table
            ],
            'movement_start_date' => [
                'required',
                'date', // Validates that the start date is a valid date
            ],
            'movement_end_date' => [
                'required',
                'date', // Validates that the end date is a valid date
                'after_or_equal:movement_start_date', // Ensures the end date is after or equal to the start date
            ],
            'read_status' => [
                'nullable',
                'boolean', // Ensures read_status is a boolean value
            ],
            'comments' => [
                'nullable',
                'string', // Validates that comments are a string if provided
            ],
            'status' => [
                'required',
                'in:pending,in_progress,completed', // Ensures the status is one of the specified options
            ],
            'created_by' => [
                'required',
                'exists:users,id', // Validates that the created_by user exists in the users table
            ],
            'updated_by' => [
                'required',
                'exists:users,id', // Validates that the updated_by user exists in the users table
            ],
        ];
    }
}
