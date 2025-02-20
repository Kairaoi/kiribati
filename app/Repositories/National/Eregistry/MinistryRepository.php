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
     * Create a new ministry record
     * 
     * @param array $input
     * @return Ministry
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
     * Update an existing ministry record
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
     * Get ministries for data table with search and sorting
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
     * Get a list of ministries for dropdowns filtered by the authenticated user's ministry
     * 
     * @param string $column
     * @param string $key
     * @return \Illuminate\Support\Collection
     */
    public function pluck($column = 'name', $key = 'id')
    {
        return $this->model()::query()
            ->where('id', Auth::user()->ministry_id) // Ensure this is the correct filtering column
            ->orderBy($column)
            ->pluck($column, $key);
    }

    /**
     * Get a full list of ministries for dropdowns
     * 
     * @param string $column
     * @param string $key
     * @return \Illuminate\Support\Collection
     */
    public function list($column = 'name', $key = 'id')
    {
        return $this->model()::query()
            ->orderBy($column)
            ->pluck($column, $key);
    }
}
