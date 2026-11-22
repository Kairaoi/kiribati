<?php

namespace App\Repositories\National\Eregistry;

use App\Models\National\Eregistry\File;
use App\Repositories\BaseRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

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
    public function createFile(array $validated, 
                           ?string $mainFilePath,
                           array $additionalFilePaths,
                           string $referenceNo,
                           array $letterRecipients = [],
                           array $memoRecipients = [],
                           int $userId,
                           int $organisationId,
                           ?string $mainFileName = null
    ): File {

    $correspondenceType = $validated['correspondence_type'] ?? null;

    $fileData = array_merge($validated, [
        'main_file_path'        => $mainFilePath,
        'main_file_name'        => $mainFileName,
        'additional_file_paths' => $additionalFilePaths,
        'reference_no'          => $referenceNo,
        'is_active'             => true,
        'created_by'            => $userId,
        'updated_by'            => $userId,
        'letter_date'           => now()->toDateString(),
        'organisation_id'       => $organisationId,

        'status' => $validated['document_source'] === 'online'
            ? 'Pending Signature'
            : 'Pending Action',

        'letter_recipients' => $correspondenceType === 'letter'
            ? $letterRecipients
            : [],

        'memo_recipients' => $correspondenceType === 'memo'
            ? $memoRecipients
            : [],
    ]);

    return $this->model()::create($fileData);
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

    /**
     * Check whether the current user has a given role or roles.
     */
    protected function userHasRole($user, $roles): bool
    {
        if (!$user) {
            return false;
        }

        $roles = is_array($roles) ? $roles : [$roles];

        if (method_exists($user, 'hasRole')) {
            return $user->hasRole($roles);
        }

        if (isset($user->role)) {
            $userRole = $user->role;
            if (is_array($userRole)) {
                return count(array_intersect($roles, $userRole)) > 0;
            }
            return in_array($userRole, $roles, true);
        }

        if (!empty($user->roles) && is_iterable($user->roles)) {
            foreach ($user->roles as $role) {
                if (is_string($role) && in_array($role, $roles, true)) {
                    return true;
                }
                if (is_object($role) && isset($role->name) && in_array($role->name, $roles, true)) {
                    return true;
                }
            }
        }

        return false;
    }


    public function getForDataTable($search, $user, int $userOrgId, string $type)
    {
        
        $latestDispatches = DB::table('dispatches')
        ->select('file_id')
        ->selectRaw('MAX(dispatch_date) AS dispatch_date')
        ->groupBy('file_id');

        $query = $this->model->query()
                            ->select([
                                'files.id as id',
                                'files.uuid as uuid',
                                'files.subject as file_subject',
                                'files.status as file_status',
                                'fc.id as circulation_id',
                                'fc.status as circulation_status',
                                'fc.review_comment as review_comment',
                                'fc.reviewed_by as circulation_reviewed_by',
                                'fc.to_organisation_id as circulation_organisation_id', //used in getDataTables in FileController
                                'files.reference_no as reference_no',
                                'files.letter_date as letter_date',
                                'files.organisation_id as organisation_id',
                                'files.due_date as due_date',
                                'categories.name as category',
                                'file_types.name as file_type',
                                'dispatches.dispatch_date as dispatch_date',
                                'fc.received_at as received_at',
                                DB::raw("CONCAT(received_user.first_name, ' ', received_user.last_name) as received_by"),
                                'files.correspondence_type as correspondence_type'
                            ])
                            ->join('organisations', 'files.organisation_id', '=', 'organisations.id')
                            ->leftJoin('categories', 'categories.id', '=', 'files.category_id')
                            ->leftJoin('file_types', 'file_types.id', '=', 'files.file_type_id')
                            ->leftJoin('file_circulations as fc', function ($join) use ($userOrgId) {
                                $join->on('files.id', '=', 'fc.file_id')
                                    ->where('fc.to_organisation_id', $userOrgId);
                            })
                            ->where(function ($query) {
                                $query->whereNull('fc.status')
                                    ->orWhere('fc.status', '!=', 'Filed');
                            })
                            ->leftJoin('users as received_user', 'fc.received_by', '=', 'received_user.id')
                            ->leftJoin('file_assignments as fa', function ($join) use ($user) {
                                $join->on('fa.file_circulation_id', '=', 'fc.id')
                                    ->where('fa.officer_id', $user->id);
    
                            })
                            ->leftJoinSub($latestDispatches, 'dispatches', function ($join) {
                                $join->on('dispatches.file_id', '=', 'files.id');
                            });
                            
                            
        if ($this->userHasRole($user, 'sro')) {
            $query->where(function ($q) use ($user) {
                $q->where('fc.status', 'Pending SRO Review')
                    ->orWhere('files.status', 'Pending SRO Review')
                    ->orWhere('fc.status', 'Reviewed')
                    ->orWhere('fc.reviewed_by', $user->id)
                    ->orWhere('fc.review_officer', $user->id)
                    ->orWhere('files.created_by', $user->id);
            });

            $query->where(function ($q) use ($userOrgId) {
                $q->where('files.organisation_id', $userOrgId)
                    ->orWhereNotNull('fc.id');
            });

        } else if ($this->userHasRole($user, ['system-admin', 'admin']) && 
            !$this->userHasRole($user, ['registry', 'user', 'review-officer', 'ufs'])) {
                $query->whereRaw('1 = 0');

        } else if ($this->userHasRole($user, 'registry')) {
            $query->where(function ($query) use ($userOrgId, $user) {
                $query->where(function ($organisationFiles) use ($userOrgId) {
                    $organisationFiles
                        ->where(function ($scope) use ($userOrgId) {
                            $scope->where('files.organisation_id', $userOrgId)
                                ->orWhere('fc.to_organisation_id', $userOrgId);
                        })
                        ->whereIn('files.status', [
                            'Pending Dispatch',
                            'Pending SRO Review',
                            'Pending SRO Signature',
                            'Reviewed',
                            'Dispatched',
                        ]);
                })
                ->orWhere('files.created_by', $user->id)
                ->orWhere(function ($ufs) use ($user) {
                    $ufs->where('files.internal_ufs_id', $user->id)
                        ->where('files.status', 'Pending UFS');
                });
            });

        } else if ($this->userHasRole($user, ['user'])) {
            $query->where(function ($q) use ($user) {
                $q->where(function ($assigned) use ($user) {
                    $assigned->where('fa.officer_id', $user->id);
                })
                ->orWhere(function ($ufs) use ($user) {
                    $ufs->where('files.internal_ufs_id', $user->id);
                })
                ->orWhere(function ($created_by) use ($user) {
                    $created_by->where('files.created_by', $user->id);
                })
                ->orWhere(function ($review) use ($user) {
                    $review->where('fc.review_officer', $user->id);
                })
                ->orWhere(function ($reviewedBy) use ($user) {
                    $reviewedBy->where('fc.reviewed_by', $user->id)
                               ->orWhere('files.signed_by', $user->id);
                })      
                ->orWhere(function ($hodReview) use ($user) {
                    $hodReview->where('fc.hod_reviewer', $user->id);
                });
            });
        }

        if ($type === 'active') {
            $query->whereNotExists(function ($query) use ($userOrgId) {
                    $query->selectRaw(1)
                        ->from('organisation_closed_files as mcf')
                        ->whereColumn('mcf.file_id', 'files.id')
                        ->where('mcf.organisation_id', $userOrgId);
                    });
        } 
        
        if ($type === 'closed') {
            $query->whereExists(function ($query) use ($userOrgId) {
                    $query->selectRaw(1)
                        ->from('organisation_closed_files as mcf')
                        ->whereColumn('mcf.file_id', 'files.id')
                        ->where('mcf.organisation_id', $userOrgId);
                    });
        
        }

           
        if (!empty(trim($search))) {
            $search = '%' . strtolower(trim($search)) . '%';

            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(files.subject) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(files.reference_no) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(file_types.name) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(files.due_date) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(files.status) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(dispatches.dispatch_date) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(files.correspondence_type) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(fc.received_at) LIKE ?', [$search]);
            });
        }
     
        return $query;                
    }


    public function pluck($column = 'name', $key = 'id')
    {
        $organisationId = Auth::user()->organisation_id;
    
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



