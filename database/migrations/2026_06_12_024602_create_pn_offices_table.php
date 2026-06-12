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
        Schema::create('pn_offices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id')->index();
            $table->unsignedBigInteger('unit_id')->index();
            $table->unsignedBigInteger('sub_unit_id')->nullable()->index();
            $table->string('name')->index();
            $table->string('abreviation')->nullable();
            $table->string('address')->nullable();
            $table->string('description')->nullable();
            $table->string('icon')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // $table->foreign('category_id', 'fk_pn_offices_category_id')
            //     ->references('id')->on('pn_categories')->onDelete('cascade');
            // $table->foreign('unit_id', 'fk_pn_offices_unit_id')
            //     ->references('id')->on('pn_units')->onDelete('cascade');
            // $table->foreign('sub_unit_id', 'fk_pn_offices_sub_unit_id')
            //     ->references('id')->on('pn_sub_units')->onDelete('set null');
            // $table->foreign('created_by', 'fk_pn_offices_created_by')
            //     ->references('id')->on('users')->onDelete('set null');
            // $table->foreign('updated_by', 'fk_pn_offices_updated_by')
            //     ->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pn_offices');
    }
};
