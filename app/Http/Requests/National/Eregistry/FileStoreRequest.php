<?php

namespace App\Http\Requests\National\Eregistry;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FileStoreRequest extends FormRequest
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
            'source_type' => [
                'required',
                Rule::in([
                    'identity_organisation',
                    'external_partner',
                    'own_ministry',
                ]),
            ],       
            'source_id' => [
                'required_unless:source_type,own_ministry',
                'nullable',
                'integer',
            ],     
            'document_source' => [
                'required',
                Rule::in(
                    request('source_type') === 'own_ministry'
                        ? ['upload', 'online']
                        : ['upload']
                ),
            ],
            'from_division_id' => [
                'required_unless:source_type,external_partner',
                'nullable',
                'exists:divisions,id'
            ],
            'subject' => [
                'required',
                'string', 
                'max:255'
            ],
            'file_type_id' => [
                'required', 
                'exists:file_types,id'
            ],
            'category_id' => [
                'nullable',
                'exists:categories,id'
            ],
            'due_date' => [
                'nullable',
                'date'
            ],
            'additional_files' => [
                'nullable',
                'array',
                'max:3'
            ],
            'additional_files.*' => [
                'file',
                'mimes:pdf,xls,xlsx,png,jpg,jpeg,doc,docx,ppt,pptx',
                'max:10240'
            ],
            'main_file' => [
                'exclude_unless:document_source,upload',
                'required_if:document_source,upload',
                'file',
                'mimes:pdf,jpg,jpeg,png,gif,webp,tif,tiff',
                'max:10240'
            ],
            'correspondence_type' => [
                'exclude_unless:document_source,online',
                'required',
                'in:letter,internal,memo'
            ],

            'memo_recipients' => [
                'exclude_unless:correspondence_type,memo',
                'required',
                'array',
                'min:1',
            ],

            'memo_recipients.*' => [
                'integer',
                'exists:organisations,id',
            ],

            'registered_organisations' => [
                'exclude_unless:correspondence_type,letter',
                'required_without:external_partners',
                'array',
                'min:1',
            ],

            'registered_organisations.*' => [
                'integer',
                'distinct',
                'exists:organisations,id',
            ],

            'external_partners' => [
                'exclude_unless:correspondence_type,letter',
                'required_without:registered_organisations',
                'array',
                'min:1',
            ],

            'external_partners.*' => [
                'integer',
                'distinct',
                'exists:external_partners,id',
            ],

            'memo_from_field' => [
                'exclude_unless:correspondence_type,memo',
                 'exclude_unless:document_source,online',
                'required',
                'string',
                'max:255',
            ],

            'memo_cc_field' => [
                'exclude_unless:correspondence_type,memo',
                'exclude_unless:document_source,online',
                'nullable',
                'string',
                'max:255',
            ],
            'memo_attention_to' => [
                'exclude_unless:correspondence_type,memo',
                'exclude_unless:document_source,online',
                'nullable',
                'string',
                'max:255',
            ],
            'internal_from_field' => [
                'exclude_unless:correspondence_type,internal',
                'exclude_unless:document_source,online',
                'required',
                'string',
                'max:255',
            ],
            'internal_to_field' => [
                'exclude_unless:correspondence_type,internal',
                'exclude_unless:document_source,online',
                'required',
                'string',
                'max:255',
            ],
            'internal_cc_field' => [
                'exclude_unless:correspondence_type,internal',
                'exclude_unless:document_source,online',
                'nullable',
                'string',
                'max:255',
            ],
            'internal_ufs_id' => [
                'exclude_unless:correspondence_type,internal',
                'exclude_unless:document_source,online',
                'required',
                'exists:users,id',
            ],
            'content' => [
                'nullable',
                'exclude_unless:document_source,online',
                'required_without:main_file',
                'string',
            ],
           
        ];
    
    
    }

    public function messages()
    {
        return [
            'source_type.required' => 'The source type selected is required.',
            'source_type.in' => 'The selected source type is invalid.',
            'source_id.required_unless' => 'The source field is required unless the source type is "own_ministry".',
            'source_id.integer' => 'The source field must be an integer.',
            'document_source.required' => 'The document source field is required.',
            'document_source.in' => 'The selected document source is invalid.',
            'division.required_if' => 'A division is required when the source type is your own organisation.',
            'division.exists' => 'The selected division is invalid.',
        ];
    }
}
