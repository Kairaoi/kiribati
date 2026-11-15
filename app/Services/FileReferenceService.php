<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\National\Eregistry\Organisation;

class FileReferenceService
{
    public static function generate($organisation_id, $file_type_id)
    {
        $year = now()->year;

        return DB::transaction(function () use ($organisation_id, $file_type_id, $year) {

            // Get organisation code
            $organisationCode = Organisation::query()
                        ->whereKey($organisation_id)
                        ->value('code') ?? 'OTH';

            // Get file type details
            $fileType = DB::table('file_types')
                ->where('id', $file_type_id)
                ->where(function ($query) use ($organisation_id) {
                    $query->where('organisation_id', $organisation_id)
                        ->orWhereNull('organisation_id');
                })
                ->first();

            if (!$fileType) {
                throw new \Exception("Invalid file type for this organisation.");
            }

            $typeCode = $fileType->code;

            // Lock sequence row (prevents duplicates)
            $sequence = DB::table('file_sequences')
                ->where('organisation_id', $organisation_id)
                ->where('file_type_id', $file_type_id)
                ->where('year', $year)
                ->lockForUpdate()
                ->first();

            if (!$sequence) {
                $number = 1;

                DB::table('file_sequences')->insert([
                    'organisation_id' => $organisation_id,
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
                "{$organisationCode}-{$typeCode}-{$year}-" .
                str_pad($number, 2, '0', STR_PAD_LEFT)
            );
        });
    }
}