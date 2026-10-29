<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('abuse_flags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained()->cascadeOnDelete();
            $table->foreignId('flagged_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('reason');
            $table->boolean('resolved')->default(false);
            $table->timestamps();
            $table->index(['resolved', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('abuse_flags');
    }
};
