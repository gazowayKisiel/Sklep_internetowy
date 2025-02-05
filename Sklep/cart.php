<?php
session_start();  // Rozpoczyna sesję, aby przechowywać dane użytkownika, np. koszyk

include 'db.php';  // Włączenie pliku z połączeniem do bazy danych

// Inicjalizacja koszyka, jeśli nie istnieje
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];  // Tworzenie pustego koszyka, jeśli nie został jeszcze zainicjowany
}

// Dodanie produktu do koszyka
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id'])) {
    $_SESSION['cart'][] = $_POST['id'];  // Dodanie produktu do koszyka na podstawie jego ID
}

// Usunięcie produktu z koszyka
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['remove_id'])) {
    $remove_id = $_POST['remove_id'];  // Pobranie ID produktu do usunięcia
    $_SESSION['cart'] = array_filter($_SESSION['cart'], function($item) use ($remove_id) {
        return $item != $remove_id;  // Filtrujemy produkty w koszyku, usuwając wybrany
    });
}

// Pobranie produktów z koszyka
$cart_items = $_SESSION['cart'] ?? [];  // Pobranie produktów w koszyku (jeśli istnieją)
$produkty_w_koszyku = [];  // Tablica do przechowywania produktów w koszyku

if (!empty($cart_items)) {  // Jeśli koszyk nie jest pusty
    $unique_ids = array_count_values($cart_items);  // Zliczenie ilości wystąpień każdego produktu w koszyku
    foreach ($unique_ids as $id => $count) {
        $produkt = $db->querySingle("SELECT * FROM produkty WHERE id = $id", true);  // Pobranie produktu z bazy danych
        if ($produkt) {  // Jeśli produkt istnieje
            $produkt['ilosc_w_koszyku'] = $count;  // Dodanie informacji o ilości produktu w koszyku
            $produkty_w_koszyku[] = $produkt;  // Dodanie produktu do tablicy produktów w koszyku
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koszyk - Sklep Internetowy</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Twój Koszyk</h1>
            <nav>
                <ul>
                    <li><a href="home.php">Strona Główna</a></li>
                    <li><a href="products.php">Powrót do produktów</a></li>
                    <li><a href="orders.php">Historia Zamówień</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <section class="koszyk">  <!-- Sekcja zawierająca informacje o koszyku -->
        <div class="container">
            <h2>Produkty w koszyku</h2>  <!-- Nagłówek sekcji z produktami w koszyku -->
            <?php if (empty($produkty_w_koszyku)): ?>  <!-- Sprawdzanie, czy koszyk jest pusty -->
                <p>Twój koszyk jest pusty.</p>  <!-- Komunikat, jeśli koszyk jest pusty -->
            <?php else: ?>  <!-- Jeśli koszyk nie jest pusty -->
                <table>  <!-- Tabela z produktami w koszyku -->
                    <tr>
                        <th>Zdjęcie</th>  <!-- Nagłóweki kolumnyn -->
                        <th>Nazwa</th>
                        <th>Cena</th> 
                        <th>Ilość</th>
                        <th>Usuń</th>
                    </tr>
                    <?php foreach ($produkty_w_koszyku as $produkt): ?>  <!-- Pętla po produktach w koszyku -->
                        <tr>
                            <td><img src="<?= htmlspecialchars($produkt['zdjecie']); ?>" alt="<?= htmlspecialchars($produkt['nazwa']); ?>" width="50"></td>  <!-- Wyświetlanie zdjęcia produktu -->
                            <td><?= htmlspecialchars($produkt['nazwa']); ?></td>  <!-- Wyświetlanie nazwy produktu, ceny i ilości w koszyku -->
                            <td><?= number_format($produkt['cena'], 2); ?> PLN</td>
                            <td><?= intval($produkt['ilosc_w_koszyku']); ?></td>  
                            <td>
                                <form method="post">  <!-- Formularz do usuwania produktu z koszyka -->
                                    <input type="hidden" name="remove_id" value="<?= $produkt['id']; ?>">  <!-- Przekazywanie ID produktu do usunięcia -->
                                    <input type="submit" value="Usuń">  <!-- Przycisk do usunięcia produktu z koszyka -->
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
                <br>
                <a href="checkout.php" class="btn">Przejdź do zamówienia</a>  <!-- Przycisk przekierowujący do strony zamówienia -->
            <?php endif; ?>
        </div>
    </section>

    <footer>
        <div class="container">
            <p>&copy; 2025 Sklep Internetowy - Wszelkie prawa zastrzeżone</p>
        </div>
    </footer>
</body>
</html>
