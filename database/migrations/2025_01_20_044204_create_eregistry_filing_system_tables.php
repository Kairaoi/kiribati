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
            $table->boolean('is_active')->default(true);
            $table->foreignId('organisation_type_id')->constrained('organisation_types');
            $table->foreignId('identity_organisation_id')->constrained('identity_organisations');
            $table->text('location')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->constrained('users');
        });

    
        Schema::create('external_partners', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('location')->nullable();
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
            $table->boolean('is_active')->default(true);
            $table->foreignId('ministry_id')->constrained('ministries');
            $table->timestamps();
        });


        // Create files table
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string('reference_no')->unique();
            $table->foreignId('ministry_id')->nullable()->constrained('ministries'); //who logs the file in the system
            $table->foreignId('from_division_id')->nullable()->constrained('divisions');
            $table->morphs('source');
            // $table->enum('source_channel', ['system', 'manual', 'external']);
            $table->foreignId('file_type_id')->constrained('file_types');
            $table->foreignId('category_id')->nullable()->constrained('categories');
            $table->string('subject');
            $table->string('main_file_path');
            $table->json('additional_file_paths')->nullable(); 
            $table->string('main_file_name')->nullable(); 
            $table->date('letter_date'); 
            $table->date('due_date')->nullable();
            $table->string('status')->nullable();
            $table->dateTime('response_deadline')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_archived')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
           
        });


        //pivot table
        Schema::create('file_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('file_id')->constrained('files')->onDelete('cascade');
            $table->foreignId('ministry_id')->constrained('ministries')->onDelete('cascade'); 
            $table->enum('status', [
                'Pending Review',
                'Pending Dispatch',
                'Pending Circulation',
                'Dispatched',
                'Circulated',
                'Reviewed',
                'Completed',
                'Filed',
                'Archive'
            ]);
            $table->timestamps();
        });


        Schema::create('dispatches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('file_id')->constrained();
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
            $table->foreignId('circulated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->datetime('circulated_at')->default(now());
            $table->enum('status', ['Pending Circulation', 
                                    'Pending Review', 
                                    'Reviewed', 
                                    'Filed', 
                                    'Archived']);
            $table->foreignId('updated_by')->nullable()->constrained('users');            
            $table->datetime('read_at')->nullable();                     
            $table->boolean('read_status')->default(false);
            $table->text('review_comment')->nullable();
            $table->foreignId('to_review_file')->nullable()->constrained('users'); 
            $table->datetime('date_reviewed')->nullable();
            $table->boolean('requires_action')->default(false);    
            $table->text('action_taken')->nullable();                  
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
            $table->enum('status', ['pending', 'accepted', 'declined'])->default('pending'); // Added status field
            // for reassignment tracking
            $table->foreignId('reassigned_from')->nullable()->constrained('users')->nullOnDelete();
            $table->datetime('accepted_at')->nullable();
            $table->timestamps();
        });


        Schema::create('ministry_archived_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ministry_id')->constrained()->cascadeOnDelete();
            $table->foreignId('file_id')->constrained()->cascadeOnDelete();
            $table->foreignId('archived_by')->constrained('users');
            $table->datetime('archived_at')->default(now());
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
        Schema::dropIfExists('organisation_archived_files');
        Schema::dropIfExists('file_assignments ');

    }
}
