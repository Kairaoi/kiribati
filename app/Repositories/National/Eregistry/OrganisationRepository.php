<?php

namespace App\Repositories\National\Eregistry;

use App\Repositories\BaseRepository;
use App\Models\National\Eregistry\Organisation;
use Illuminate\Support\Facades\Auth;

class OrganisationRepository extends BaseRepository
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
        return Organisation::class;
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
            'reviewer_title' => $input['reviewer_title'] ?? null,
            'address' => $input['address'] ?? null,
            'phone' => $input['phone'] ?? null,
            'email' => $input['email'] ?? null,
            'website' => $input['website'] ?? null,
            'po_box' => $input['po_box'] ?? null,
            'organisation_type_id' => $input['organisation_type_id'] ?? null,
            'logo_path' => $input['logo'] ?? null,
            'is_active' => $input['is_active'] ?? true,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ];

        return $this->model()::create($data);
    }


    /**
     * Update an existing global organisation record
     * 
     * @param Organisation $model
     * @param array $input
     * @return bool
     */
    public function update(Organisation $model, array $input)
    {
        $data = [
            'name' => $input['name'] ?? $model->name,
            'code' => $input['code'] ?? $model->code,
            'reviewer_title' => $input['reviewer_title'] ?? $model->reviewer_title,
            'address' => $input['address'] ?? $model->address,
            'phone' => $input['phone'] ?? $model->phone,
            'email' => $input['email'] ?? $model->email,
            'website' => $input['website'] ?? $model->website,
            'po_box' => $input['po_box'] ?? $model->po_box,
            'organisation_type_id' => $input['organisation_type_id'] ?? $model->organisation_type_id,
            'logo_path' => $input['logo'] ?? $model->logo_path,
            'is_active' => $input['is_active'] ?? $model->is_active,
            'updated_by' => Auth::id(),
        ];

        // return $this->model()::update($data);
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
    public function getForDataTable($selectedType = null, $order_by = 'id', $sort = 'asc', $search)
    {
        $query = $this->model()::query()->select(['organisations.name as name',
                                                'organisations.id as id', 
                                                'organisations.uuid as uuid',
                                                'organisations.code as code',
                                                'organisations.is_active as is_active',
                                                'organisation_types.name as type_name',
                                                'organisations.created_at as created_at',
                                                'organisations.updated_at as updated_at'])
                                                ->join('organisation_types', 'organisations.organisation_type_id', '=', 'organisation_types.id');
        
        $query->forSelectedType($selectedType);
                                        
        if (!empty($search)) {
            $search = '%' . strtolower($search) . '%';
            $query->where(function ($q) use ($search) {
                $q->whereRaw("LOWER(organisations.name) LIKE ?", [$search])
                  ->orWhereRaw("LOWER(organisations.code) LIKE ?", [$search])
                  ->orWhereRaw("LOWER(organisations.description) LIKE ?", [$search]);
            });
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


    public function listAll($column = 'name')
    {
        return $this->model()::query()
            ->with('type')
            ->orderBy($column)
            ->get([
                'id',
                'name',
                'code',
                'organisation_type_id',
            ]);
    }

    public function listNotMinistries($column = 'name')
    {
        return $this->model()::query()
            ->with('type')
            ->where('organisation_type_id', '!=', 1)
            ->orderBy($column)
            ->get([
                'id',
                'name',
                'code',
                'organisation_type_id',
            ]);
    }


    public function listMinistries($column = 'name', $key = 'id') //Get only ministries
    {
        return $this->model()::query()
            ->where('organisation_type_id', 1) // '1' is the ID for ministries
            ->orderBy($column)
            ->get([
                'id',
                'name',
                'code',
                'organisation_type_id',
                'reviewer_title',
                'is_active'
            ]);
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

    public function getReviewerTitle($OrganisationId = null) {
        return $this->model()::query()
              ->where('id', $OrganisationId)
              ->first(['reviewer_title']);
    }

    
}
