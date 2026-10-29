<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('points_of_interest', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('kind');
            $table->double('lat');
            $table->double('lng');
            $table->boolean('is_simulated')->default(false);
        });
        Schema::create('grid_cells', function (Blueprint $table) {
            $table->id();
            $table->string('cell_key')->unique();
            $table->double('min_lat');
            $table->double('min_lng');
            $table->double('max_lat');
            $table->double('max_lng');
            $table->unsignedInteger('hotspot_count')->default(0);
            $table->unsignedInteger('report_count')->default(0);
            $table->float('score')->default(0);
            $table->unsignedInteger('rank_position')->nullable();
            $table->string('prone_level')->nullable();
            $table->timestamps();
        });
        Schema::create('priority_configs', function (Blueprint $table) {
            $table->string('name')->primary();
            $table->float('value');
            $table->string('label');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('priority_configs');
        Schema::dropIfExists('grid_cells');
        Schema::dropIfExists('points_of_interest');
    }
};
