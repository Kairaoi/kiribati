<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEregistryFilingSystemTables extends Migration
{
    public function up()
    {
        // Create file_types table
        Schema::create('file_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_global')->default(false);
            $table->boolean('is_active')->default(true);
            $table->foreignId('organisation_id')->nullable(); // NULL = global type, NOT NULL = organisation-specific
            $table->text('description')->nullable(); 
            $table->string('code');

            $table->timestamps();

            // Prevent duplicates within same organisation
            $table->unique(['organisation_id', 'name']);
            $table->unique(['organisation_id', 'code']);
        });

        //  Create categories table
         Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Financial, Administrative, etc.
            $table->string('code')->unique(); // Unique code for each category
            $table->text('description')->nullable();
            $table->timestamps();
        });


        Schema::create('organisation_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); //  organisation, international org, diplomatic missions, SOE, 
            $table->text('description')->nullable();
            $table->timestamps();
        });


        //Organisations
        Schema::create('organisations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code');
            $table->uuid('uuid')->unique();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->text('address')->nullable();
            $table->string('po_box')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('reviewer_title')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->constrained('users');
            $table->foreignId('organisation_type_id')->constrained('organisation_types');
            $table->timestamps();

            $table->unique(['name', 'organisation_type_id']);
        });

    
        Schema::create('external_partners', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('location')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->foreignId('organisation_id')->constrained('organisations'); 
            $table->foreignId('organisation_type_id')->nullable()->constrained('organisation_types');
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->constrained('users');
            $table->enum('status', ['active', 'pending'])->default('pending');
            $table->timestamps();

            $table->unique(['name', 'organisation_type_id']);
        });


        Schema::create('divisions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('organisation_id')->constrained('organisations');
            $table->foreignId('hod_id')->nullable()->constrained('users');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
        });

        
        Schema::create('hod_appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('division_id')->constrained('divisions')->restrictOnDelete();
            $table->foreignId('officer_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('appointed_by')->constrained('users')->restrictOnDelete();
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->dateTime('revoked_at')->nullable();
            $table->timestamps();

            $table->index(['division_id', 'revoked_at', 'starts_at'], 'hod_appointments_active_index');
        });


        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('division_id')->constrained('divisions');
            $table->foreignId('organisation_id')->constrained('organisations');
            $table->foreignId('unit_head_id')->nullable()->constrained('users');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });


        // Create files table
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('reference_no')->unique();
            $table->foreignId('organisation_id')->nullable()->constrained('organisations'); //who logs the file in the system
            $table->foreignId('from_division_id')->nullable()->constrained('divisions');
            $table->morphs('source');
            $table->enum('correspondence_type', ['internal', 'letter', 'memo'])->nullable();
            $table->enum('document_source', ['upload', 'online'])->nullable();
            $table->json('memo_recipients')->nullable();
            $table->json('letter_recipients')->nullable();
            $table->text('content')->nullable();
            $table->foreignId('file_type_id')->constrained('file_types');
            $table->foreignId('category_id')->nullable()->constrained('categories');
            $table->string('subject');
            $table->string('main_file_path')->nullable();
            $table->string('main_file_name')->nullable();
            $table->json('additional_file_paths')->nullable(); 
            $table->date('letter_date');
            $table->date('due_date')->nullable();
            $table->string('status')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->constrained('users');
            $table->string('memo_from_field')->nullable();
            $table->string('memo_cc_field')->nullable();
            $table->string('memo_attention_to')->nullable();
            $table->string('internal_from_field')->nullable();
            $table->string('internal_to_field')->nullable();
            $table->string('internal_cc_field')->nullable();
            $table->foreignId('internal_ufs_id')->nullable()->constrained('users'); 
            $table->string('final_pdf_path')->nullable();
            $table->datetime('final_pdf_rendered_at')->nullable();
            $table->string('final_pdf_hash')->nullable();
            $table->foreignId('signed_by')->nullable()->constrained('users');
            $table->string('signature_path')->nullable();
            $table->datetime('signed_at')->nullable();
            $table->datetime('signature_cancelled_at')->nullable();
            $table->foreignId('signature_cancelled_by')->nullable()->constrained('users');
            $table->string('signature_cancelled_comment')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });


        Schema::create('dispatches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('file_id')->constrained('files');
            $table->foreignId('dispatched_by')->nullable()->constrained('users');
            $table->datetime('dispatch_date')->nullable();
            $table->boolean('read_status')->default(false);
            $table->text('comments')->nullable();
            $table->string('required_action')->nullable();
            $table->string('action_taken')->nullable();
            $table->foreignId('updated_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });


        Schema::create('folders', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();   
            $table->string('name');         
            $table->foreignId('organisation_id')->constrained('organisations');
            $table->foreignId('parent_id')->nullable()->constrained('folders')->nullOnDelete();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });

            
        Schema::create('file_folder_assignments', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('file_id')->constrained('files')->cascadeOnDelete();      
            $table->foreignId('organisation_id')->constrained('organisations')->cascadeOnDelete();
            $table->foreignId('folder_id')->constrained('folders')->cascadeOnDelete();
            $table->foreignId('filed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->datetime('filed_at')->default(now());
            $table->foreignId('updated_by')->constrained('users');
            $table->timestamps();

            $table->unique(['file_id', 'organisation_id']);
        });
            
    
        Schema::create('file_circulations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('file_id')->constrained();
            $table->foreignId('dispatch_id')->nullable()->constrained('dispatches')->nullOnDelete();
            $table->foreignId('to_organisation_id')->constrained('organisations');
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->datetime('received_at')->nullable();
            $table->foreignId('circulated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->datetime('circulated_at')->default(now());
            $table->enum('status', ['Pending',
                                    'Received',
                                    'Pending Dispatch',
                                    'Pending Review', 
                                    'Pending Receipt',
                                    'Pending Approval',
                                    'Pending UFS Circulation',
                                    'Pending UFS',
                                    'Reviewed', 
                                    'UFS Approved',
                                    'UFS Rejected',
                                    'Approved',
                                    'Cancelled',
                                    'Dispatched',
                                    'Returned for Amendment',
                                    'Pending Signature',
                                    'Pending SRO Signature',
                                    'Pending HOD Signature',
                                    'Signature Cancelled',
                                    'Pending SRO Review',
                                    'Closed',
                                    'Filed'
                    ]);
            $table->enum('ufs_status', ['Pending', 'Approved', 'Rejected'])->nullable();
            $table->enum('final_review_status', ['Pending SRO Review', 
                                                'Reviewed',
                                                ])->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users');            
            $table->datetime('read_at')->nullable();                     
            $table->boolean('read_status')->default(false);
            $table->text('review_comment')->nullable();
            $table->foreignId('review_officer')->nullable()->constrained('users'); 
            $table->foreignId('reviewed_by')->nullable()->constrained('users'); 
            $table->foreignId('hod_reviewer')->nullable()->constrained('users');
            $table->string('hod_comment')->nullable();
            $table->datetime('date_reviewed')->nullable();
            $table->boolean('requires_action')->default(false);    
            $table->text('action_taken')->nullable();          
            $table->datetime('ufs_approved_at')->nullable();
            $table->datetime('ufs_rejected_at')->nullable();
            $table->string('ufs_comment')->nullable();
           
            $table->timestamps();
    
            $table->unique(['file_id',  'to_organisation_id']); 
        });

        

        Schema::create('file_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('file_circulation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('officer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('assigned_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('assigned_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->enum('status', ['pending', 
                                    'in_progress', 
                                    'reassigned', 
                                    'complete'])->default('pending'); // Added status field
        
            $table->foreignId('reassigned_from')->nullable()->constrained('users')->nullOnDelete();
            $table->string('task')->nullable();
            $table->string('assigned_officer_comment')->nullable();
            $table->datetime('accepted_at')->nullable();
            $table->datetime('completed_at')->nullable();
            $table->timestamps();
            $table->unique(['file_circulation_id',  'officer_id']); 
        });
      

        Schema::create('organisation_archived_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organisation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('file_id')->constrained()->cascadeOnDelete();
            $table->foreignId('archived_by')->constrained('users');
            $table->datetime('archived_at')->default(now());
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->unique(['organisation_id', 'file_id']); 
        });


        Schema::create('organisation_closed_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organisation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('file_id')->constrained()->cascadeOnDelete();
            $table->foreignId('closed_by')->constrained('users');
            $table->datetime('closed_at')->default(now());
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->unique(['organisation_id', 'file_id']); 
        });


        Schema::create('file_sequences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organisation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('file_type_id')->constrained()->cascadeOnDelete();

            $table->integer('year');
            $table->integer('last_number')->default(0);
            $table->timestamps();

            $table->unique(['organisation_id', 'file_type_id', 'year']);
        });


        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

    }


    public function down()
    {
        Schema::dropIfExists('file_circulations');
        Schema::dropIfExists('file_categories');
        Schema::dropIfExists('files');
        Schema::dropIfExists('file_recipients');
        Schema::dropIfExists('file_types');
        Schema::dropIfExists('divisions');
        Schema::dropIfExists('hod_appointments');
        Schema::dropIfExists('organisations');
        Schema::dropIfExists('organisation_types');
        Schema::dropIfExists('file_sequences');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('dispatches');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('file_assignments');
        Schema::dropIfExists('organisation_archived_files');
        Schema::dropIfExists('file_folder_assignments');
        Schema::dropIfExists('organisation_closed_files');
        Schema::dropIfExists('file_assignments');
        Schema::dropIfExists('folders');
    }
}
