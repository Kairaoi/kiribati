<?php

namespace App\Http\Controllers\National\Eregistry;

use App\Http\Controllers\Controller;
use App\Repositories\National\Eregistry\MovementRepository;
use App\Repositories\National\Eregistry\InwardFileRepository;
use App\Repositories\National\Eregistry\MinistryRepository;
use App\Repositories\National\Eregistry\DivisionRepository;
use App\Repositories\National\Eregistry\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;


class MovementController extends Controller
{
    private $movement;
    private $files;
    private $ministries;
    private $divisions;
    private $users;

    public function __construct(
        MovementRepository $movement,
        InwardFileRepository $files,
        MinistryRepository $ministries,
        DivisionRepository $divisions,
        UserRepository $users
    )
    {
        $this->movement = $movement;
        $this->files = $files;
        $this->ministries = $ministries;
        $this->divisions = $divisions;
        $this->users = $users;
    }

    /**
     * Get data for DataTables.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function getDataTables(Request $request)
    {
        $search = $request->get('search', '');
        if (is_array($search)) {
            $search = $search['value'];
        }
        $query = $this->movement->getForDataTable($search);
        $datatables = DataTables::make($query)->make(true);
        return $datatables;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('national.eregistry.movements.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Auth::user()->can('movement.create')) {
            abort(403, 'Unauthorized action.');
        }

        $files = $this->files->pluck();
        $ministries = $this->ministries->pluck();
        $divisions = $this->divisions->pluck();
        $users = $this->users->pluck();

        return view('national.eregistry.movements.create', [
            'files' => $files,
            'ministries' => $ministries,
            'divisions' => $divisions,
            'users' => $users,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!Auth::user()->can('movement.store')) {
            abort(403, 'Unauthorized action.');
        }

        $input = $request->all();

        // Validation
        $request->validate([
            'file_id' => 'required|exists:files,id',
            'from_ministry_id' => 'required|exists:ministries,id',
            'to_ministry_id' => 'required|exists:ministries,id',
            'from_division_id' => 'required|exists:divisions,id',
            'to_division_id' => 'required|exists:divisions,id',
            'from_user_id' => 'required|exists:users,id',
            'to_user_id' => 'required|exists:users,id',
            'movement_start_date' => 'required|date',
            'movement_end_date' => 'required|date',
            'comments' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        // Store the movement record
        $this->movement->create($input);

        return redirect()->route('movements.index')->with('message', 'Movement created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!Auth::user()->can('movement.show')) {
            abort(403, 'Unauthorized action.');
        }

        $movement = $this->movement->getById($id);

        return view('national.eregistry.movements.show')->with('movement', $movement);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!Auth::user()->can('movement.edit')) {
            abort(403, 'Unauthorized action.');
        }

        $movement = $this->movement->getById($id);
        $files = $this->files->pluck();
        $ministries = $this->ministries->pluck();
        $divisions = $this->divisions->pluck();
        $users = $this->users->pluck();

        return view('national.eregistry.movements.edit', [
            'movement' => $movement,
            'files' => $files,
            'ministries' => $ministries,
            'divisions' => $divisions,
            'users' => $users,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!Auth::user()->can('movement.update')) {
            abort(403, 'Unauthorized action.');
        }

        $movement = $this->movement->getById($id);
        $this->movement->update($movement, $request->all());

        return redirect()->route('movements.index')->with('message', 'Movement updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!Auth::user()->can('movement.delete')) {
            abort(403, 'Unauthorized action.');
        }

        $movement = $this->movement->getById($id);
        $this->movement->delete($movement);

        return redirect()->route('movements.index')->with('message', 'Movement deleted successfully.');
    }
}
