<?php

namespace App\Http\Controllers\National\Eregistry;

use App\Http\Controllers\Controller;
use App\Models\National\Eregistry\Division;
use App\Models\User;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function getOrganisationUsers($organisationId)
    {
        $users = User::where('organisation_id', $organisationId)->pluck('name', 'id');
        return response()->json($users);
    }

    public function getOrganisationDivision($organisationId)
    {
        $divisions = Division::where('organisation_id', $organisationId)->pluck('name', 'id');
        return response()->json($divisions);
    }
}