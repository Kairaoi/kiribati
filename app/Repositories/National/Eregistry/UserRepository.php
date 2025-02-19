<?php

namespace App\Repositories\National\Eregistry;

use App\Models\User;
use App\Repositories\BaseRepository;
use Auth;


class UserRepository extends BaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return User::class;
    }

    /**
     * Create a new user record
     *
     * @param array $input
     * @return User
     */
    public function create(array $input)
    {
        $data = [
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => bcrypt($input['password']),
            'current_team_id' => $input['current_team_id'] ?? null,
            'profile_photo_path' => $input['profile_photo_path'] ?? null,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ];

        $user = $this->model();
        $user = new $user($data);
        $user->save();

        return $user;
    }

    /**
     * Update an existing user record
     *
     * @param User $model
     * @param array $input
     * @return bool
     */
    public function update(User $model, array $input)
    {
        $data = [
            'name' => $input['name'] ?? $model->name,
            'email' => $input['email'] ?? $model->email,
            'password' => isset($input['password']) ? bcrypt($input['password']) : $model->password,
            'current_team_id' => $input['current_team_id'] ?? $model->current_team_id,
            'profile_photo_path' => $input['profile_photo_path'] ?? $model->profile_photo_path,
            'updated_by' => Auth::id(),
        ];

        return $model->update($data);
    }

    /**
     * Get users for data table with search and sorting
     *
     * @param string $search
     * @param string $order_by
     * @param string $sort
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getForDataTable($search = '', $order_by = 'id', $sort = 'asc')
    {
        $query = $this->model->query()
            ->select(['id', 'name', 'email', 'current_team_id', 'profile_photo_path']);

        if (!empty($search)) {
            $search = '%' . strtolower($search) . '%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ILIKE', $search)
                  ->orWhere('email', 'ILIKE', $search);
            });
        }

        return $query->orderBy($order_by, $sort);
    }

    /**
     * Get a list of users for dropdowns
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
