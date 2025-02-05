<?php

$db = new SQLite3('sklep.sqlite');

// Tworzenie tabeli produkty, jeśli jeszcze nie istnieje
$db->exec("CREATE TABLE IF NOT EXISTS produkty (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nazwa TEXT NOT NULL,
    opis TEXT,
    cena REAL NOT NULL,
    ilosc INTEGER NOT NULL,
    zdjecie TEXT
)");

// Tworzenie tabeli zamowienia, jeśli jeszcze nie istnieje
$db->exec("CREATE TABLE IF NOT EXISTS zamowienia (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    data_zamowienia TEXT DEFAULT CURRENT_TIMESTAMP,
    koszyk TEXT NOT NULL,
    koszt_wysylki REAL NOT NULL,
    suma REAL NOT NULL,
    ulica TEXT, 
    numer_domu TEXT, 
    miasto TEXT,
    kod_pocztowy TEXT
)");

echo "Baza danych została zainicjalizowana, a kolumny zostały dodane do tabeli 'zamowienia'.";
?>
