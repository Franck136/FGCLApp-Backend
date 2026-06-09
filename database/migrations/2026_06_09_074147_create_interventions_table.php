<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('interventions', function (Blueprint $table) {
            $table->id();

            $table->string('reference', 50)->unique(); // ex: INT-2026-041

            // FK
            $table->foreignId('client_id')
                  ->constrained('clients')
                  ->cascadeOnDelete();
            $table->foreignId('contrat_id')
                  ->nullable()
                  ->constrained('contrats')
                  ->nullOnDelete();
            $table->foreignId('created_by')
                  ->constrained('users')
                  ->cascadeOnDelete();

            // Classification
            $table->enum('type_intervention', [
                'preventive',
                'corrective',
                'installation',
                'audit',
            ]);
            $table->enum('priorite', ['haute', 'normale', 'basse'])->default('normale');
            $table->enum('statut', [
                'planifiee',
                'en_cours',
                'terminee',
                'annulee',
            ])->default('planifiee');

            // Dates — pilotage BDD
            $table->dateTime('date_planifiee');
            $table->dateTime('date_debut_reelle')->nullable();
            $table->dateTime('date_fin_reelle')->nullable();
            $table->unsignedInteger('duree_minutes')->nullable(); // calculé automatiquement

            // Coût — pilotage facturation
            $table->decimal('cout', 10, 2)->nullable();

            // PDF : rapport détaillé (constats, solutions, photos, signature client)
            $table->string('pdf_rapport_path', 255)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interventions');
    }
};
