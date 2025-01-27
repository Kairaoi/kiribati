<?php
namespace App\Repositories\National\Eregistry;

use App\Repositories\BaseRepository;
use App\Models\National\Eregistry\Movement;
use Auth;

class MovementRepository extends BaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Movement::class;
    }

    /**
     * Create a new Movement record
     *
     * @param array $input
     * @return Movement
     */
    public function create(array $input)
    {
        $data = [
            'file_id' => $input['file_id'],
            'from_ministry_id' => $input['from_ministry_id'],
            'to_ministry_id' => $input['to_ministry_id'],
            'from_division_id' => $input['from_division_id'],
            'to_division_id' => $input['to_division_id'],
            'from_user_id' => $input['from_user_id'],
            'to_user_id' => $input['to_user_id'],
            'movement_start_date' => $input['movement_start_date'],
            'movement_end_date' => $input['movement_end_date'] ?? null,
            'read_status' => $input['read_status'] ?? false,
            'comments' => $input['comments'] ?? '',
            'status' => $input['status'] ?? 'pending',
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ];

        $movement = $this->model();
        $movement = new $movement($data);
        $movement->save();

        return $movement;
    }

    /**
     * Update an existing Movement record
     *
     * @param Movement $model
     * @param array $input
     * @return bool
     */
    public function update(Movement $model, array $input)
    {
        $data = [
            'file_id' => $input['file_id'] ?? $model->file_id,
            'from_ministry_id' => $input['from_ministry_id'] ?? $model->from_ministry_id,
            'to_ministry_id' => $input['to_ministry_id'] ?? $model->to_ministry_id,
            'from_division_id' => $input['from_division_id'] ?? $model->from_division_id,
            'to_division_id' => $input['to_division_id'] ?? $model->to_division_id,
            'from_user_id' => $input['from_user_id'] ?? $model->from_user_id,
            'to_user_id' => $input['to_user_id'] ?? $model->to_user_id,
            'movement_start_date' => $input['movement_start_date'] ?? $model->movement_start_date,
            'movement_end_date' => $input['movement_end_date'] ?? $model->movement_end_date,
            'read_status' => $input['read_status'] ?? $model->read_status,
            'comments' => $input['comments'] ?? $model->comments,
            'status' => $input['status'] ?? $model->status,
            'updated_by' => Auth::id(),
        ];

        return $model->update($data);
    }

    /**
     * Get Movement records for data table with search and sorting
     *
     * @param string $search
     * @param string $order_by
     * @param string $sort
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getForDataTable($search = '', $order_by = 'id', $sort = 'asc')
    {
        $query = $this->model->query()
            ->select([
                'id',
                'file_id',
                'from_ministry_id',
                'to_ministry_id',
                'from_division_id',
                'to_division_id',
                'from_user_id',
                'to_user_id',
                'movement_start_date',
                'movement_end_date',
                'status',
            ]);

        if (!empty($search)) {
            $search = '%' . strtolower($search) . '%';
            $query->where(function ($q) use ($search) {
                $q->where('comments', 'ILIKE', $search)
                  ->orWhere('status', 'ILIKE', $search);
            });
        }

        return $query->orderBy($order_by, $sort);
    }

    /**
     * Get a list of Movements for dropdowns
     *
     * @param string $column
     * @param string $key
     * @return \Illuminate\Support\Collection
     */
    public function pluck($column = 'status', $key = 'id')
    {
        return $this->model->query()
            ->orderBy($column)
            ->pluck($column, $key);
    }
}
