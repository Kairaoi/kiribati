<?php

namespace App\Repositories\National\Eregistry;

use App\Models\User;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

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
            'first_name' => $input['first_name'],
            'last_name' => $input['last_name'],
            'division_id' => $input['division_id'] ?? null,
            'organisation_id' => $input['organisation_id'] ?? null,
            'role' => $input['role'] ?? null,
            'email' => $input['email'],
            'password'=> Hash::make($input['password']),// Only here
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
            'first_name' => $input['first_name'] ?? $model->name,
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
    public function getForDataTable($search = '', $user = null)
    {
        $query = $this->model->query()
        ->select([
            'users.id',
            'users.first_name',
            'users.last_name',
            'users.email',
            'users.designation',
            'users.created_at',
            'users.updated_at',
            'users.profile_photo_path',
            'divisions.name as division_name',
            'ministries.code as ministry_code',
            'users.is_active as status',
            DB::raw('GROUP_CONCAT(roles.name SEPARATOR ", ") as role_names')
        ])
        ->leftJoin('divisions', 'users.division_id', '=', 'divisions.id')
        ->join('ministries', 'users.ministry_id', '=', 'ministries.id')
        ->leftJoin('model_has_roles', function ($join) {
            $join->on('users.id', '=', 'model_has_roles.model_id')
                 ->where('model_has_roles.model_type', '=', User::class);
        })
        ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
        ->groupBy(
            'users.id',
        
        );


        if ($user->hasRole('system-admin')) {
             // System admin sees all users
        } else if ($user->hasRole('ministry-admin') || $user->hasRole('registry')) {
            // Ministry admin only sees users from their ministry
            $query->where('users.ministry_id', $user->ministry_id)
                ->whereDoesntHave('roles', function ($q) {
                    $q->where('name', 'system-admin');
                });
        }

        
        return $query->orderBy('users.created_at', 'desc');
    }

    /**
     * Get a list of users for dropdowns
     *
     * @param string $column
     * @param string $key
     * @return \Illuminate\Support\Collection
     */
    public function pluck($column = 'first_name', $key = 'id')
    {
        $ministry_id = auth()->user()->ministry_id;

        //return users name and id for the logged in organisation 
        //and with the role of 'admin'
        return $this->model->query()
            ->where('ministry_id', $ministry_id)
            ->orderBy($column)
            ->pluck($column, $key);
    }

     /**
     * Get a full list of users for dropdowns
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
            ->get(['id', 'first_name', 'last_name']);
    }

    /**
     * Get users with their division in a organisation
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUsersDivision()
    {
        // dd(Auth::user()->ministry_id);

        return $this->model->query()
            ->select('users.id', 'users.division_id', 'users.first_name', 'users.last_name', 'divisions.name as division_name')
            ->join('divisions', 'users.division_id', '=', 'divisions.id')
            ->where('users.ministry_id', Auth::user()->ministry_id)->where('users.email', '!=', 'admin@system.gov.ki')
            ->orderBy('divisions.name')
            ->orderBy('users.first_name')
            ->orderBy('users.last_name')
            ->get();
    }


      /**
     * Get users in the same division in the same organisation
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getDivisionUsers($userDivisionId)
    {

        return $this->model->query()
            ->select('users.id', 
                    'users.division_id', 
                    'users.first_name', 
                    'users.last_name', 
                    'divisions.name as division_name',
                    'users.email as email',
                    'users.designation as designation',
                    'users.is_active as is_active')
            ->join('divisions', 'users.division_id', '=', 'divisions.id')
            ->where('users.ministry_id', Auth::user()->ministry_id)->where('users.email', '!=', 'admin@system.gov.ki')
            ->where('users.division_id', $userDivisionId)
            ->orderBy('divisions.name')
            ->orderBy('users.first_name')
            ->orderBy('users.last_name')
            ->get();
    }

}
