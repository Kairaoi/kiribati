<?php

namespace App\Http\Controllers\National\Eregistry;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    /**
     * Get data for DataTables.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function getDataTables(Request $request)
    {

        $roles = Role::with('permissions')->select('roles.*');

        $search = $request->get('search', '');
        if (is_array($search)) {
            $search = $search['value'];
        }

        return DataTables::of($roles)
        ->addColumn('permissions', function ($role) {
            return $role->permissions->pluck('name')->join(', '); // Convert array to comma-separated string
        })
        ->rawColumns(['permissions']) // Ensure HTML is processed if needed
        ->make(true);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('national.eregistry.roles.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissions = Permission::all();
        return view('national.eregistry.roles.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validation for role creation
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'required|array', // Ensure permissions are passed
        ]);

        // Create the role using Spatie Role model
        $role = Role::create([
            'name' => $request->name,
        ]);

        // Assign the selected permissions to the role
        $role->syncPermissions($request->permissions);

        return redirect()->route('registry.roles.index')->with('success', 'Role created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Show a specific role's details
        $role = Role::findOrFail($id);
        return view('national.eregistry.roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $role = Role::findOrFail($id);
        return view('national.eregistry.roles.edit', compact('role'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validate the input
        $request->validate([
            'name' => 'required|unique:roles,name,' . $id, // Ensure the name is unique but ignore the current role
            'permissions' => 'required|array', // Ensure permissions are passed
        ]);

        $role = Role::findOrFail($id);
        $role->name = $request->name;
        $role->save();

        // Sync the permissions with the role
        $role->syncPermissions($request->permissions);

        return redirect()->route('roles.index')->with('success', 'Role updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $role = Role::findOrFail($id);
        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Role deleted successfully!');
    }
}
