<?php

namespace App\Http\Controllers\National\Eregistry;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;



class EregistryBoradController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function index()
    {
        
        return view('national.eregistry.index');
    }
}
