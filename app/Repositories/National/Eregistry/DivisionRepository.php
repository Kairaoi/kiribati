<?php
namespace App\Repositories\National\Eregistry;

use App\Repositories\BaseRepository;
use App\Models\National\Eregistry\Division;
use Illuminate\Support\Facades\Auth;

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
     * Create a new Division record
     * 
     * @param array $input
     * @return Division
     */
    public function create(array $input)
    {
        $data = [
            'organisation_id' => $input['organisation_id'],
            'name' => $input['name'],
            'is_active' => $input['is_active'] ?? true,
            'created_at' => Auth::id(),
            'updated_at' => Auth::id(),
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
            'organisation_id' => $input['organisation_id'],
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
    public function getForDataTable($search = '', $order_by = 'id', $sort = 'asc', $organisationId = null)
    {
        $query = $this->model->query()
            ->select(['id', 'organisation_id', 'name', 'location', 'is_active']);

        // Apply organisation filter if provided
        if (!is_null($organisationId)) {
            $query->where('organisation_id', $organisationId);
        }

        // Apply search filter if provided
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

    
    /**
     * Get a full list of organisations for dropdowns
     * 
     * @param string $column
     * @param string $key
     * @return \Illuminate\Support\Collection
     */
    public function list($column = 'name', $key = 'id')
    {
        return $this->model()::query()
            ->orderBy($column)
            ->get();
    }


    //Get the list of divisions for a specific organisation
    //used in create function in file controller
    public function listWithOrganisation($organisationId)
    {
        return $this->model->query()
            ->with('organisation') // Eager load the organisation relationship
            ->where('organisation_id', $organisationId)
            ->orderBy('name')
            ->get();
    }

}
