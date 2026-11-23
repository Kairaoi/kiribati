<?php

namespace App\Http\Requests\National\Eregistry;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FileUpdateRequest extends FormRequest
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
    public function rules(): array
    {
        $file = $this->route('file');
        $documentSource = $this->input('document_source') ?? $file->document_source;

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
                'required_unless:source_type,external_partner,identity_organisation',
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
                'max:5'
            ],
            'additional_files.*' => [
                'file',
                'mimes:pdf,xls,xlsx,png,jpg,jpeg,doc,docx,ppt,pptx',
                'max:30720'
            ],
            'remove_additional_files' => [
                'sometimes', 
                'array'
            ],
            'remove_additional_files.*' => [
                'string'],

            'main_file' => [
                'exclude_unless:document_source,upload',
                Rule::requiredIf(
                    $documentSource === 'upload' && blank($file->main_file_path)
                ),
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png,gif,webp,tif,tiff',
                'max:30720'
            ],
            // 'correspondence_type' => [
            //     'exclude_unless:document_source,online',
            //     'required',
            //     'in:letter,internal,memo'
            // ],

            // 'memo_recipients' => [
            //     'exclude_unless:correspondence_type,memo',
            //     'required',
            //     'array',
            //     'min:1',
            // ],

            // 'memo_recipients.*' => [
            //     'integer',
            //     'exists:organisations,id',
            // ],

            // 'registered_organisations' => [
            //     'exclude_unless:correspondence_type,letter',
            //     'required_without:external_partners',
            //     'array',
            //     'min:1',
            // ],

            // 'registered_organisations.*' => [
            //     'integer',
            //     'distinct',
            //     'exists:organisations,id',
            // ],

            // 'external_partners' => [
            //     'exclude_unless:correspondence_type,letter',
            //     'required_without:registered_organisations',
            //     'array',
            //     'min:1',
            // ],

            // 'external_partners.*' => [
            //     'integer',
            //     'distinct',
            //     'exists:external_partners,id',
            // ],

            // 'memo_from_field' => [
            //     'exclude_unless:correspondence_type,memo',
            //      'exclude_unless:document_source,online',
            //     'required',
            //     'string',
            //     'max:255',
            // ],

            // 'memo_cc_field' => [
            //     'exclude_unless:correspondence_type,memo',
            //     'exclude_unless:document_source,online',
            //     'nullable',
            //     'string',
            //     'max:255',
            // ],
            // 'memo_attention_to' => [
            //     'exclude_unless:correspondence_type,memo',
            //     'exclude_unless:document_source,online',
            //     'nullable',
            //     'string',
            //     'max:255',
            // ],
            // 'internal_from_field' => [
            //     'exclude_unless:correspondence_type,internal',
            //     'exclude_unless:document_source,online',
            //     'required',
            //     'string',
            //     'max:255',
            // ],
            // 'internal_to_field' => [
            //     'exclude_unless:correspondence_type,internal',
            //     'exclude_unless:document_source,online',
            //     'required',
            //     'string',
            //     'max:255',
            // ],
            // 'internal_cc_field' => [
            //     'exclude_unless:correspondence_type,internal',
            //     'exclude_unless:document_source,online',
            //     'nullable',
            //     'string',
            //     'max:255',
            // ],
            // 'internal_ufs_id' => [
            //     'exclude_unless:correspondence_type,internal',
            //     'exclude_unless:document_source,online',
            //     'required',
            //     'exists:users,id',
            // ],
            // 'content' => [
            //     'nullable',
            //     'exclude_unless:document_source,online',
            //     'required_without:main_file',
            //     'string',
            // ],
           
        ];
    
    }
}
