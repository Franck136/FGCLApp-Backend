<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('techniciens', function (Blueprint $table) {
            $table->id();

            // FK : 1-1 avec users
            $table->foreignId('user_id')
                  ->unique()
                  ->constrained('users')
                  ->cascadeOnDelete();

            // Données de pilotage BDD
            // $table->string('specialite', 150); // ex: "reseau,hardware,software"
            $table->string('zone_intervention', 150);
            $table->enum('type_contrat_travail', ['CDI', 'CDD', 'stagiaire', 'prestataire']);
            $table->date('date_embauche')->nullable();
            $table->boolean('disponible')->default(true);

            // PDF : contrat de travail complet (salaire, clauses, CV)
            $table->string('pdf_contrat_path', 255)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('techniciens');
    }
};
