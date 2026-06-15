<?php

namespace App\Repositories\National\Eregistry;

use App\Repositories\BaseRepository;
use App\Models\National\Eregistry\Ministry;
use Illuminate\Support\Facades\Auth;

class MinistryRepository extends BaseRepository
{
    protected $auth;

    /**
     * Constructor to inject dependencies
     */
    public function __construct()
    {
        parent::__construct();
        $this->auth = Auth::user();
    }

    /**
     * Specify Model class name
     * 
     * @return string
     */
    public function model()
    {
        return Ministry::class;
    }

    /**
     * Create a new global organisation record
     * 
     * @param array $input
     * @return IdentityOrganisation
     */
    public function create(array $input)
    {
        $data = [
            'name' => $input['name'],
            'code' => $input['code'],
            'description' => $input['description'],
            'is_active' => $input['is_active'] ?? true,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ];

        return $this->model()::create($data);
    }

    /**
     * Update an existing global organisation record
     * 
     * @param Ministry $model
     * @param array $input
     * @return bool
     */
    public function update(Ministry $model, array $input)
    {
        $data = [
            'name' => $input['name'],
            'code' => $input['code'],
            'description' => $input['description'],
            'is_active' => $input['is_active'] ?? $model->is_active,
            'updated_by' => Auth::id(),
        ];

        return $model->update($data);
    }

    /**
     * Get organisations for data table with search and sorting
     * 
     * @param string $search
     * @param string $order_by
     * @param string $sort
     * @param bool $trashed
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getForDataTable($search = '', $order_by = 'id', $sort = 'asc', $trashed = false)
    {
        $query = $this->model()::query()->select(['id', 'name', 'code', 'description', 'is_active']);

        if (!empty($search)) {
            $search = '%' . strtolower($search) . '%';
            $query->where(function ($q) use ($search) {
                $q->whereRaw("LOWER(name) LIKE ?", [$search])
                  ->orWhereRaw("LOWER(code) LIKE ?", [$search])
                  ->orWhereRaw("LOWER(description) LIKE ?", [$search]);
            });
        }

        if ($trashed) {
            $query->onlyTrashed();
        }

        return $query->orderBy($order_by, $sort);
    }

    /**
     * Get a logged in user's organisation
     * 
     * @param string $column
     * @param string $key
     * @return \Illuminate\Support\Collection
     */
    public function pluck($column = 'name', $key = 'id')
    {
        return $this->model()::query()
            ->orderBy($column)
            ->pluck($column, $key, 'code');
    }

    /**
     * Get a full list of ministries for dropdowns
     * 
     * @param string $column
     * @param string $key
     * @return \Illuminate\Support\Collection
     */
    public function list($column = 'name', $key = 'id') //return all organisations with only their id, name, code, location and organisation_type_id
    {
        return $this->model()::query()
            ->orderBy('id')
            ->orderBy($column)
            ->get(['id', 'name', 'code', 'reviewer_title']);
    }

    


}
