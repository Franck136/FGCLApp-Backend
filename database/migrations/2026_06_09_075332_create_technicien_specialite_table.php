<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('technicien_specialite', function (Blueprint $table) {
            $table->id();

            $table->foreignId('technicien_id')
                  ->constrained('techniciens')
                  ->cascadeOnDelete();

            $table->foreignId('specialite_id')
                  ->constrained('specialites')
                  ->cascadeOnDelete();

            // Un technicien ne peut avoir qu'une fois la même spécialité
            $table->unique(['technicien_id', 'specialite_id']);

            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('technicien_specialite');
    }
};
