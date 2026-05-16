<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('todos', function (Blueprint $table) {

            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('todo_list_id')->nullable()->constrained()->nullOnDelete();

            /*CONTENT*/
            $table->string('title');
            $table->longText('description')->nullable();

            /*STATUS*/
            $table->enum('status', ['todo', 'doing', 'done', 'archived'])->default('todo');

            /*PRIORITY*/
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');

            /*FLAGS*/
            $table->boolean('is_favorite')->default(false);

            /*DATES*/
            $table->date('due_date')->nullable();

            $table->timestamp('reminder_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('archived_at')->nullable();

            /*ORDERING*/
            $table->integer('position')->default(0);

            /*OFFLINE FIRST*/
            $table->boolean('is_synced')->default(false);
            $table->unsignedBigInteger('version')->default(1);
            $table->timestamp('client_updated_at')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->uuid('device_uuid')->nullable();

            $table->timestamp('deleted_at_client')->nullable();
            $table->timestamps();

            $table->softDeletes();


            $table->index('uuid');

            $table->index('status');
            $table->index('priority');
            $table->index('is_synced');
            $table->index('client_updated_at');
            $table->index('version');
            $table->index('deleted_at_client');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('todos');
    }
};
