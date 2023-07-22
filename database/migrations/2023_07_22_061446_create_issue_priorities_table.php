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
        Schema::create('issue_priorities', function (Blueprint $table) {
            $table->id();
            $table->string('name', 155);
            $table->tinyInteger('is_default')->default(0)->comment('default-1');
            $table->tinyInteger('active')->default(1)->comment('active-1');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issue_priorities');
    }
};
