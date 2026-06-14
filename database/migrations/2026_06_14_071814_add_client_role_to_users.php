<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Modifier l'enum role pour ajouter 'client'
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','commercial','technicien','client') NOT NULL");

        // 2. Ajouter client_id pour lier un user à son entreprise cliente
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('client_id')
                  ->nullable()
                  ->after('statut')
                  ->constrained('clients')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->dropColumn('client_id');
        });

        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','commercial','technicien') NOT NULL");
    }
};
