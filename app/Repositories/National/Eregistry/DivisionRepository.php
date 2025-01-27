<?php
namespace App\Repositories\National\Eregistry;

use App\Repositories\BaseRepository;
use App\Models\National\Eregistry\Division;
use Auth;

class DivisionRepository extends BaseRepository
{
    /**
     * Specify Model class name
     * 
     * @return string
     */
    public function model()
    {
        return Division::class;
    }

    /**
     * Create a new division record
     * 
     * @param array $input
     * @return Division
     */
    public function create(array $input)
    {
        $data = [
            'ministry_id' => $input['ministry_id'],
            'name' => $input['name'],
            'code' => $input['code'],
            'description' => $input['description'] ?? '',
            'is_active' => $input['is_active'] ?? true,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ];

        $division = $this->model();
        $division = new $division($data);
        $division->save();

        return $division;
    }

    /**
     * Update an existing division record
     * 
     * @param Division $model
     * @param array $input
     * @return bool
     */
    public function update(Division $model, array $input)
    {
        $data = [
            'ministry_id' => $input['ministry_id'],
            'name' => $input['name'],
            'code' => $input['code'],
            'description' => $input['description'] ?? $model->description,
            'is_active' => $input['is_active'] ?? $model->is_active,
            'updated_by' => Auth::id(),
        ];

        return $model->update($data);
    }

    /**
     * Get divisions for data table with search and sorting
     * 
     * @param string $search
     * @param string $order_by
     * @param string $sort
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getForDataTable($search = '', $order_by = 'id', $sort = 'asc')
    {
        $query = $this->model->query()
            ->select(['id', 'ministry_id', 'name', 'code', 'description', 'is_active']);

        if (!empty($search)) {
            $search = '%' . strtolower($search) . '%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ILIKE', $search)
                  ->orWhere('code', 'ILIKE', $search)
                  ->orWhere('description', 'ILIKE', $search);
            });
        }

        return $query->orderBy($order_by, $sort);
    }

    /**
     * Get a list of divisions for dropdowns
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
}
