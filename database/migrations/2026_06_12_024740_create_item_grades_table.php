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
        Schema::create('item_grades', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('division_id')->index();
            $table->string('name')->index();
            $table->string('description')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // $table->foreign('created_by', 'fk_item_grades_created_by')
            //     ->references('id')->on('users')->onDelete('set null');
            // $table->foreign('updated_by', 'fk_item_grades_updated_by')
            //     ->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_grades');
    }
};
