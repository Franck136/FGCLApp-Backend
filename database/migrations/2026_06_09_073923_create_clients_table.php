<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('raison_sociale', 150);
            $table->string('forme_juridique', 50)->nullable();
            $table->string('numero_contribuable', 50)->nullable()->unique();
            $table->string('secteur_activite', 100);
            $table->text('adresse');
            $table->string('ville', 80);
            $table->string('region', 80)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('telephone', 20);

            // Contact principal
            $table->string('nom_responsable', 150);
            $table->string('poste_responsable', 100)->nullable();
            $table->string('telephone_responsable', 20)->nullable();
            $table->string('email_responsable', 150)->nullable();

            $table->enum('statut', ['actif', 'inactif', 'suspendu'])->default('actif');
            $table->date('date_debut_relation')->nullable();

            // FK : commercial assigné
            $table->foreignId('commercial_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
