<?php

namespace App\Http\Controllers\National\Eregistry;

use App\Http\Controllers\Controller;
use App\Models\National\Eregistry\Division;
use App\Models\User;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function getMinistryUsers($ministryId)
    {
        $users = User::where('ministry_id', $ministryId)->pluck('name', 'id');
        return response()->json($users);
    }

    public function getMinistryDivisions($ministryId)
    {
        $divisions = Division::where('ministry_id', $ministryId)->pluck('name', 'id');
        return response()->json($divisions);
    }
}