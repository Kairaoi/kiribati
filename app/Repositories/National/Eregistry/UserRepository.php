<?php

namespace App\Repositories\National\Eregistry;

use App\Models\User;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

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
    public function create(array $validated): User
    {
        return DB::transaction(function () use ($validated) {
            $user = $this->model()::create([
                'organisation_id' => $validated['organisation_id'],
                'division_id' => $validated['division_id'],
                'first_name'  => $validated['first_name'],
                'last_name'   => $validated['last_name'],
                'email'       => $validated['email'],
                'designation' => $validated['designation'] ?? null,
                'password'    => isset($validated['password'])
                    ? Hash::make($validated['password'])
                    : null,
                'is_active'   => true,
                'updated_by'  => auth()->id(),
            ]);

            $role = Role::findOrFail($validated['role']);

            $user->assignRole($role);

            return $user;
        });
    }

    /**
     * Update an existing user record
     *
     * @param User $model
     * @param array $input
     * @return bool
     */
    public function update(User $user, array $data): User
    {
        $user->update([
            'organisation_id' => $data['organisation_id'] ?? $user->organisation_id ,
            'division_id' => $data['division_id'] ?? $user->division_id,
            'first_name'  => $data['first_name'] ?? $user->first_name,
            'last_name'   => $data['last_name'] ?? $user->last_name,
            'email'       => $data['email'] ?? $user->email,
            'designation' => $data['designation'] ?? $user->designation,
            'is_active'   => true,
            'updated_by'  => auth()->id(),
        ]);

        return $user;
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
            'users.uuid as uuid',
            'users.first_name',
            'users.last_name',
            'users.email',
            'users.designation',
            'users.created_at',
            'users.updated_at',
            'divisions.name as division_name',
            'users.is_active as is_active',
            DB::raw('GROUP_CONCAT(roles.name SEPARATOR ", ") as role_names')
        ])
        ->leftJoin('divisions', 'users.division_id', '=', 'divisions.id')
        ->leftJoin('organisations', 'users.organisation_id', '=', 'organisations.id')
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
        } else if ($user->hasRole('admin') || $user->hasRole('registry')) {
            // Ministry admin only sees users from their ministry
            $query->where('users.organisation_id', $user->organisation_id)
                ->whereDoesntHave('roles', function ($q) {
                    $q->where('name', 'system-admin');
                });
        }

           
        if (!empty(trim($search))) {
        $search = '%' . strtolower(trim($search)) . '%';

        $query->where(function ($q) use ($search) {
            $q->whereRaw('LOWER(users.first_name) LIKE ?', [$search])
                ->orWhereRaw('LOWER(users.last_name) LIKE ?', [$search])
                ->orWhereRaw(
                    "LOWER(CONCAT(users.first_name, ' ', users.last_name)) LIKE ?",
                    [$search]
                )
                ->orWhereRaw('LOWER(users.email) LIKE ?', [$search])
                ->orWhereRaw('LOWER(users.designation) LIKE ?', [$search])
                ->orWhereRaw('LOWER(divisions.name) LIKE ?', [$search])
                ->orWhereRaw('LOWER(roles.name) LIKE ?', [$search]);
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
        $organisation_id = Auth::user()->organisation_id;

        //return users name and id for the logged in organisation 
        //and with the role of 'admin'
        return $this->model->query()
            ->where('organisation_id', $organisation_id)
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
            ->orderBy('name')
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
            ->select('users.id', 'users.division_id', 'users.first_name', 'users.last_name', 'divisions.name as division_name', 'users.designation')
            ->join('divisions', 'users.division_id', '=', 'divisions.id')
            ->where('users.organisation_id', Auth::user()->organisation_id)->where('users.email', '!=', 'admin@system.gov.ki')
            ->where('users.id', '!=', Auth::user()->id)
            ->where('users.is_active', true)
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
            ->where('users.id', '!=', Auth::user()->id)
            ->where('users.is_active', true)
            ->where('users.organisation_id', Auth::user()->organisation_id)->where('users.email', '!=', 'admin@system.gov.ki')
            ->where('users.division_id', $userDivisionId)
            ->orderBy('divisions.name')
            ->orderBy('users.first_name')
            ->orderBy('users.last_name')
            ->get();
    }

    public function updateMainRole(User $user, int $roleId): void
    {
        $role = Role::findOrFail($roleId);

        $editableRoles = [
            'user',
            'registry',
            'admin',
        ];

        foreach ($editableRoles as $editableRole) {
            if ($user->hasRole($editableRole)) {
                $user->removeRole($editableRole);
            }
        }

        $user->assignRole($role);
    }

}
