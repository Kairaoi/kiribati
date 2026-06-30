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
            $table->foreignId('ministry_id')->nullable(); // NULL = global type, NOT NULL = ministry-specific
            $table->text('description')->nullable(); 
            $table->string('code');
            $table->timestamps();

            // Prevent duplicates within same ministry
            $table->unique(['ministry_id', 'name']);
            $table->unique(['ministry_id', 'code']);
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


        //Global Identity Registry
        Schema::create('identity_organisations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code');
            $table->text('location')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->constrained('users');
            $table->foreignId('organisation_type_id')->constrained('organisation_types');
            $table->timestamps();

            $table->unique(['name', 'organisation_type_id']);
        });


        //Users of the system.
        Schema::create('ministries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->string('reviewer_title')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('organisation_type_id')->constrained('organisation_types');
            $table->foreignId('identity_organisation_id')->constrained('identity_organisations');
            $table->string('address')->nullable();
            $table->string('po_box')->nullable();
            $table->string('logo_path')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->constrained('users');

            $table->timestamps();
        });

    
        Schema::create('external_partners', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('location')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->foreignId('ministry_id')->constrained('ministries'); 
            $table->foreignId('organisation_type_id')->nullable()->constrained('organisation_types');
            $table->foreignId('identity_organisation_id')->nullable()->constrained('identity_organisations');
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
            $table->foreignId('ministry_id')->constrained('ministries');
            $table->foreignId('hod_id')->nullable()->constrained('users');
            $table->timestamps();
        });


        // Create files table
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string('reference_no')->unique();
            $table->foreignId('ministry_id')->nullable()->constrained('ministries'); //who logs the file in the system
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
            $table->json('additional_file_paths')->nullable(); 
            $table->string('main_file_name')->nullable(); 
            $table->date('letter_date');
            $table->date('due_date')->nullable();
            $table->string('status')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_archived')->default(false);
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->constrained('users');
            $table->string('memo_from_field')->nullable();
            $table->string('memo_cc_field')->nullable();
            $table->string('memo_attention_to')->nullable();
            $table->string('internal_from_field')->nullable();
            $table->string('internal_to_field')->nullable();
            $table->string('internal_cc_field')->nullable();
            $table->foreignId('internal_ufs_id')->nullable()->constrained('users'); 
            
            $table->timestamps();
            $table->softDeletes();
        });


        Schema::create('file_signatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('file_id')->constrained('files')->cascadeOnDelete();
            $table->foreignId('signed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('signed_name')->nullable();
            $table->string('signed_title')->nullable();
            $table->string('signed_ministry')->nullable();
            $table->string('signature_image')->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->timestamps();
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

    
        Schema::create('file_circulations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('file_id')->constrained();
            $table->foreignId('dispatch_id')->nullable()->constrained('dispatches')->nullOnDelete();
            $table->foreignId('to_ministry_id')->constrained('ministries');
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->datetime('received_at')->nullable();
            $table->foreignId('circulated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->datetime('circulated_at')->default(now());
            $table->enum('status', ['Pending',
                                    'Received',
                                    'Pending Review', 
                                    'Pending Receipt',
                                    'Pending Approval',
                                    'Pending UFS',
                                    'Reviewed', 
                                    'UFS Approved',
                                    'UFS Rejected',
                                    'Approved',
                                    'Rejected',
                                    'Dispatched',
                                    'Returned for Amendment',
                                    'Pending SRO Submission',
                                    'Pending SRO Approval',
                                    'Pending HOD Review',
                                    'Pending Colleague Review'
                    ]);
            $table->enum('ufs_status', ['Pending', 'Approved', 'Rejected'])->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users');            
            $table->datetime('read_at')->nullable();                     
            $table->boolean('read_status')->default(false);
            $table->text('review_comment')->nullable();
            $table->foreignId('review_officer')->nullable()->constrained('users'); 
            $table->foreignId('reviewed_by')->nullable()->constrained('users'); 
            $table->foreignId('colleague_id')->nullable()->constrained('users');
            $table->string('colleague_comment')->nullable();
            $table->string('approval_comment')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->datetime('approved_at')->nullable();
            $table->datetime('date_reviewed')->nullable();
            $table->boolean('requires_action')->default(false);    
            $table->text('action_taken')->nullable();          
            $table->datetime('ufs_approved_at')->nullable();
            $table->datetime('ufs_rejected_at')->nullable();
            $table->string('ufs_comment')->nullable();
            $table->foreignId('signed_by')->nullable()->constrained('users');
            $table->string('signature_path')->nullable();
            $table->datetime('signed_at')->nullable();
            $table->string('rendered_pdf_path')->nullable();
            $table->datetime('rendered_pdf_at')->nullable();
            $table->string('rendered_pdf_hash')->nullable();
            $table->timestamps();
    
            $table->unique(['file_id',  'to_ministry_id']); 
        });

        

        Schema::create('file_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('file_circulation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('officer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('assigned_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('assigned_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->enum('status', ['pending', 'accepted', 'reassigned', 'completed'])->default('pending'); // Added status field
            // for reassignment tracking
            $table->foreignId('reassigned_from')->nullable()->constrained('users')->nullOnDelete();
            $table->string('reassign_comment')->nullable();
            $table->datetime('accepted_at')->nullable();
            $table->timestamps();

            $table->unique(['file_circulation_id',  'officer_id']); 
        });


        Schema::create('document_overlays', function (Blueprint $table) {
            $table->id();
            $table->foreignId('file_id')->constrained()->cascadeOnDelete();
            $table->foreignId('file_circulation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('file_assignment_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('page_number')->default(1);
            $table->string('overlay_type'); 
            // approved_stamp, rejected_stamp, review_comment, signature
            $table->json('content')->nullable();
            $table->decimal('x_position', 8, 2)->default(0);
            $table->decimal('y_position', 8, 2)->default(0);
            $table->decimal('width', 8, 2)->nullable();
            $table->decimal('height', 8, 2)->nullable();
            $table->integer('font_size')->default(12);
            $table->boolean('is_locked')->default(false);
            $table->foreignId('created_by')->constrained('users');
            $table->decimal('canvas_width', 10, 2)->nullable();
            $table->decimal('canvas_height', 10, 2)->nullable();  
            $table->timestamps();
        });


        Schema::create('ministry_archived_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ministry_id')->constrained()->cascadeOnDelete();
            $table->foreignId('file_id')->constrained()->cascadeOnDelete();
            $table->foreignId('archived_by')->constrained('users');
            $table->datetime('archived_at')->default(now());
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->unique(['ministry_id', 'file_id']); 
        });


        Schema::create('ministry_closed_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ministry_id')->constrained()->cascadeOnDelete();
            $table->foreignId('file_id')->constrained()->cascadeOnDelete();
            $table->foreignId('closed_by')->constrained('users');
            $table->datetime('closed_at')->default(now());
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->unique(['ministry_id', 'file_id']); 
        });


        Schema::create('file_sequences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ministry_id')->constrained()->cascadeOnDelete();
            $table->foreignId('file_type_id')->constrained()->cascadeOnDelete();

            $table->integer('year');
            $table->integer('last_number')->default(0);
            $table->timestamps();

            $table->unique(['ministry_id', 'file_type_id', 'year']);
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
        Schema::dropIfExists('organisations');
        Schema::dropIfExists('organisation_types');
        Schema::dropIfExists('file_sequences');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('dispatches');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('ministry_archived_files');
        Schema::dropIfExists('file_assignments ');

    }
}
