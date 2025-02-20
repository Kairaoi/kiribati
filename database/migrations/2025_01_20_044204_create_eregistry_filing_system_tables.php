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
            $table->string('name')->unique(); // e.g., Inward, Outward
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Create ministries table
        Schema::create('ministries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description');
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->constrained('users');
            $table->timestamps();
        });
        // Schema::create('divisions', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('name');
        //     $table->string('code');  // Make sure this line is present
        //     $table->text('description');
        //     $table->boolean('is_active')->default(true);
        //     $table->foreignId('ministry_id')->constrained();
        //     $table->foreignId('created_by')->constrained('users');
        //     $table->foreignId('updated_by')->constrained('users');
        //     $table->timestamps();
        // });

        // Create folders table
        Schema::create('folders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ministry_id')->constrained('ministries');
            $table->string('folder_number');  // Change to string
            $table->string('folder_name');
            $table->string('category')->nullable();
            $table->text('folder_description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['ministry_id', 'folder_number']);
        });
        

        // Create files table
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('folder_id')->constrained();
            $table->foreignId('ministry_id')->constrained();
            $table->string('file_reference')->unique()->nullable();
            $table->string('file_index')->nullable();
            $table->string('name');
            $table->string('path');
            // $table->foreignId('division_id')->nullable()->constrained('divisions');
            $table->date('receive_date');
            $table->date('letter_date');
            $table->string('letter_ref_no')->nullable()->unique();
            $table->text('details');
            $table->string('from_details_name');
            $table->string('to_details_person_name');
            $table->text('comments')->nullable();
            $table->enum('security_level', [
                'public', 
                'internal', 
                'confidential', 
                'strictly_confidential'
            ]);
            $table->enum('status', [
                'draft',
                'pending_registry_review',
                'pending_secretary_review',
                'pending_staff_action',
                'in_circulation',
                'completed',
                'archived'
            ])->default('draft');
            $table->boolean('circulation_status')->default(false);
            $table->boolean('requires_response')->default(false);
            $table->dateTime('response_deadline')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->constrained('users');
            $table->foreignId('file_type_id')->constrained('file_types');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['ministry_id']);
        });
        
        

        Schema::create('movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('file_id')->constrained();
            $table->foreignId('from_ministry_id')->constrained('ministries');
            $table->foreignId('to_ministry_id')->constrained('ministries');
            $table->foreignId('from_user_id')->constrained('users');
            $table->foreignId('to_user_id')->constrained('users');
            // $table->foreignId('from_division_id')->nullable()->constrained('divisions'); // Added this line
            // $table->foreignId('to_division_id')->nullable()->constrained('divisions');
            $table->datetime('movement_start_date');
            $table->datetime('movement_end_date')->nullable();
            $table->boolean('read_status')->default(false);
            $table->text('comments')->nullable();
            $table->string('required_action')->nullable();
            $table->string('action_taken')->nullable();
            $table->enum('status', [
                'pending_registry',
                'pending_secretary_review',
                'pending_staff_assignment',
                'assigned_to_staff',
                'in_circulation',
                'completed',
                'returned'
            ])->default('pending_registry');
            $table->boolean('is_circular')->default(false);
            $table->json('circular_recipients')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
            // $table->index(['from_ministry_id', 'to_ministry_id'], 'movements_ministry_index');
        });
        
        // Updated file_circulations table for internal ministry circulation
        Schema::create('file_circulations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('file_id')->constrained();
            $table->foreignId('ministry_id')->constrained('ministries');  // Added ministry_id
            // $table->foreignId('division_id')->nullable()->constrained('divisions');  // Added division_id
            $table->foreignId('to_user_id')->constrained('users');
            $table->foreignId('circulated_by')->constrained('users');    // Added who circulated
            $table->datetime('circulated_at')->default(now());
            $table->datetime('read_at')->nullable();                     // Changed to read_at timestamp
            $table->boolean('read_status')->default(false);
            $table->text('comments')->nullable();                        // Added comments field
            $table->boolean('requires_action')->default(false);          // Added action required flag
            $table->text('action_taken')->nullable();                    // Added action taken field
            $table->timestamps();
            
            // Add index for better query performance
            $table->index(['ministry_id',  'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('file_circulations');
     
        Schema::dropIfExists('movements');
        Schema::dropIfExists('files');
        Schema::dropIfExists('file_types');
        Schema::dropIfExists('folders');
        Schema::dropIfExists('divisions');
        Schema::dropIfExists('ministries');
    }
}
