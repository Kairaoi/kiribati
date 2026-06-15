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
    public function create(array $input)
    {
        $data = [
            'name' => $input['name'],
            'description' => $input['description'] ?? null,
            'code' => $input['code'],
            'created_at' => now(),
            'updated_at' => now(),
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
    public function update(FileType $model, array $input)
    {

        $data = [
            'name' => $input['name'],
            'description' => $input['description'] ?? $model->description,
            'code' => $input['code'],
            'created_at' => now(),
            'updated_at' => now(),
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
    public function getForDataTable($selectedType, int $ministryId, $user, $search = '', $order_by = 'id', $sort = 'desc')
    {
        
        $query = $this->model->query()
            ->select(['file_types.id as id', 
                      'file_types.name as file_type_name', 
                      'file_types.is_global as is_global', 
                      'file_types.code as code', 
                      'file_types.created_at as created_at', 
                      'file_types.updated_at as updated_at'])
            ->leftJoin('ministries', 'file_types.ministry_id', '=', 'ministries.id')
            ->groupBy('file_types.id');

            //scope in Model

            if ($user->hasRole('system-admin')) {

            } else {
                $query->forType($selectedType, $ministryId);
            }

        if (!empty($search)) {
            $search = '%' . strtolower($search) . '%';

            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', [$search])
                ->orWhereRaw('LOWER(description) LIKE ?', [$search])
                ->orWhereRaw('LOWER(code) LIKE ?', [$search])
                ->orWhereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") LIKE ?', [str_replace('%', '', $search)])
                ->orWhereRaw('CAST(created_by AS CHAR) LIKE ?', [$search]);
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


    public function listWithMinistryTypes($ministryId)
    {
        return $this->model->query()
            ->where(function ($query) use ($ministryId) {
                $query->where('is_global', 1)
                    ->orWhere('ministry_id', $ministryId);
            })
            ->orderBy('name')
            ->get(['id', 'name', 'is_global']);
    }

    public function getFileTypes()
    {
        return $this->model->query()
            ->orderBy('name')
            ->get(['id', 'name', 'is_global']);
    }

}
