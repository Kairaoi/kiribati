<?php

namespace App\Http\Controllers\National\Eregistry;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index()
    {
        $activities = Activity::with(['causer', 'subject'])
                        ->latest()
                        ->paginate(20);

        return view('national.eregistry.activity_logs.index', compact('activities'));
    
    }
}