<?php

namespace App\Repositories\National\Eregistry;

use App\Models\National\Eregistry\File;
use App\Models\National\Eregistry\FileCirculation;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FileCirculationRepository extends BaseRepository
{
    /**
     * Specify Model class name
     */
    public function model()
    {
        return FileCirculation::class;
    }

    /**
     * Create a new file record
     */
    public function create(array $input)
    {
        $data = [
            'file_id' => $input['file_id'],
            'organisation_id' => $input['organisation_id'],
            'circulated_by' => Auth::id(),
            'circulated_at' => now(),
            'to_review_file' => $input['to_review_file'] ?? null,
            'assigned_officer' => $input['assigned_officer'] ?? null,
            'read_at' => null,
            'read_status' => false,
            'requires_action' => $input['requires_action'] ?? false,
            'action_taken' => $input['action_taken'] ?? null,
            'updated_by' => Auth::id(),
            'assigned_division_id' => $input['assigned_division_id'] ?? null,
        ];

        $file = new File($data);
        $file->save();

        // Update file_index after ID is available
        // $file->update(['file_index' => "{$file->organisation_id}/{$file->id}"]);

        return $file;
    }

    /**
     * Update a file record
     */
    public function update(File $model, array $input)
    {
        $data = array_merge($model->toArray(), [
            'file_id' => $input['file_id'],
            'organisation_id' => $input['organisation_id'],
            'circulated_by' => Auth::id(),
            'circulated_at' => now(),
            'to_review_file' => $input['to_review_file'] ?? null,
            'assigned_officer' => $input['assigned_officer'] ?? null,
            'read_at' => null,
            'read_status' => false,
            'requires_action' => $input['requires_action'] ?? false,
            'action_taken' => $input['action_taken'] ?? null,
            'updated_by' => Auth::id(),
            'assigned_division_id' => $input['assigned_division_id'] ?? null,

        ]);

        return $model->update($data);
    }

    /**
     * Get a list of files for dropdowns
     */
    public function pluck($column = 'name', $key = 'id')
    {
        $ministryId = Auth::user()->ministry_id;
    
        return $this->model()::query()
                ->where('to_ministry_id', $ministryId)
                ->where('is_active', true)
                ->orderBy($column)
                ->pluck($column, $key);
    }


    public function list($column = 'name', $key = 'id')
    {
        return $this->model->query()
            ->orderBy($column)
            ->pluck($column, $key);
    }    

    
    public function ministryCirculations($fileId, $ministryId)
    {
        return $this->model()::query()
            ->where('file_id', $fileId)
            ->where('to_ministry_id', '!=', $ministryId);
    }

    public function thisCirculation($fileId, $ministryId)
    {
        return $this->model()::query()
            ->where('file_id', $fileId)
            ->where('to_ministry_id', $ministryId)
            ->latest();
    }
    

}



