<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Dodajemy pole blokady do tabeli użytkowników
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_active')->default(true);
        });

        // 2. Odcinamy klucz obcy, a potem usuwamy stare pole z zadań
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['id_przypisanego']); // <-- TA LINIJKA RATUJE SYTUACJĘ
            $table->dropColumn('id_przypisanego');
        });

        // 3. Tworzymy tabelę łączącą Zadania z Użytkownikami (Wiele-do-Wielu)
        Schema::create('task_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_user');

        Schema::table('tasks', function (Blueprint $table) {
            // Przywracanie starego stanu w razie cofnięcia migracji
            $table->unsignedBigInteger('id_przypisanego')->nullable();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
};