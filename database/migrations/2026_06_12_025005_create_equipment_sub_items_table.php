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
        Schema::create('equipment_sub_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('main_item_id')->index();
            $table->string('name')->index();
            $table->string('description')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // $table->foreign('main_item_id', 'fk_equipment_sub_items_main_item_id')
            //     ->references('id')->on('equipment_main_items')->onDelete('cascade');
            // $table->foreign('created_by', 'fk_equipment_sub_items_created_by')
            //     ->references('id')->on('users')->onDelete('set null');
            // $table->foreign('updated_by', 'fk_equipment_sub_items_updated_by')
            //     ->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_sub_items');
    }
};
