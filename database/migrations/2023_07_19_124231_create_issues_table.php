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
        Schema::create('issues', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('project_id');
            $table->bigInteger('category_id');
            $table->string('subject', 500);
            $table->text('description');
            $table->integer('status');
            $table->integer('tracker');
            $table->integer('priority');
            $table->tinyInteger('is_deleted')->default(0)->comment('Deleted-1, Active-0');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issues');
    }
};
