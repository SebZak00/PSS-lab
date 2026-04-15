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
        Schema::create('tasks', function (Blueprint $table) {
        $table->id();
        $table->string('tytul', 100);
        $table->text('opis')->nullable();
        $table->enum('status', ['nowe', 'w_toku', 'zrobione'])->default('nowe');
        $table->date('termin_wykonania')->nullable();
        
        $table->unsignedBigInteger('id_przypisanego')->nullable();
        $table->unsignedBigInteger('id_tworcy')->nullable();
        
        $table->foreign('id_przypisanego')->references('id')->on('users')->onDelete('set null');
        $table->foreign('id_tworcy')->references('id')->on('users')->onDelete('set null');

        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
