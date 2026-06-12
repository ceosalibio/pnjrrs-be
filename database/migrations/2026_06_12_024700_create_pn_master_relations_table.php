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
        Schema::create('pn_master_relations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id')->index();
            $table->unsignedBigInteger('unit_id')->index();
            $table->unsignedBigInteger('sub_unit_id')->nullable()->index();
            $table->unsignedBigInteger('office_id')->nullable()->index();
            $table->unsignedBigInteger('sub_office_id')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();
            
            // $table->foreign('category_id', 'fk_pn_master_relations_category_id')
            //     ->references('id')->on('pn_categories')->onDelete('cascade');
            // $table->foreign('unit_id', 'fk_pn_master_relations_unit_id')
            //     ->references('id')->on('pn_units')->onDelete('cascade');
            // $table->foreign('sub_unit_id', 'fk_pn_master_relations_sub_unit_id')
            //     ->references('id')->on('pn_sub_units')->onDelete('set null');
            // $table->foreign('office_id', 'fk_pn_master_relations_office_id')
            //     ->references('id')->on('pn_offices')->onDelete('set null');
            // $table->foreign('sub_office_id', 'fk_pn_master_relations_sub_office_id')
            //     ->references('id')->on('pn_sub_offices')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pn_master_relations');
    }
};
