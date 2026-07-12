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
        Schema::create('sync_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('device_id')->nullable()->constrained()->nullOnDelete();

            /*RESULTS*/
            $table->integer('uploaded')->default(0);
            $table->integer('downloaded')->default(0);
            $table->integer('conflicts')->default(0);

            /*STATUS*/
            $table->enum('status', ['success','partial','failed'])->default('success');
            $table->text('message')->nullable();
            $table->timestamp('synced_at')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sync_logs');
    }
};
