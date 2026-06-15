<?php

namespace App\Http\Controllers\National\Eregistry;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles;



class EregistryBoradController extends Controller
{
    
    public function index()
    {
        $user = Auth::user();
        $ministryId = Auth::user()->ministry_id;
        // $reviewOfficer = User::role('review-officer')
        //                         ->where('ministry_id', $ministryId)
        //                         ->first();
       
        return view('national.eregistry.files.index');  
          
    }

    
    public function myFiles()
    {
        
        return view('national.eregistry.myfiles');
    }

    public function management()
    {
        
        return view('national.eregistry.management');
    }

    public function profile()
    {
        
        return view('national.eregistry.profile');
    }
}
