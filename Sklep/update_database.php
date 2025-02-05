<?php
//  Ten plik używany był do poszeżania bazy danych o kolejne tabele


$db = new SQLite3('sklep.sqlite');

// Sprawdzenie, czy 'usuniety' już istnieje
$kolumny = $db->query("PRAGMA table_info(produkty)");
$kolumnaIstnieje = false;

while ($kolumna = $kolumny->fetchArray(SQLITE3_ASSOC)) {
    if ($kolumna['name'] === 'usuniety') {
        $kolumnaIstnieje = true;
        break;
    }
}

// Jeśli kolumna nie istnieje, dodajemy ją do tabeli 'produkty'
if (!$kolumnaIstnieje) {
    $db->exec("ALTER TABLE produkty ADD COLUMN usuniety INTEGER DEFAULT 0");
    echo "Dodano kolumnę 'usuniety' do tabeli 'produkty'.";
} else {
    echo "Kolumna 'usuniety' już istnieje.";
}
?>
