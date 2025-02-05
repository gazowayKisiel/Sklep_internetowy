<?php
include 'db.php'; // Ładowanie pliku z połączeniem do bazy danych

// Sprawdzanie, czy formularz został wysłany metodą POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Pobieranie wartości z formularza
    $nazwa = $_POST['nazwa'];
    $opis = $_POST['opis'];
    $cena = $_POST['cena'];
    $ilosc = $_POST['ilosc'];
    $zdjecie = $_POST['zdjecie'];

    // Przygotowanie zapytania SQL do dodania nowego produktu do bazy danych
    $stmt = $db->prepare("INSERT INTO produkty (nazwa, opis, cena, ilosc, zdjecie) VALUES (?, ?, ?, ?, ?)");
    // Powiązanie wartości z zapytaniami
    $stmt->bindValue(1, $nazwa); 
    $stmt->bindValue(2, $opis); 
    $stmt->bindValue(3, $cena); 
    $stmt->bindValue(4, $ilosc); 
    $stmt->bindValue(5, $zdjecie);
    $stmt->execute(); // Wykonanie zapytania w bazie danych

    // Po zakończeniu operacji, wyświetlenie komunikatu o sukcesie
    echo "<p class='success-message'>Produkt dodany!</p>";
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dodaj Produkt</title>
    <link rel="stylesheet" href="style.css"> 
</head>
<body>

<!-- Formularz dodawania produktu -->
<div class="container"> <!-- Kontener dla formularza -->
    <div class="add-product-form"> <!-- Sekcja formularza -->
        <h1>Dodaj Produkt</h1> <!-- Tytuł sekcji formularza -->
        <form method="POST"> <!-- Formularz wysyłający dane metodą POST -->
            <!-- Pole dla nazwy produktu -->
            <div class="form-group">
                <label for="nazwa">Nazwa:</label>
                <input type="text" name="nazwa" id="nazwa" required> <!-- Pole tekstowe dla nazwy produktu -->
            </div>

            <!-- Pole dla opisu produktu -->
            <div class="form-group">
                <label for="opis">Opis:</label>
                <textarea name="opis" id="opis" required></textarea> <!-- Pole tekstowe dla opisu produktu -->
            </div>

            <!-- Pole dla ceny produktu -->
            <div class="form-group">
                <label for="cena">Cena:</label>
                <input type="number" name="cena" id="cena" step="0.01" required> <!-- Pole liczbowe dla ceny -->
            </div>

            <!-- Pole dla ilości produktu -->
            <div class="form-group">
                <label for="ilosc">Ilość:</label>
                <input type="number" name="ilosc" id="ilosc" required> <!-- Pole liczbowe dla ilości -->
            </div>

            <!-- Pole dla URL zdjęcia produktu -->
            <div class="form-group">
                <label for="zdjecie">Zdjęcie URL:</label>
                <input type="text" name="zdjecie" id="zdjecie" required> <!-- Pole tekstowe dla URL zdjęcia -->
            </div>

            <!-- Przyciski formularza -->
            <div class="form-group">
                <input type="submit" value="Dodaj Produkt" class="btn"> <!-- Przycisk do wysłania formularza -->
            </div>
        </form>

        <!-- Przycisk powrotu do strony głównej -->
        <div class="form-group">
            <a href="home.php" class="btn">Powrót do strony głównej</a> <!-- Link powrotu do strony głównej -->
        </div>
    </div>
</div>

</body>
</html>
