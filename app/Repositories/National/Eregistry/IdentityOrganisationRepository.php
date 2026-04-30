<?php

namespace App\Repositories\National\Eregistry;

use App\Repositories\BaseRepository;
use App\Models\National\Eregistry\IdentityOrganisation;
use Illuminate\Support\Facades\Auth;

class IdentityOrganisationRepository extends BaseRepository
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
        return IdentityOrganisation::class;
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
     * @param IdentityOrganisation $model
     * @param array $input
     * @return bool
     */
    public function update(IdentityOrganisation $model, array $input)
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
            ->orderBy('id')
            ->orderBy($column)
            ->where('organisation_type_id', '!=', 1)
            ->where('organisation_type_id', '!=', 2)
            ->get(['id', 'name', 'code', 'organisation_type_id']);
    }

    public function listAll($column = 'name', $key = 'id')
    {
        return $this->model()::query()
            ->orderBy('id')
            ->orderBy($column)
            ->get(['id', 'name', 'code', 'organisation_type_id']);
    }


    public function listMinistries($column = 'name', $key = 'id') //Get only ministries
    {
        return $this->model()::query()
            ->where('organisation_type_id', 1) // '1' is the ID for ministries
            ->orderBy($column)
            ->pluck($column, $key);
    }


    //Get only SOEs
    public function listSoes($column = 'name', $key = 'id') 
    {
        return $this->model()::query()
            ->where('organisation_type_id', 2) 
            ->orderBy($column)
            ->pluck($column, $key);
    }


    //Get only Diplomatics Missions
    public function listDiplomatics($column = 'name', $key = 'id')
    {
        return $this->model()::query()
            ->where('organisation_type_id', 3) 
            ->orderBy($column)
            ->pluck($column, $key);
    }


    //Get only Religious Organisations
    public function listReligions($column = 'name', $key = 'id') 
    {
        return $this->model()::query()
            ->where('organisation_type_id', 8)
            ->orderBy($column)
            ->pluck($column, $key);
    }

}
