<?php
namespace App\Repositories\National\Eregistry;

use App\Repositories\BaseRepository;
use App\Models\National\Eregistry\Division;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
            'ministry_id' => $input['ministry_id'],
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
    public function getForDataTable($search = '', $order_by = 'id', $sort = 'asc', $ministryId = null, $user = null)
    {

        $query = $this->model->query()
            ->select(['divisions.id as id', 
                      'ministries.code as ministry_code', 
                      'divisions.name as division_name', 
                      'divisions.location as location', 
                      'divisions.is_active as is_active',
                      'divisions.created_at as created_at',
                      DB::raw("CONCAT(users.first_name, ' ', users.last_name) as hod_name"),
                      'divisions.updated_at as updated_at'])
            ->join('ministries', 'divisions.ministry_id', '=', 'ministries.id')
            ->leftJoin('users', 'divisions.hod_id', '=', 'users.id');

            if($user && $user->hasRole('system-admin')) {
                // System Admin can see all divisions, no additional filtering needed
            } else {
                $query->where('divisions.ministry_id', $ministryId); // Filter by ministry
            }   

        // Apply search filter if provided
        if (!empty($search)) {
            $search = '%' . strtolower($search) . '%';
            $query->where(function ($q) use ($search) {
                $q->where('divisions.name', 'ILIKE', $search)
                ->orWhere('divisions.code', 'ILIKE', $search)
                ->orWhere('divisions.description', 'ILIKE', $search);
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


    //Get the list of divisions for a specific ministry
    //used in create function in file controller
    public function listWithMinistry($ministryId)
    {
        return $this->model->query()
            ->select('id', 'name')
            ->where('ministry_id', $ministryId)
            ->orderBy('name')
            ->get();
    }

}
