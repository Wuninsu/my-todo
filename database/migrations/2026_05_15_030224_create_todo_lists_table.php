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
        Schema::create('todo_lists', function (Blueprint $table) {

            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            /*CONTENT*/
            $table->string('name');
            $table->string('color')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_default')->default(false);

            /*OFFLINE FIRST*/
            $table->boolean('is_synced')->default(false);
            $table->unsignedBigInteger('version')->default(1);
            $table->timestamp('client_updated_at')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->uuid('device_uuid')->nullable();
            $table->timestamp('deleted_at_client')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('todo_lists');
    }
};
