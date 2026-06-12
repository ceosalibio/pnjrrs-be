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
        Schema::create('report_equipment', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id')->index();
            $table->unsignedBigInteger('unit_id')->index();
            $table->unsignedBigInteger('sub_unit_id')->nullable()->index();
            $table->unsignedBigInteger('office_id')->nullable()->index();
            $table->unsignedBigInteger('sub_office_id')->nullable()->index();
            $table->json('items')->nullable()->comment('JSON field to store related items');
            $table->json('activity')->nullable()->comment('JSON field to store related items');
            $table->json('result')->nullable()->comment('JSON field to store related results');
            $table->bigInteger('required')->default(0);
            $table->bigInteger('actual')->default(0);
            $table->string('report_month', 7)->index();
            $table->integer('status')->default(0)->index()->comment('0: Draft, 1: Submitted, 2: Approved, 3: Rejected');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // $table->foreign('category_id', 'fk_report_equipment_category_id')
            //     ->references('id')->on('pn_categories')->onDelete('cascade');
            // $table->foreign('unit_id', 'fk_report_equipment_unit_id')
            //     ->references('id')->on('pn_units')->onDelete('cascade');
            // $table->foreign('sub_unit_id', 'fk_report_equipment_sub_unit_id')
            //     ->references('id')->on('pn_sub_units')->onDelete('set null');
            // $table->foreign('office_id', 'fk_report_equipment_office_id')
            //     ->references('id')->on('pn_offices')->onDelete('set null');
            // $table->foreign('sub_office_id', 'fk_report_equipment_sub_office_id')
            //     ->references('id')->on('pn_sub_offices')->onDelete('set null');
            // $table->foreign('created_by', 'fk_report_equipment_created_by')
            //     ->references('id')->on('users')->onDelete('set null');
            // $table->foreign('updated_by', 'fk_report_equipment_updated_by')
            //     ->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_equipment');
    }
};
