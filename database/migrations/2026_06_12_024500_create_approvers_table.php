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
        Schema::create('approvers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('report_id')->index();
            $table->string('report_type')->index();
            $table->string('sign_type')->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('approved_status')->nullable();
            $table->string('disapproved_status')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approvers');
    }
};
