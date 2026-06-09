<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('intervention_technicien', function (Blueprint $table) {
            $table->id();

            $table->foreignId('intervention_id')
                  ->constrained('interventions')
                  ->cascadeOnDelete();
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            // Un technicien ne peut être affecté qu'une seule fois à la même intervention
            $table->unique(['intervention_id', 'user_id']);

            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('intervention_technicien');
    }
};
