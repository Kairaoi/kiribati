<?php

namespace App\Repositories\National\Eregistry;

use App\Models\National\Eregistry\File;
use App\Repositories\BaseRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FileRepository extends BaseRepository
{
    /**
     * Specify Model class name
     */
    public function model()
    {
        return File::class;
    }

    /**
     * Create a new file record
     */
    public function create(array $input)
    {
        $data = [
            // 'folder_id' => $input['folder_id'],
            'organisation_id' => $input['organisation_id'],
            'file_reference' => $input['file_reference'] ?? 'FILE-' . time() . '-' . Auth::id(),
            'subject' => $input['subject'],
            'main_file_path' => $input['main_file_path'],
            'additional_file1_path' => $input['additional_file1_path'] ?? null,
            'additional_file2_path' => $input['additional_file2_path'] ?? null,
            'additional_file3_path' => $input['additional_file3_path'] ?? null,
            'letter_date' => $input['letter_date'],
            'comments' => $input['comments'] ?? '',
            'status' => isset($input['status']) ? $input['status'] : 'draft',
            'is_active' => $input['is_active'] ?? true,
            'file_type_id' => $input['file_type_id'],
            'category_id' => $input['category_id'] ?? null,
            'letter_ref_no' => $input['letter_ref_no'] ?? '',
            'division_id' => $input['division_id'] ?? null,
            'recipient_organisations' => $input['recipient_organisations'] ?? [],
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ];

        $file = new File($data);
        $file->save();

        // Update file_index after ID is available
        // $file->update(['file_index' => "{$file->organisation_id}/{$file->id}"]);

        return $file;
    }

    /**
     * Update a file record
     */
    public function update(File $model, array $input)
    {
        $data = array_merge($model->toArray(), [
            'organisation_id' => $input['organisation_id'] ?? $model->organisation_id,
            'subject' => $input['subject'] ?? $model->subject,
            'path' => $input['path'] ?? $model->path,
            'letter_date' => $input['letter_date'] ?? $model->letter_date,
            'status' => $input['status'] ?? $model->status,
            'is_active' => $input['is_active'] ?? $model->is_active,
            'file_type_id' => $input['file_type_id'] ?? $model->file_type_id,
            'division_id' => $input['division_id'] ?? $model->division_id,
            'category_id' => $input['category_id'] ?? $model->category_id,
            'letter_ref_no' => $input['letter_ref_no'] ?? $model->letter_ref_no,
            'recipient_organisations' => $input['recipient_organisations'] ?? $model->recipient_organisations,
            'updated_by' => Auth::id(),
        ]);

        return $model->update($data);
    }

    public function getForFilteredTable($selectedType, int $userMinistryId, array $filterOrgIds = [], $fromDate = null, $toDate = null)
    {
        return $this->model->query()
            ->forType($selectedType, $userMinistryId) //scope in Model
            ->forOrganisation($filterOrgIds)  //scope in Model
            ->forDateRange($fromDate, $toDate) //scope in Model
            ->join('organisations as from_org', 'files.organisation_id', '=', 'from_org.id')
          
            ->select([
                'files.id',
                'files.subject as file_subject',
                'files.letter_date as letter_date',
                'from_org.code as organisation_code',
            ]);
    } 


    public function getForDataTable(int $userMinistryId, string $type = 'active', $selectedType = null, $fileType = null, $category = null, ? int $organisationId = null, $fromDate = null, $toDate = null)
    {
        $user = auth()->user();
        $isAdminRegistrySro = $user->hasAnyRole(['ministry-admin', 'registry', 'sro']);
        $isReviewOfficer = $user->hasRole('review-officer');
        $isSystemAdmin = $user->hasRole('system-admin');

        $query = $this->model->query()
                            ->select([
                                'files.id as id',
                                'files.subject as file_subject',
                                'files.status as file_status',
                                'fc.id as circulation_id',
                                'fc.status as circulation_status',
                                'fc.to_ministry_id as circulation_ministry_id',
                                'files.reference_no as reference_no',
                                'files.letter_date as letter_date',
                                'files.ministry_id as ministry_id',
                                'files.due_date as due_date',
                                'categories.name as category',
                                'file_types.name as file_type',
                                'dispatches.dispatch_date as dispatch_date',
                                'fc.received_at as received_at',
                                // 'fc.received_by as received_by',
                                // DB::raw("CONCAT(received_by.first_name, ' ', received_by.last_name) as received_by_name"),
                            ])
                            ->join('ministries', 'files.ministry_id', '=', 'ministries.id')
                            ->leftJoin('categories', 'categories.id', '=', 'files.category_id')
                            ->leftJoin('file_types', 'file_types.id', '=', 'files.file_type_id')
                            ->leftJoin('file_circulations as fc', function ($join) use ($userMinistryId) {
                                $join->on('files.id', '=', 'fc.file_id')
                                    ->where('fc.to_ministry_id', $userMinistryId);
                            })
                            ->leftJoin('users as received_user', 'fc.received_by', '=', 'received_user.id')
                            ->leftJoin('file_assignments as fa', function ($join) use ($user) {
                                $join->on('fa.file_circulation_id', '=', 'fc.id')
                                    ->where('fa.officer_id', $user->id)
                                    ->where('fa.is_active', true);
                            })

                            ->leftJoin('dispatches', function ($join) {
                                $join->on('dispatches.file_id', '=', 'files.id');
                                    // ->on('dispatches.id', '=', 'fc.dispatch_id');
                            });

            

        if (!$user->hasRole('system-admin')) {
            $query->where(function ($query) use ($userMinistryId, $user) {
                $query->where('files.ministry_id', $userMinistryId)
                    ->orWhere('fc.to_ministry_id', $userMinistryId)
                    ->orWhere(function ($ufs) use ($user) {
                        $ufs->where('files.internal_ufs_id', $user->id)
                            ->where('files.status', 'Pending UFS');
                    });
            });
        }

        if ($user->hasRole(['user', 'ministry-admin'])) {
            $query->where(function ($q) use ($user) {
                $q->where(function ($assigned) use ($user) {
                    $assigned->whereNotNull('fa.id')
                        ->where('fa.officer_id', $user->id);
                })
                ->orWhere(function ($ufs) use ($user) {
                    $ufs->where('files.internal_ufs_id', $user->id)
                        ->where('files.status', 'Pending UFS');
                })
                ->orWhere(function ($created_by) use ($user) {
                    $created_by->where('files.created_by', $user->id);
                })
                ->orWhere(function ($review) use ($user) {
                    $review->where('fc.review_officer', $user->id);
                })
                ->orWhere(function ($colleagueReview) use ($user) {
                    $colleagueReview->where('fc.colleague_id', $user->id);
                });
            });
        }

        // if ($user->hasRole('hod')) {
        //     $query->where(function ($q) use ($user) {
        //         $q->where(function ($approve) use ($user) {
        //              ->where('fa.officer_id', $user->id);
        //         });
        //     });
        // }


        if ($type === 'active') {
            $query->whereNotExists(function ($query) use ($userMinistryId) {
                    $query->selectRaw(1)
                        ->from('ministry_closed_files as mcf')
                        ->whereColumn('mcf.file_id', 'files.id')
                        ->where('mcf.ministry_id', $userMinistryId);
                    });
        } 
        
        if ($type === 'closed') {
                $query->forType($selectedType, $userMinistryId)
                      ->forFileType($fileType, $userMinistryId)
                      ->forCategory($category, $userMinistryId);
        }

        return $query;                
    }


    public function pluck($column = 'name', $key = 'id')
    {
        $organisationId = auth()->user()->organisation_id;
    
        return $this->model()::query()
                ->where('organisation_id', $organisationId)
                ->where('is_active', true) 
                ->orderBy($column)
                ->pluck($column, $key);
    }


    public function list($column = 'name', $key = 'id')
    {
        return $this->model->query()
            ->orderBy($column)
            ->pluck($column, $key);
    }    

}



