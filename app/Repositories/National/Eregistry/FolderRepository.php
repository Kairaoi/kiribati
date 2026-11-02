<?php

namespace App\Repositories\National\Eregistry;

use App\Repositories\BaseRepository;
use App\Models\National\Eregistry\Folder;
use Auth;


class FolderRepository extends BaseRepository
{
    /**
     * Specify Model class name
     * 
     * @return string
     */
    public function model()
    {
        return Folder::class;
    }

    /**
     * Create a new folder record
     * 
     * @param array $input
     * @return Folder
     */
    public function create(array $input)
    {

        $data = [
            'organisation_id' => $input['ministry_id'],
            'name' => $input['name'],
            'description' => $input['description'] ?? null,
            'parent_id' => $input['parent_id'] ?? null,
            'is_active' => $input['is_active'] ?? true,
            'created_at' => now(),
            'updated_at' => now(),
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ];

        $folder = $this->model();
        $folder = new $folder($data);
        $folder->save();

        return $folder;
    }


    /**
     * Update an existing folder record
     * 
     * @param Folder $model
     * @param array $input
     * @return bool
     */
    public function update(Folder $model, array $input, $user = null)
    {
        
        $data = [
            'name' => $input['name'],
            'description' => $input['description'] ?? $model->description,
            'code' => $input['code'],
            'ministry_id' => $input['ministry_id'] ?? $model->ministry_id,
            'is_global' => $input['is_global'] ?? $model->is_global,
            'updated_at' => now(),
            'updated_by' => $user->id,
        ];

        return $model->update($data);
    }
    

    /**
     * Get folders for data table with search and sorting
     * 
     * @param string $search
     * @param string $order_by
     * @param string $sort
     * @return \Illuminate\Database\Eloquent\Builder
     */
    // public function getForDataTable($selectedType, int $ministryId, $user, $search = '', $order_by = 'id', $sort = 'desc')
    // {
        
    //     $query = $this->model->query()
    //         ->select(['folders.id as id', 
    //                   'folders.name as folder_name', 
    //                   'folders.is_global as is_global',
    //                   'folders.is_active as is_active', 
    //                   'folders.code as code', 
    //                   'folders.created_at as created_at', 
    //                   'folders.updated_at as updated_at'])
    //         ->withCount('files')
    //         ->leftJoin('ministries', 'folders.ministry_id', '=', 'ministries.id')
    //         ->groupBy('folders.id');

    //         //scope in Model

    //         if ($user->hasRole('system-admin')) {

    //         } else {
    //             $query->forType($selectedType, $ministryId);
    //         }

    //     if (!empty($search)) {
    //         $search = '%' . strtolower($search) . '%';

    //         $query->where(function ($q) use ($search) {
    //             $q->whereRaw('LOWER(name) LIKE ?', [$search])
    //             ->orWhereRaw('LOWER(description) LIKE ?', [$search])
    //             ->orWhereRaw('LOWER(code) LIKE ?', [$search])
    //             ->orWhereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") LIKE ?', [str_replace('%', '', $search)])
    //             ->orWhereRaw('CAST(created_by AS CHAR) LIKE ?', [$search]);
    //         });
    //     }

    //     return $query->orderBy($order_by, $sort)->get();
    // }



    /**
     * Get a list of file types for dropdowns
     * 
     * @param string $column
     * @param string $key
     * @return \Illuminate\Support\Collection
     */
    public function pluck($column = 'name', $key = 'id')
    {
        return $this->model->query()
            ->orderBy($column)
            ->pluck($column, $key);
    }


    public function listWithOrganisationTypes($organisationId = null)
    {
        return $this->model->query()
            ->where(function ($query) use ($organisationId) {
                $query->where('is_global', 1)
                    ->orWhere('organisation_id', $organisationId)
                    ->where('is_active', '1');
            })
            ->orderBy('name')
            ->get(['id', 'name']);
    }


    public function getTopFolders($organisationId = null)
    {
        return $this->model->query()
            ->orderBy('name')
            ->where('organisation_id', $organisationId)
            ->where('is_active', true)
            ->whereNull('parent_id')
            ->orderBy('name');           
    }

}
