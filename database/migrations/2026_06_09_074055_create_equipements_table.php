<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipements', function (Blueprint $table) {
            $table->id();

            // FK
            $table->foreignId('client_id')
                  ->constrained('clients')
                  ->cascadeOnDelete();

            $table->enum('type_equipement', [
                'PC',
                'Serveur',
                'Imprimante',
                'Routeur',
                'Switch',
                'NAS',
                'Autre',
            ]);

            // Données de suivi BDD
            $table->string('marque', 80);
            $table->string('modele', 100);
            $table->string('numero_serie', 100)->nullable()->unique();
            $table->enum('etat', ['bon', 'degrade', 'hors_service'])->default('bon');
            $table->string('localisation', 150)->nullable();
            $table->date('derniere_maintenance')->nullable();

            // PDF : fiche technique constructeur, manuel, garantie
            $table->string('pdf_path', 255)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipements');
    }
};
