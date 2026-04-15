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
       Schema::create('notes', function (Blueprint $table) {
        $table->id();
        $table->text('tresc');
        $table->enum('typ', ['zwykla_notatka', 'prosba_o_ddl'])->default('zwykla_notatka');
        
        $table->unsignedBigInteger('id_zadania');
        $table->unsignedBigInteger('id_uzytkownika');

        $table->foreign('id_zadania')->references('id')->on('tasks')->onDelete('cascade');
        $table->foreign('id_uzytkownika')->references('id')->on('users')->onDelete('cascade');

        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
