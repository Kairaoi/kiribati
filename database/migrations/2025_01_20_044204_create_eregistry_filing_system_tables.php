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
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');
            $table->timestamps();
        });

        // Create folders table
        Schema::create('folders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ministry_id')->constrained('ministries'); // Foreign key to ministries table
            $table->integer('folder_number'); // Folder number specific to the ministry (e.g., 1, 2, 3, etc.)
            $table->string('folder_name'); // Name of the folder (e.g., Circular, Miscellaneous, etc.)
            $table->string('category')->nullable(); // Optional category for grouping folders (e.g., Meetings, Reports)
            $table->text('folder_description')->nullable(); // Detailed description of the folder’s content
            $table->boolean('is_active')->default(true); // Whether the folder is active or archived
            $table->foreignId('created_by')->constrained('users'); // User who created the folder
            $table->foreignId('updated_by')->constrained('users'); // User who last updated the folder
            $table->timestamps();
            $table->unique(['ministry_id', 'folder_number']); // Ensuring unique folder numbers per ministry
        });

        // Create divisions table
        Schema::create('divisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ministry_id')->constrained();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description');
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->constrained('users');
            $table->timestamps();
            $table->index('ministry_id');
            $table->index('created_by');
        });

        // Create files table
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('folder_id')->constrained();
            $table->foreignId('ministry_id')->constrained();
            $table->foreignId('division_id')->constrained();
            $table->string('name');
            $table->string('path');
            $table->date('receive_date');
            $table->date('letter_date');
            $table->string('letter_ref_no');
            $table->text('details');
            $table->string('from_details_name');
            $table->string('to_details_person_name');
            $table->text('comments');
            $table->enum('security_level', ['public', 'internal', 'confidential', 'strictly_confidential']);
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->constrained('users');
            $table->foreignId('file_type_id')->constrained('file_types');  // Foreign key for file type
            $table->timestamps();
            $table->index(['ministry_id', 'division_id']);
        });

        // Create out_ward_files table
        Schema::create('in_ward_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('folder_id')->constrained();
            $table->foreignId('ministry_id')->constrained(); //Ministry owning the outward file
            $table->foreignId('division_id')->constrained();
            $table->string('name');
            $table->string('path')->nullable();
            $table->date('send_date');
            $table->date('letter_date');
            $table->string('letter_ref_no');
            $table->text('details');
            $table->string('from_details_name');
            $table->string('to_details_name');
            $table->enum('security_level', ['public', 'internal', 'confidential', 'strictly_confidential']);
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->constrained('users');
            $table->foreignId('file_type_id')->constrained('file_types');  // Foreign key for file type
            $table->timestamps();
            $table->index(['ministry_id', 'division_id']);
        });

        // Create out_ward_files table
        Schema::create('out_ward_files', function (Blueprint $table) {
            $table->id();
            // $table->foreignId('folder_id')->constrained();
            $table->foreignId('ministry_id')->constrained(); //Ministry owning the outward file
            $table->foreignId('division_id')->constrained();
            $table->string('name');
            $table->string('path')->nullable();
            $table->date('send_date');
            $table->date('letter_date');
            $table->string('letter_ref_no');
            $table->text('details');
            $table->string('from_details_name');
            $table->string('to_details_name');
            $table->enum('security_level', ['public', 'internal', 'confidential', 'strictly_confidential']);
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->constrained('users');
            $table->foreignId('file_type_id')->constrained('file_types');  // Foreign key for file type
            $table->timestamps();
            $table->index(['ministry_id', 'division_id']);
            $table->string('recipient_display')->nullable(); // Store 'All' or NULL
        });

        // Create file_ministry table (pivot table) - track which ministries a file is sent to
        Schema::create('out_ward_file_ministry', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outward_file_id')->constrained('out_ward_files')->onDelete('cascade');
            $table->foreignId('ministry_id')->constrained()->onDelete('cascade');
            $table->enum('role', ['owner', 'recipient']); // Identifies if the ministry owns or receives the file
            $table->timestamps();
        });

        Schema::create('in_ward_file_ministry', function (Blueprint $table) {
            $table->id();
            $table->foreignId('in_ward_file_id')->constrained()->onDelete('cascade');  // Foreign key to inward_files table
            $table->foreignId('ministry_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });


        // Create file_access table
        Schema::create('file_access', function (Blueprint $table) {
            $table->id();
            $table->foreignId('file_id')->constrained();
            $table->foreignId('ministry_id')->constrained();
            $table->foreignId('division_id')->constrained();
            $table->enum('access_type', ['view', 'edit', 'full']);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->constrained('users');
            $table->timestamps();
            $table->index(['ministry_id', 'division_id']);
        });

        // Create movements table
        Schema::create('movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('file_id')->constrained();
            $table->foreignId('from_ministry_id')->constrained('ministries');
            $table->foreignId('to_ministry_id')->constrained('ministries');
            $table->foreignId('from_division_id')->constrained('divisions');
            $table->foreignId('to_division_id')->constrained('divisions');
            $table->foreignId('from_user_id')->constrained('users');
            $table->foreignId('to_user_id')->constrained('users');
            $table->datetime('movement_start_date');
            $table->datetime('movement_end_date');
            $table->boolean('read_status')->default(false);
            $table->text('comments');
            $table->enum('status', ['pending', 'in_progress', 'completed']);
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->constrained('users');
            $table->timestamps();

            // Provide a custom index name to avoid the long default name
            $table->index(
                ['from_ministry_id', 'to_ministry_id', 'from_division_id', 'to_division_id'],
                'movements_ministry_division_index'  // Custom short index name
            );
        });
    }

    public function down()
    {
        Schema::dropIfExists('movements');
        Schema::dropIfExists('file_access');
        Schema::dropIfExists('out_ward_files');
        Schema::dropIfExists('file_ministry');
        Schema::dropIfExists('files');
        Schema::dropIfExists('file_types');
        Schema::dropIfExists('folders');
        Schema::dropIfExists('divisions');
        Schema::dropIfExists('ministries');
    }
}
