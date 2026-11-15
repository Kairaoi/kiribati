<?php

namespace App\Repositories\National\Eregistry;

use App\Repositories\BaseRepository;
use App\Models\National\Eregistry\FileType;
use Auth;


class FileTypeRepository extends BaseRepository
{
    /**
     * Specify Model class name
     * 
     * @return string
     */
    public function model()
    {
        return FileType::class;
    }

    /**
     * Create a new file type record
     * 
     * @param array $input
     * @return FileType
     */
    public function createFileType(array $input, $user = null)
    {
        $isSystemAdmin = $user->hasRole('system-admin');

        $data = [
            'name' => $input['name'],
            'description' => $input['description'] ?? null,
            'code' => $input['code'],
            'organisation_id' => $isSystemAdmin ? null : $user->organisation_id,
            'is_global'   => $isSystemAdmin ? 1 : 0,
            'created_by' => $user ? $user->id : null,
            'updated_by' => $user ? $user->id : null,
        ];

        $fileType = $this->model();
        $fileType = new $fileType($data);
        $fileType->save();

        return $fileType;
    }

    /**
     * Update an existing file type record
     * 
     * @param FileType $model
     * @param array $input
     * @return bool
     */
    public function update(FileType $model, array $input, $user = null)
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
     * Get file types for data table with search and sorting
     * 
     * @param string $search
     * @param string $order_by
     * @param string $sort
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getForDataTable($selectedType, int $organisationId, $user, $search = '', $order_by = 'id', $sort = 'desc')
    {
        
        $query = $this->model->query()
            ->select(['file_types.id as id', 
                      'file_types.name as file_type_name', 
                      'file_types.is_global as is_global',
                      'file_types.is_active as is_active', 
                      'file_types.code as code', 
                      'file_types.updated_at as updated_at'])
            ->withCount('files')
            ->leftJoin('organisations', 'file_types.organisation_id', '=', 'organisations.id')
            ->groupBy('file_types.id');

        if ($user->hasRole('system-admin')) {

        } else {
            $query->forType($selectedType, $organisationId);
        }

        if (!empty($search)) {
            $search = '%' . strtolower($search) . '%';

            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(file_types.name) LIKE ?', [$search])
                ->orWhereRaw('LOWER(file_types.code) LIKE ?', [$search])
                ->orWhereRaw('DATE_FORMAT(file_types.updated_at, "%Y-%m-%d") LIKE ?', [str_replace('%', '', $search)]);
            });
        }

        return $query->orderBy($order_by, $sort)->get();
    }

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


    public function listWithDescriptions()
    {
        return $this->model->query()
            ->orderBy('name')
            ->get(['id', 'name', 'description']);
    }


    public function listWithOrganisationTypes($organisationId)
    {
        return $this->model->query()
            ->where(function ($query) use ($organisationId) {
                $query->where('is_global', 1)
                    ->orWhere('organisation_id', $organisationId)
                    ->where('is_active', '1');
            })
            ->orderBy('name')
            ->get(['id', 'name', 'code', 'is_global']);
    }

    public function getFileTypes()
    {
        return $this->model->query()
            ->orderBy('name')
            ->get(['id', 'name', 'is_global']);
    }

}
