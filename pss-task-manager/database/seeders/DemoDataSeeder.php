<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Task;
use App\Models\Note;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Pobieramy role z bazy
        $rolaTL = Role::where('nazwa', 'TeamLeader')->first();
        $rolaPracownik = Role::where('nazwa', 'Pracownik')->first();

        if (!$rolaTL || !$rolaPracownik) {
            $this->command->error('Brak ról w bazie! Upewnij się, że masz role TeamLeader i Pracownik w tabeli roles.');
            return;
        }

        // 2. Tworzymy dwóch TeamLeaderów
        $tl1 = User::create([
            'name' => 'Anna Kowalska (TL)', 
            'email' => 'anna@firma.pl', 
            'password' => Hash::make('password123')
        ]);
        $tl1->roles()->attach($rolaTL->id);

        $tl2 = User::create([
            'name' => 'Piotr Nowak (TL)', 
            'email' => 'piotr@firma.pl', 
            'password' => Hash::make('password123')
        ]);
        $tl2->roles()->attach($rolaTL->id);

        // 3. Tworzymy 5 Pracowników
        $pracownicy = [];
        for ($i = 1; $i <= 5; $i++) {
            $pracownik = User::create([
                'name' => "Pracownik $i", 
                'email' => "pracownik$i@firma.pl", 
                'password' => Hash::make('password123')
            ]);
            $pracownik->roles()->attach($rolaPracownik->id);
            $pracownicy[] = $pracownik;
        }

        // 4. Tworzymy 10 Zadań
        $statusy = ['Nowe', 'W trakcie', 'Zakończone'];
        $liderzy = [$tl1, $tl2];

        for ($i = 1; $i <= 10; $i++) {
            $zadanie = Task::create([
                'tytul' => "Wdrożenie modułu nr $i",
                'opis' => "To jest automatycznie wygenerowane zadanie pokazowe nr $i. Należy przeanalizować dokumentację i zaimplementować rozwiązanie w systemie.",
                'status' => $statusy[array_rand($statusy)],
                'termin_wykonania' => now()->addDays(rand(1, 14)), // Losowy termin od jutra do 2 tygodni
                'id_tworcy' => $liderzy[array_rand($liderzy)]->id,
            ]);

            // Losujemy od 1 do 3 pracowników z naszej tablicy i przypisujemy do zadania
            $wylosowaniPracownicy = collect($pracownicy)->random(rand(1, 3));
            $zadanie->users()->attach($wylosowaniPracownicy->pluck('id'));

            // 5. Dodajemy przykładową notatkę do zadania (od pierwszego wylosowanego pracownika)
            Note::create([
                'task_id' => $zadanie->id,
                'user_id' => $wylosowaniPracownicy->first()->id,
                'tresc' => "Zabrałem się za to zadanie. Myślę, że wyrobię się przed terminem.",
                'typ' => 'zwykla_notatka'
            ]);
        }

        $this->command->info('Dane demonstracyjne zostały pomyślnie dodane!');
    }
}