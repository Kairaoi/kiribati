<?php

namespace App\Repositories\National\Eregistry;

use App\Repositories\BaseRepository;
use App\Models\National\Eregistry\OutWardFile;
use Illuminate\Support\Facades\Auth;

class OutWardFileRepository extends BaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return OutWardFile::class;
    }

    /**
     * Create a new OutWardFile record
     *
     * @param array $input
     * @return OutWardFile
     */
    public function create(array $input)
    {
        $data = [
            // 'folder_id' => $input['folder_id'],
            'ministry_id' => $input['ministry_id'],
            'division_id' => $input['division_id'],
            'name' => $input['name'],
            'path' => $input['path'],
            'send_date' => $input['send_date'],
            'letter_date' => $input['letter_date'],
            'letter_ref_no' => $input['letter_ref_no'],
            'details' => $input['details'] ?? '',
            'from_details_name' => $input['from_details_name'],
            'to_details_name' => $input['to_details_name'],
            // 'vessel_name' => $input['vessel_name'],
            'security_level' => $input['security_level'] ?? 'public',
            'file_type_id' => $input['file_type_id'],
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ];

        $outWardFile = $this->model();
        $outWardFile = new $outWardFile($data);
        $outWardFile->save();

        return $outWardFile;
    }

    /**
     * Update an existing OutWardFile record
     *
     * @param OutWardFile $model
     * @param array $input
     * @return bool
     */
    public function update(OutWardFile $model, array $input)
    {
        $data = [
            // 'folder_id' => $input['folder_id'] ?? $model->folder_id,
            'ministry_id' => $input['ministry_id'] ?? $model->ministry_id,
            'division_id' => $input['division_id'] ?? $model->division_id,
            'name' => $input['name'] ?? $model->name,
            'path' => $input['path'] ?? $model->path,
            'send_date' => $input['send_date'] ?? $model->send_date,
            'letter_date' => $input['letter_date'] ?? $model->letter_date,
            'letter_ref_no' => $input['letter_ref_no'] ?? $model->letter_ref_no,
            'details' => $input['details'] ?? $model->details,
            'from_details_name' => $input['from_details_name'] ?? $model->from_details_name,
            'to_details_name' => $input['to_details_name'] ?? $model->to_details_name,
            // 'vessel_name' => $input['vessel_name'] ?? $model->vessel_name,
            'security_level' => $input['security_level'] ?? $model->security_level,
            'file_type_id' => $input['file_type_id'] ?? $model->file_type_id,
            'updated_by' => Auth::id(),
        ];

        return $model->update($data);
    }

    /**
     * Get OutWardFiles for data table with search and sorting
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
                // 'folder_id',
                'ministry_id',
                'division_id',
                'name',
                'path',
                'send_date',
                'letter_date',
                'letter_ref_no',
                'security_level',
            ]);

        if (!empty($search)) {
            $search = '%' . strtolower($search) . '%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ILIKE', $search)
                  ->orWhere('letter_ref_no', 'ILIKE', $search);

            });
        }

        // dd($query->toSql(), $query->get());

        return $query->orderBy($order_by, $sort);
    }

    /**
     * Get a list of OutWardFiles for dropdowns
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
}
