<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contrats', function (Blueprint $table) {
            $table->id();

            // FK
            $table->foreignId('client_id')
                  ->constrained('clients')
                  ->cascadeOnDelete();
            $table->foreignId('commercial_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->string('reference', 50)->unique();
            $table->enum('type_contrat', [
                'maintenance_preventive',
                'maintenance_corrective',
                'infogerance',
                'installation',
            ]);

            // Dates clés — données de pilotage
            $table->date('date_signature');
            $table->date('date_debut');
            $table->date('date_fin');
            $table->unsignedInteger('duree_mois'); // calculé : date_fin - date_debut

            $table->boolean('renouvellement_auto')->default(false);
            $table->enum('statut', ['actif', 'suspendu', 'expire', 'resilie'])->default('actif');

            // PDF du contrat (clauses, montants, signatures → dans le fichier)
            $table->string('pdf_path', 255)->nullable();
            $table->unsignedSmallInteger('pdf_version')->default(1);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contrats');
    }
};
