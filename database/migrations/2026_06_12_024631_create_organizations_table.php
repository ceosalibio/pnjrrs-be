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
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id')->index();
            $table->unsignedBigInteger('unit_id')->index();
            $table->unsignedBigInteger('sub_unit_id')->nullable()->index();
            $table->unsignedBigInteger('office_id')->nullable()->index();
            $table->unsignedBigInteger('sub_office_id')->nullable()->index();
            $table->json('items')->nullable()->comment('JSON field to store related items');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraints
            // $table->foreign('category_id', 'fk_organizations_category_id')
            //     ->references('id')->on('pn_categories')->onDelete('cascade');
            // $table->foreign('unit_id', 'fk_organizations_unit_id')
            //     ->references('id')->on('pn_units')->onDelete('cascade');
            // $table->foreign('sub_unit_id', 'fk_organizations_sub_unit_id')
            //     ->references('id')->on('pn_sub_units')->onDelete('set null');
            // $table->foreign('office_id', 'fk_organizations_office_id')
            //     ->references('id')->on('pn_offices')->onDelete('set null');
            // $table->foreign('sub_office_id', 'fk_organizations_sub_office_id')
            //     ->references('id')->on('pn_sub_offices')->onDelete('set null');
            // $table->foreign('created_by', 'fk_organizations_created_by')
            //     ->references('id')->on('users')->onDelete('set null');
            // $table->foreign('updated_by', 'fk_organizations_updated_by')
            //     ->references('id')->on('users')->onDelete('set null');

            // Indexes for foreign keys
            // $table->index('category_id');
            // $table->index('unit_id');
            // $table->index('sub_unit_id');
            // $table->index('office_id');
            // $table->index('sub_office_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
