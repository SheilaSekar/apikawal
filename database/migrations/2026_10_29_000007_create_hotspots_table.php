<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotspots', function (Blueprint $table) {
            $table->id();
            $table->double('lat');
            $table->double('lng');
            $table->unsignedSmallInteger('confidence')->nullable();
            $table->dateTime('detected_at')->index();
            $table->string('source');
            $table->boolean('is_simulated')->default(false);
            $table->index(['lat', 'lng']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotspots');
    }
};
