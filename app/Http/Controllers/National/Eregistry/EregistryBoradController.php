<?php

namespace App\Http\Controllers\National\Eregistry;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;



class EregistryBoradController extends Controller
{
    
    public function index()
    {
        $user = auth()->user();
        $ministryId = auth()->user()->ministry_id;
        $reviewOfficer = User::role('review-officer')
                                ->where('ministry_id', $ministryId)
                                ->first();
        if ($user->hasAnyRole('registry', 'sro')) {
            
            return view('national.eregistry.index', compact('reviewOfficer'));
        }
        if ($user->hasAnyRole('admin', 'sro')) {
            
            return view('national.eregistry.files.index');
        }

        if ($user->hasAnyRole('user')) {
             return view('national.eregistry.files.index');

        }
        
        return view('national.eregistry.index', compact('reviewOfficer')); //dashboard
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
