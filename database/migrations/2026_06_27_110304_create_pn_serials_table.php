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
        Schema::create('pn_serials', function (Blueprint $table) {
            $table->id();
             $table->unsignedBigInteger('personnel_report_id')->index();
             $table->unsignedBigInteger('category_id')->index();
            $table->unsignedBigInteger('unit_id')->index();
            $table->unsignedBigInteger('sub_unit_id')->nullable()->index();
            $table->unsignedBigInteger('office_id')->nullable()->index();
            $table->unsignedBigInteger('sub_office_id')->nullable()->index();
            $table->unsignedBigInteger('rank_id')->nullable()->index();
            $table->string('serial')->nullable()->index();
            $table->string('name')->nullable()->index();
            $table->string('report_month', 7)->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pn_serials');
    }
};
