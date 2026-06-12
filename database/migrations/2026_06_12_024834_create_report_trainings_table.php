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
        Schema::create('report_trainings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('pn_categories')->onDelete('cascade')->index();
            $table->foreignId('unit_id')->constrained('pn_units')->onDelete('cascade')->index();
            $table->foreignId('sub_unit_id')->nullable()->constrained('pn_sub_units')->onDelete('set null')->index();
            $table->foreignId('office_id')->nullable()->constrained('pn_offices')->onDelete('set null')->index();
            $table->foreignId('sub_office_id')->nullable()->constrained('pn_sub_offices')->onDelete('set null')->index();
            $table->json('items')->nullable()->comment('JSON field to store related items');
            $table->json('activity')->nullable()->comment('JSON field to store related items');
            $table->json('result')->nullable()->comment('JSON field to store related results');
            $table->bigInteger('required')->default(0);
            $table->bigInteger('actual')->default(0);
            $table->bigInteger('orac')->default(0);
            $table->yearMonth('report_month')->index();
            $table->integer('status')->default(0)->index()->comment('0: Draft, 1: Submitted, 2: Approved, 3: Rejected');
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
        Schema::dropIfExists('report_trainings');
    }
};
