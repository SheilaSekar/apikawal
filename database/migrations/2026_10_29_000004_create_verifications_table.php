<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('vote');
            $table->unsignedTinyInteger('weight');
            $table->float('distance_km')->nullable();
            $table->foreignId('source_report_id')->nullable()->constrained('reports')->nullOnDelete();
            $table->timestamps();
            $table->unique(['report_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verifications');
    }
};
