<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();

            // Destinataire
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->enum('type', [
                'echeance_contrat',
                'nouvelle_intervention',
                'rappel',
                'info',
            ]);

            $table->string('titre', 150);
            $table->text('message');
            $table->boolean('lu')->default(false);

            // Références optionnelles selon le type
            $table->foreignId('intervention_id')
                  ->nullable()
                  ->constrained('interventions')
                  ->nullOnDelete();

            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
