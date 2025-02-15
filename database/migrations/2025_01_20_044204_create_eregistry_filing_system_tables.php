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
        Schema::create('divisions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code');  // Make sure this line is present
            $table->text('description');
            $table->boolean('is_active')->default(true);
            $table->foreignId('ministry_id')->constrained();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->constrained('users');
            $table->timestamps();
        });

        // Create folders table
        Schema::create('folders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ministry_id')->constrained('ministries');
            $table->integer('folder_number');
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
            $table->string('file_reference')->unique()->nullable()->default('default_value');
            $table->string('file_index')->nullable(); // Remove the GENERATED ALWAYS AS constraint
            $table->string('name');
            $table->string('path');
            $table->foreignId('division_id')->nullable()->constrained('divisions');
            $table->date('receive_date');
            $table->date('letter_date');
            $table->string('letter_ref_no')->nullable()->unique();
            $table->text('details');
            $table->string('from_details_name');
            $table->string('to_details_person_name');
            $table->text('comments')->nullable();
            $table->enum('security_level', ['public', 'internal', 'confidential', 'strictly_confidential']);
            $table->enum('status', ['draft', 'pending_review', 'approved', 'archived'])->default('draft');
            $table->boolean('circulation_status')->default(false);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->constrained('users');
            $table->foreignId('file_type_id')->constrained('file_types');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['ministry_id']);
        });
        
        

        // Create file movements table
        Schema::create('movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('file_id')->constrained();
            $table->foreignId('from_ministry_id')->constrained('ministries');
            $table->foreignId('to_ministry_id')->constrained('ministries');
            $table->foreignId('from_user_id')->constrained('users');
            $table->foreignId('to_user_id')->constrained('users');
            $table->foreignId('to_division_id')->nullable()->constrained('divisions'); // New field for internal movements
            $table->datetime('movement_start_date');
            $table->datetime('movement_end_date')->nullable();
            $table->boolean('read_status')->default(false);
            $table->text('comments')->nullable();
            $table->string('required_action')->nullable();
            $table->string('action_taken')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed']);
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['from_ministry_id', 'to_ministry_id'], 'movements_ministry_index');
        });

       
        // Create file versioning table
        Schema::create('file_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('file_id')->constrained();
            $table->string('version_number');
            $table->string('path');
            $table->foreignId('updated_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('file_versions');
        Schema::dropIfExists('file_permissions');
        Schema::dropIfExists('movements');
        Schema::dropIfExists('files');
        Schema::dropIfExists('file_types');
        Schema::dropIfExists('folders');
        Schema::dropIfExists('divisions');
        Schema::dropIfExists('ministries');
    }
}
