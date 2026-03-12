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
            $table->string('name')->unique(); // e.g., Letter, General, MoU
            $table->text('description')->nullable();
            $table->string('code')->unique(); // Unique code for each file type
            $table->timestamps();
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


        // Create SOE table
        Schema::create('organisations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique()->nullable();
            $table->text('location')->nullable();
            $table->foreignId('review_officer_id')->nullable()->constrained('users');
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->constrained('users');
            $table->foreignId('organisation_type_id')->constrained('organisation_types');
            $table->timestamps();

            $table->unique(['name', 'organisation_type_id']);
        });


        Schema::create('divisions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location');
            $table->boolean('is_active')->default(true);
            $table->foreignId('organisation_id')->constrained('organisations');
            $table->timestamps();
        });


        // Create files table
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organisation_id')->nullable()->constrained();
            $table->foreignId('division_id')->nullable()->constrained('divisions');
            $table->string('file_reference')->unique()->nullable();
            $table->foreignId('file_type_id')->constrained('file_types');
            $table->foreignId('category_id')->constrained('categories');
            $table->string('subject');
            $table->string('main_file_path'); //File path
            $table->json('additional_file_paths')->nullable(); // JSON column to store paths of additional files
            $table->string('main_file_name')->nullable(); // Original file name for download purposes
            $table->enum('initial_type', ['dispatch','internal']);
            $table->date('letter_date'); 
            $table->string('letter_ref_no')->nullable()->unique();
            $table->string('status')->nullable();
            $table->dateTime('response_deadline')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_archived')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['organisation_id']);
        });

        
        Schema::create('file_sequences', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('organisation_id');
            $table->integer('year');
            $table->integer('last_number')->default(0);
            $table->unique(['organisation_id', 'year']);
        });


        //pivot table
        Schema::create('file_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('file_id')->constrained('files')->onDelete('cascade');
            $table->foreignId('organisation_id')->constrained('organisations')->onDelete('cascade'); // But only minisries will be used here
            $table->enum('status', [
                'Pending Review',
                'Pending Dispatch',
                'Pending Circulation', //for internal files
                'Dispatched',
                'Circulated',
                'Assigned',
                'Completed'
            ]);
            $table->timestamps();
        });


        Schema::create('dispatches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('file_id')->constrained();
            $table->foreignId('from_organisation_id')->constrained('organisations');
            $table->foreignId('from_division_id')->constrained('divisions');
            $table->foreignId('dispatched_by')->nullable()->constrained('users');
            $table->datetime('dispatch_date')->nullable();
            $table->boolean('read_status')->default(false);
            $table->text('comments')->nullable();
            $table->string('required_action')->nullable();
            $table->string('action_taken')->nullable();
            // $table->json('circular_recipients')->nullable();
            $table->foreignId('updated_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();

        });

        // Updated file_circulations table for internal organisation circulation
        Schema::create('file_circulations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('file_id')->constrained();
            $table->foreignId('from_organisation_id')->constrained('organisations');  // Added organisation_id
            $table->foreignId('to_organisation_id')->constrained('organisations');  // Added organisation_id
            $table->foreignId('circulated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->datetime('circulated_at')->default(now());
            $table->foreignId('updated_by')->nullable()->constrained('users');            
            $table->foreignId('assigned_division_id')->nullable()->constrained('divisions');  // Added division_id
            $table->foreignId('to_review_file')->nullable()->constrained('users');                      //Secretary or Officer in Charge
            // $table->foreignId('assigned_officer')->nullable()->constrained('users'); 
            $table->datetime('read_at')->nullable();                     // Changed to read_at timestamp
            $table->boolean('read_status')->default(false);
            $table->text('review_comment')->nullable();                        // Added comments field
            $table->boolean('requires_action')->default(false);          // Added action required flag
            $table->text('action_taken')->nullable();                    // Added action taken field
            $table->timestamps();
            // Add index for better query performance
            // $table->index(['organisation_id',  'user_id']);
        });


        // Create file_circulation_officer table for tracking officers assigned to file circulations
        Schema::create('file_circulation_officer', function (Blueprint $table) {
            $table->id();
            $table->foreignId('file_circulation_id')->constrained();
            $table->foreignId('officer_id')->constrained('users');
            $table->enum('status', ['pending', 'completed'])->default('pending');
            $table->date('date_assigned')->default(now());
            $table->date('date_completed')->nullable(); // Date when the officer completed their action or acknowledge receipt of file 
            $table->timestamps();
        });


        Schema::create('organisation_archived_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organisation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('file_id')->constrained()->cascadeOnDelete();
            $table->foreignId('archived_by')->constrained('users');
            $table->datetime('archived_at')->default(now());
            $table->timestamps();

            $table->unique(['organisation_id', 'file_id']); // prevent duplicate archive

        });


        // Schema::create('audit_logs', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('user_id')->constrained('users'); // User who performed the action
        //     $table->string('action'); // e.g., 'create', 'update', 'delete'
        //     $table->ipAddress('ip_address')->nullable(); // IP address of the user
        //     $table->string('user_agent')->nullable(); // from what device/browser the action was performed
        //     $table->morphs('auditable'); // Polymorphic relation to the model being audited
        //     $table->json('old_values')->nullable(); // Old values before the action
        //     $table->json('new_values')->nullable(); // New values after the action
        //     $table->timestamps();
        // });
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
    }
}
