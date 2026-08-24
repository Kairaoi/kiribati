<?php

namespace App\Repositories\National\Eregistry;

use App\Models\National\Eregistry\ExternalPartner;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExternalPartnerRepository extends BaseRepository
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
        return ExternalPartner::class;
    }

    /**
     * Create a new global organisation record
     * 
     * @param array $input
     * @return ExternalPartner
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
    public function update(ExternalPartner $model, array $input)
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
    public function getForDataTable($ministryId, $search = '', $order_by = 'id', $sort = 'asc', $trashed = false)
    {
        $query = $this->model()::query()->select(['external_partners.id as id',
                                                 'external_partners.name as name', 
                                                 'organisation_types.name as organisation_type_name',
                                                 'external_partners.email as email',
                                                 'external_partners.phone as phone',
                                                 'external_partners.is_active as is_active',
                                                ])              
                                                ->join('ministries', function ($join) use ($ministryId) {
                                                           $join->on('ministries.id', '=', 'external_partners.ministry_id')
                                                                ->where('external_partners.ministry_id', $ministryId);
                                                })
                                                ->leftJoin('identity_organisations', 'external_partners.identity_organisation_id', '=', 'identity_organisations.id')
                                                ->leftJoin('organisation_types', 'external_partners.organisation_type_id', '=', 'organisation_types.id');                                                

        if (!empty($search)) {
            $search = '%' . strtolower($search) . '%';
            $query->where(function ($q) use ($search) {
                $q->whereRaw("LOWER(external_partners.name) LIKE ?", [$search])
                  ->orWhereRaw("LOWER(external_partners.code) LIKE ?", [$search])
                  ->orWhereRaw("LOWER(external_partners.description) LIKE ?", [$search]);
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
            ->pluck($column, $key);
    }

    /**
     * Get a full list of organisations for dropdowns
     * 
     * @param string $column
     * @param string $key
     * @return \Illuminate\Support\Collection
     */
    public function list($ministryId = null, $column = 'name')
    {
        return $this->model()::query()
            ->where('ministry_id', $ministryId)
            ->where('is_active', "1")
            ->orderBy('id')
            ->orderBy($column)
            ->get(['id', 'name']);
    }



}
