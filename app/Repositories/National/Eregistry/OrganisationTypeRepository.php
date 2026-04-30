<?php

namespace App\Repositories\National\Eregistry;

use App\Models\National\Eregistry\Organisation;
use App\Repositories\BaseRepository;
use App\Models\National\Eregistry\OrganisationType;
use Auth;


class OrganisationTypeRepository extends BaseRepository
{
    /**
     * Specify Model class name
     * 
     * @return string
     */
    public function model()
    {
        return OrganisationType::class;
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
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $organisationType = $this->model();
        $organisationType = new $organisationType($data);
        $organisationType->save();

        return $organisationType;
    }

    /**
     * Update an existing organisation type record
     * 
     * @param OrganisationType $model
     * @param array $input
     * @return bool
     */
    public function update(OrganisationType $model, array $input)
    {
        $data = [
            'name' => $input['name'],
            'description' => $input['description'] ?? $model->description,
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
    public function getForDataTable($search = '', $order_by = 'id', $sort = 'asc')
    {
        $query = $this->model->query()->select(['id', 'name', 'description', 'code', 'created_at', 'updated_at']);

        if (!empty($search)) {
            $search = '%' . strtolower($search) . '%';
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', [$search])
                  ->orWhereRaw('LOWER(description) LIKE ?', [$search])
                  ->orWhereRaw('LOWER(code) LIKE ?', [$search])
                  ->orWhereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") LIKE ?', [str_replace('%', '', $search)]) // Date must match format
                  ->orWhereRaw('CAST(created_by AS CHAR) LIKE ?', [$search]);
            });
        }

        return $query->orderBy($order_by, $sort);
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


    public function list()  
    {
        return $this->model->query()
            ->orderBy('id', 'asc')
            ->where('name', '!=', 'Ministry')
            ->where('name', '!=', 'State Owned Enterprise') 
            ->get(['id', 'name']);

    }

}
