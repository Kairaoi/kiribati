<?php

namespace App\Services;

use App\Models\National\Eregistry\Division;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DivisionService
{
    public function updateHod(Request $request, Division $division)
    {
        $validated = $request->validate([
            'hod_id' => ['required', 'exists:users,id'],
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use (
            $validated,
            $division
        ) {
            // Ensure the selected user belongs to this division.
            $user = User::where('division_id', $division->id)
                ->findOrFail($validated['hod_id']);

            // Remove HOD from every other user in this division.
            $previousHods = User::where('division_id', $division->id)
                ->where('id', '!=', $user->id)
                ->role('hod')
                ->get();

            foreach ($previousHods as $previousHod) {
                $previousHod->removeRole('hod');
            }

            $user->assignRole('hod');

            $division->update([
                'hod_id' => $user->id,
            ]);
        });

        return redirect()
            ->route('registry.divisions.index')
            ->with('success', 'Head of Division assigned successfully.');
    }
}