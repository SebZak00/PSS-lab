-- Tabela ról (Katalog ról w systemie)
CREATE TABLE role (
    id INT NOT NULL AUTO_INCREMENT,
    nazwa VARCHAR(50) NOT NULL,
    aktywna BOOLEAN DEFAULT TRUE,
    data_utworzenia TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
);

-- Tabela użytkowników z polami RODO
CREATE TABLE uzytkownicy (
    id INT NOT NULL AUTO_INCREMENT,
    imie VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    haslo VARCHAR(255) NOT NULL,
    
    -- Pola audytowe (RODO)
    kiedy_utworzono TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    kiedy_zmodyfikowano TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    utworzono_przez INT,
    zmodyfikowano_przez INT,
    
    PRIMARY KEY (id)
);

-- Tabela asocjacyjna N-N łącząca Użytkowników i Role
CREATE TABLE uzytkownik_rola (
    id_uzytkownika INT NOT NULL,
    id_roli INT NOT NULL,
    data_nadania TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_uzytkownika, id_roli)
);

-- Tabela główna zadań
CREATE TABLE zadania (
    id INT NOT NULL AUTO_INCREMENT,
    tytul VARCHAR(100) NOT NULL,
    opis TEXT,
    status ENUM('nowe', 'w_toku', 'zrobione') DEFAULT 'nowe',
    termin_wykonania DATE,
    id_przypisanego INT,
    id_tworcy INT,
    data_utworzenia TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
);

-- Tabela na notatki pracownika i prośby o przesunięcie terminu
CREATE TABLE notatki_zadania (
    id INT NOT NULL AUTO_INCREMENT,
    tresc TEXT NOT NULL,
    typ ENUM('zwykla_notatka', 'prosba_o_ddl') DEFAULT 'zwykla_notatka',
    id_zadania INT NOT NULL,
    id_uzytkownika INT NOT NULL,
    data_dodania TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
);

-- RELACJE (Klucze obce)

-- Związki dla tabeli asocjacyjnej ról
ALTER TABLE uzytkownik_rola ADD CONSTRAINT fk_ur_uzytkownik FOREIGN KEY (id_uzytkownika) REFERENCES uzytkownicy(id) ON DELETE CASCADE;
ALTER TABLE uzytkownik_rola ADD CONSTRAINT fk_ur_rola FOREIGN KEY (id_roli) REFERENCES role(id) ON DELETE CASCADE;

-- Związki dla zadań
ALTER TABLE zadania ADD CONSTRAINT fk_zadanie_przypisany FOREIGN KEY (id_przypisanego) REFERENCES uzytkownicy(id) ON DELETE SET NULL;
ALTER TABLE zadania ADD CONSTRAINT fk_zadanie_tworca FOREIGN KEY (id_tworcy) REFERENCES uzytkownicy(id) ON DELETE SET NULL;

-- Związki dla notatek
ALTER TABLE notatki_zadania ADD CONSTRAINT fk_notatka_zadanie FOREIGN KEY (id_zadania) REFERENCES zadania(id) ON DELETE CASCADE;
ALTER TABLE notatki_zadania ADD CONSTRAINT fk_notatka_uzytkownik FOREIGN KEY (id_uzytkownika) REFERENCES uzytkownicy(id) ON DELETE CASCADE;

-- Związki RODO (Tabela użytkowników wskazuje sama na siebie)
ALTER TABLE uzytkownicy ADD CONSTRAINT fk_rodo_utworzyl FOREIGN KEY (utworzono_przez) REFERENCES uzytkownicy(id) ON DELETE SET NULL;
ALTER TABLE uzytkownicy ADD CONSTRAINT fk_rodo_zmodyfikowal FOREIGN KEY (zmodyfikowano_przez) REFERENCES uzytkownicy(id) ON DELETE SET NULL;