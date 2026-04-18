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
        Schema::create('companies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('owner_id')->constrained('users', 'id')->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();
        });

        // add a foreign key to the store so it belongs to a company
        Schema::table('stores', function (Blueprint $table) {
            $table->foreignUuid('company_id')->constrained('companies', 'id')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropForeign('company_id');
            $table->dropColumn('company_id');
        });
        Schema::dropIfExists('companies');
    }
};
