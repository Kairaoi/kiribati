<?php

namespace App\Http\Controllers\National\Eregistry;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;



class EregistryBoradController extends Controller
{
    
    public function index()
    {
        
        return view('national.eregistry.index');
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
