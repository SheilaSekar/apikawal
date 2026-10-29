<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable()->unique();
            $table->foreignId('reporter_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('reports')->nullOnDelete();
            $table->double('lat');
            $table->double('lng');
            $table->string('land_type');
            $table->json('signs');
            $table->float('estimated_area_ha')->nullable();
            $table->text('description')->nullable();
            $table->string('status')->default('baru');
            $table->unsignedSmallInteger('priority_score')->default(0);
            $table->string('priority_level')->default('rendah');
            $table->json('priority_breakdown')->nullable();
            $table->boolean('is_simulated')->default(false);
            $table->boolean('needs_moderation')->default(false);
            $table->timestamps();
            $table->index(['status', 'priority_level', 'created_at']);
            $table->index(['lat', 'lng']);
            $table->index('parent_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
