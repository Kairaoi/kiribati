<?php

namespace App\Repositories\National\Eregistry;

use App\Models\National\Eregistry\Category;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CategoryRepository extends BaseRepository
{
    
      
    /**
     * Specify Model class name
     * 
     * @return 
     */
    public function model()
    {
        return Category::class;
    }

    /**
     * Create a new category record
     * 
     * @param array $input
     * @return Category
     */
    public function create(array $input)
    {
        $data = [
            'name' => $input['name'],
            'code' => $input['code'],
            'description' => $input['description'] ?? '',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $category = $this->model();
        $category = new $category($data);
        $category->save();

        return $category;
    }

    /**
     * Update an existing category record
     * 
     * @param Category $model
     * @param array $input
     * @return bool
     */
    public function update(Category $model, array $input)
    {
        $data = [
            'name' => $input['name'],
            'code' => $input['code'],
            'description' => $input['description'] ?? '',
            'created_at' => now(),
            'updated_at' => now(),
            'updated_by' => Auth::id(),
        ];

        return $model->update($data);
    }

    /**
     * Get categorys for data table with search and sorting
     * 
     * @param string $search
     * @param string $order_by
     * @param string $sort
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getForDataTable($search = '', $order_by = 'id', $sort = 'asc')
    {
        $query = $this->model->query()->select([
            'id', 
            'name',
            'code',
            'description'
            // \DB::raw("CONCAT(organisation_id, '/', category_number) AS fileindex") // Combine organisation_id and category_number as fileindex
        ]);

        if (!empty($search)) {
            $search = '%' . strtolower($search) . '%';
    
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', [$search])
                  ->orWhereRaw('LOWER(code) LIKE ?', [$search])
                  ->orWhereRaw('LOWER(description) LIKE ?', [$search]);
            });
        }

        return $query->orderBy($order_by, $sort);
    }



    /**
     * Get a list of categorys for dropdowns
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


    public function listWithDescriptions()
    {
        return $this->model->query()
            ->orderBy('name')
            ->get(['id', 'name', 'description']);
    }

}
