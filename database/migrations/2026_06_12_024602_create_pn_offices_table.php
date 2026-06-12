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
            $table->foreignId('category_id')->constrained('pn_categories')->onDelete('cascade');
            $table->foreignId('unit_id')->constrained('pn_units')->onDelete('cascade');
            $table->foreignId('sub_unit_id')->nullable()->constrained('pn_sub_units')->onDelete('set null');
            $table->string('name');
            $table->string('abreviation')->nullable();
            $table->string('description')->nullable();
            $table->string('icon')->nullable();
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
        Schema::dropIfExists('pn_offices');
    }
};
