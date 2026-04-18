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
        Schema::create('stores', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('location')->nullable();
            $table->float('longitude')->nullable();
            $table->float('latitude')->nullable();
            $table->boolean('online')->default(false);
            $table->timestamps();
        });

        // stores will have many tenants
        Schema::table('users', function (Blueprint $table) {
            $table->foreignUuid('store_id')
                ->nullable()
                ->constrained('stores')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('store_id');
            $table->dropColumn('store_id');
        });
        Schema::dropIfExists('stores');
    }
};
