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
        Schema::create('pn_units', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id')->index();
            $table->string('name')->index();
            $table->string('abreviation')->nullable();
            $table->string('address')->nullable();
            $table->string('description')->nullable();
            $table->string('icon')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // $table->foreign('category_id', 'fk_pn_units_category_id')
            //     ->references('id')->on('pn_categories')->onDelete('cascade');
            // $table->foreign('created_by', 'fk_pn_units_created_by')
            //     ->references('id')->on('users')->onDelete('set null');
            // $table->foreign('updated_by', 'fk_pn_units_updated_by')
            //     ->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pn_units');
    }
};
