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
            'organisation_id' => $input['organisation_id'],
            'name' => $input['name'],
            'description' => $input['description'] ?? null,
            'location' => $input['location'] ?? null,
            'is_active' => $input['is_active'] ?? true,
            'created_at' => now(),
            'updated_at' => now(),
            'email' => $input['email'] ?? null,
            'phone' => $input['phone'] ?? null,
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
            'organisation_id' => $input['organisation_id'],
            'name' => $input['name'],
            'description' => $input['description'] ?? null,
            'location' => $input['location'] ?? null,
            'email' => $input['email'] ?? null,
            'phone' => $input['phone'] ?? null,
            'is_active' => $input['is_active'] ?? true,
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
                      'divisions.name as division_name', 
                      'divisions.location as location', 
                      'divisions.is_active as is_active',
                      'organisations.name as org.name',
                      'divisions.created_at as created_at',
                      DB::raw("CONCAT(users.first_name, ' ', users.last_name) as hod_name"),
                      'divisions.updated_at as updated_at'])

            ->join('organisations', 'divisions.organisation_id', '=', 'organisations.id')
            ->leftJoin('users', 'divisions.hod_id', '=', 'users.id');

            if($user && $user->hasRole('system-admin')) {
                // System Admin can see all divisions, no additional filtering needed
            } else {
                $query->where('divisions.organisation_id', $ministryId); // Filter by organisation
            }   

    
        if (!empty(trim($search))) {
            $search = '%' . strtolower(trim($search)) . '%';

            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(divisions.name) LIKE ?', [$search])
                    ->orWhereRaw(
                        "LOWER(CONCAT(users.first_name, ' ', users.last_name)) LIKE ?",
                        [$search]
                    );
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
            ->get(['id', 'name']);
    }


    //Get the list of divisions for a specific organisation
    //used in create function in file controller
    public function listWithOrganisation(?int $organisationId = null)
    {
        return $this->model()::query()
            ->select([
                'id',
                'organisation_id',
                'name',
            ])
            ->when(
                $organisationId !== null,
                fn ($query) => $query->where(
                    'organisation_id',
                    $organisationId
                )
            )
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

}
