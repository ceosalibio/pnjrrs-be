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
        Schema::create('item_assessment_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('report_id')->index();
            $table->string('report_type')->index();
            $table->string('question')->index();
            $table->string('answer');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // $table->foreign('created_by', 'fk_item_assessment_questions_created_by')
            //     ->references('id')->on('users')->onDelete('set null');
            // $table->foreign('updated_by', 'fk_item_assessment_questions_updated_by')
            //     ->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_assessment_questions');
    }
};
