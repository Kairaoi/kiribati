<?php
namespace App\Repositories\National\Eregistry;

use App\Models\National\Eregistry\Dispatch;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DispatchRepository extends BaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Dispatch::class;
    }

    /**
     * Create a new Dispatch record
     *
     * @param array $input
     * @return Dispatch
     */
    public function create(array $input)
    {
        $data = [
            'file_id' => $input['file_id'],
            'from_organisation_id' => $input['from_organisation_id'],
            'from_division_id' => $input['from_division_id'],
            'dispatched_by' => Auth::id(),
            // 'from_user_id' => $input['from_user_id'],
            // 'to_user_id' => $input['to_user_id'],
            'dispatch_date' => $input['dispatch_date'],
            'read_status' => $input['read_status'] ?? false,
            // 'comments' => $input['comments'] ?? '',
            // 'status' => $input['status'] ?? 'pending',
            // 'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ];

        $dispatch = $this->model();
        $dispatch = new $dispatch($data);
        $dispatch->save();

        return $dispatch;
    }

    /**
     * Update an existing Dispatch record
     *
     * @param Dispatch $model
     * @param array $input
     * @return bool
     */
    public function update(Dispatch $model, array $input)
    {
        $data = [
            'file_id' => $input['file_id'] ?? $model->file_id,
            'from_organisation_id' => $input['from_organisation_id'] ?? $model->from_organisation_id,
            'to_organisation_id' => $input['to_organisation_id'] ?? $model->to_organisation_id,
            'from_user_id' => $input['from_user_id'] ?? $model->from_user_id,
            'to_user_id' => $input['to_user_id'] ?? $model->to_user_id,
            'dispatch_start_date' => $input['dispatch_start_date'] ?? $model->dispatch_start_date,
            'dispatch_end_date' => $input['dispatch_end_date'] ?? $model->dispatch_end_date,
            'read_status' => $input['read_status'] ?? $model->read_status,
            'comments' => $input['comments'] ?? $model->comments,
            // 'status' => $input['status'] ?? $model->status,
            'updated_by' => Auth::id(),
        ];

        return $model->update($data);
    }

    /**
     * Get Dispatch records for data table with search and sorting
     *
     * @param string $search
     * @param string $order_by
     * @param string $sort
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getForDataTable($search = '', $userOrgId = null, $order_by = 'id', $sort = 'desc', $trashed = false)
    {

        $query = $this->model()::query()
            ->select([
                'dispatches.id', 
                'files.id as file_id',
                'files.subject as file_subject',  // Include file name
                'file_types.name as file_type_name',
                'files.reference_no as reference_no',
                'files.due_date as due_date',
                DB::raw("CONCAT(creators.first_name, ' ', creators.last_name) as file_created_by"),
                'divisions.name as owning_division_name', 
                DB::raw("CONCAT(users.first_name, ' ', users.last_name) as dispatched_by_name"),
                'files.status',
                'dispatches.dispatch_date', 
                
            ])
            ->leftJoin('files', 'files.id', '=', 'dispatches.file_id')
            ->leftJoin('file_types', 'file_types.id', '=', 'files.file_type_id')
            ->leftJoin('divisions', 'divisions.id', '=', 'files.division_id')
            ->leftJoin('users', 'users.id', '=', 'dispatches.dispatched_by')
            ->leftJoin('users as creators', 'creators.id', '=', 'files.created_by') //join users again and use an alias to avoid conflict
            ->leftJoin('organisation_archived_files as archives', function ($join) use ($userOrgId) {
                    $join->on('archives.file_id', '=', 'dispatches.file_id')
                        ->where('archives.organisation_id', $userOrgId);
                })
            ->whereNull('archives.file_id')
            ->where('dispatches.from_organisation_id', $userOrgId)
            ->whereIn('files.status', ['Pending Dispatch', 'Dispatched']); // Filter by file status

        if (!empty($search)) {
            $search = '%' . strtolower($search) . '%';

            $query->where(function ($q) use ($search) {
                $q->whereRaw("LOWER(files.name) LIKE ?", [$search]) // Allow searching by file name
                ->orWhereRaw("LOWER(divisions.name) LIKE ?", [$search])
                ->orWhereRaw("LOWER(file_types.name) LIKE ?", [$search])
                ->orWhereRaw("LOWER(files.status) LIKE ?", [$search])
                ->orWhereRaw("LOWER(dispatches.dispatch_date) LIKE ?", [$search])
                ->orWhereRaw("LOWER(users.first_name) LIKE ?", [$search])
                ->orWhereRaw("LOWER(CONCAT(creators.first_name, ' ', creators.last_name)) LIKE ?", [$search]);
            });
        }

        if ($trashed) {
            $query->onlyTrashed();
        }

        return $query->orderBy($order_by, $sort);
    }

    //for dispatches user index
    public function getForUserDataTable($search = '', $order_by = 'id', $sort = 'asc', $trashed = false)
    {
        $query = $this->model()::query()
            ->select([
                'dispatches.id', 
                'files.name as file_name',  // Include file name
                'file_types.name as file_type_name',
                DB::raw("CONCAT(creators.first_name, ' ', creators.last_name) as file_created_by"),
                // DB::raw("CONCAT(users.first_name, ' ', users.last_name) as created_by_name"),
                // 'files.created_by as file_created_by',
                // 'organisations.code as owning_organisation_code', 
                'divisions.name as owning_division_name', 
                DB::raw("CONCAT(users.first_name, ' ', users.last_name) as dispatched_by_name"),
                'files.status',
                'dispatches.dispatch_date', 
                
            ])
            ->leftJoin('files', 'files.id', '=', 'dispatches.file_id')
            ->leftJoin('file_types', 'file_types.id', '=', 'files.file_type_id')
            ->leftJoin('organisations', 'organisations.id', '=', 'files.organisation_id')
            ->leftJoin('divisions', 'divisions.id', '=', 'files.division_id')
            ->leftJoin('users', 'users.id', '=', 'dispatches.dispatched_by')
            ->leftJoin('users as creators', 'creators.id', '=', 'files.created_by') //join users again and use an alias to avoid conflict
            ->where('files.created_by', Auth::user()->id)
            ->whereIn('files.status', ['Pending Dispatch', 'Dispatched']); // Filter by file status

        if (!empty($search)) {
            $search = '%' . strtolower($search) . '%';
            $query->where(function ($q) use ($search) {
                $q->whereRaw("LOWER(files.name) LIKE ?", [$search]); // Allow searching by file name
                // ->orWhereRaw("LOWER(organisations_from.name) LIKE ?", [$search])
                // ->orWhereRaw("LOWER(organisations_to.name) LIKE ?", [$search])
                // ->orWhereRaw("LOWER(dispatches.status) LIKE ?", [$search]);
            });
        }

        if ($trashed) {
            $query->onlyTrashed();
        }

        return $query->orderBy($order_by, $sort);
    }

    /**
     * Get a list of Dispatchs for dropdowns
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


    public function getForDispatchType(int $userMinistryId, array $filterOrgIds = [], $fromDate = null, $toDate = null)
    {
        return $this->model->query()
            ->ofType($userMinistryId, $filterOrgIds)
            ->betweenDates($fromDate, $toDate);
    } 
}
