<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class FileReferenceService
{
    public static function generate($ministry_id, $file_type_id)
    {
        $year = now()->year;

        return DB::transaction(function () use ($ministry_id, $file_type_id, $year) {

            // Get ministry code
            $ministryCode = DB::table('ministries')
                ->where('id', $ministry_id)
                ->value('code') ?? 'OTH';

            // Get file type details
            $fileType = DB::table('file_types')
                ->where('id', $file_type_id)
                ->where(function ($query) use ($ministry_id) {
                    $query->where('ministry_id', $ministry_id)
                        ->orWhereNull('ministry_id');
                })
                ->first();

            if (!$fileType) {
                throw new \Exception("Invalid file type for this organisation.");
            }

            $typeCode = $fileType->code;

            // Lock sequence row (prevents duplicates)
            $sequence = DB::table('file_sequences')
                ->where('ministry_id', $ministry_id)
                ->where('file_type_id', $file_type_id)
                ->where('year', $year)
                ->lockForUpdate()
                ->first();

            if (!$sequence) {
                $number = 1;

                DB::table('file_sequences')->insert([
                    'ministry_id' => $ministry_id,
                    'file_type_id' => $file_type_id,
                    'year' => $year,
                    'last_number' => $number,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $number = $sequence->last_number + 1;

                DB::table('file_sequences')
                    ->where('id', $sequence->id)
                    ->update([
                        'last_number' => $number,
                        'updated_at' => now(),
                    ]);
            }

            return strtoupper(
                "{$ministryCode}/{$typeCode}/{$year}/" .
                str_pad($number, 4, '0', STR_PAD_LEFT)
            );
        });
    }
}