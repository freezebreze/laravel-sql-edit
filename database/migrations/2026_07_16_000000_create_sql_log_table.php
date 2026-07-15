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
        if (Schema::hasTable('sql_log')) {
            return;
        }

        Schema::create('sql_log', function (Blueprint $table) {
            $table->bigInteger('id', autoIncrement: true);
            $table->unsignedBigInteger('user_id');
            $table->text('error_message')->nullable();
            $table->timestamp('create_time');
            $table->longText('sql');
            $table->unique('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sql_log');
    }
};
