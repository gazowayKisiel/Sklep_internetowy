<?php
session_start();  // Rozpoczynamy sesję, aby móc przechowywać dane użytkownika, takie jak koszyk
include 'db.php';  // Dołączamy plik, który zawiera połączenie z bazą danych

// Pobieramy ID zamówienia z parametru URL (jeśli jest dostępne)
$order_id = $_GET['id'] ?? null;  // Operator ?? zapewnia, że jeśli 'id' nie jest ustawione, to będzie null

if ($order_id) {
    // Jeśli ID zamówienia jest dostępne, przygotowujemy zapytanie do pobrania szczegółów zamówienia z bazy danych
    $stmt = $db->prepare("SELECT * FROM zamowienia WHERE id = ?");
    $stmt->bindValue(1, $order_id, SQLITE3_INTEGER);  // Powiązanie parametru z zapytaniem
    $result = $stmt->execute();  // Wykonanie zapytania
    $order = $result->fetchArray(SQLITE3_ASSOC);  // Pobranie danych zamówienia w formacie tablicy

    if ($order) {
        // Jeśli zamówienie zostało znalezione, dekodujemy koszyk z formatu JSON
        $cart = json_decode($order['koszyk'], true);  // Dekodowanie zawartości koszyka
        $unique_products = array_count_values($cart);  // Zliczamy unikalne produkty w koszyku
        
        $total_price = 0;  // Zmienna do obliczania całkowitej ceny zamówienia
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title>Szczegóły Zamówienia - Sklep Internetowy</title>  
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Szczegóły Zamówienia</h1>  <!-- Nagłówek strony -->
            <nav>
                <ul>
                    <li><a href="products.php">Produkty</a></li>  <!-- Link do strony z produktami -->
                    <li><a href="cart.php">Koszyk</a></li>  <!-- Link do strony koszyka -->
                    <li><a href="orders.php">Historia Zamówień</a></li>  <!-- Link do historii zamówień -->
                </ul>
            </nav>
        </div>
    </header>

    <section class="order-details">
        <div class="container">
            <h2>Informacje o zamówieniu</h2>

            <?php if ($order): ?>  <!-- Sprawdzamy, czy zamówienie zostało znalezione -->
                <!-- Wyświetlamy szczegóły zamówienia: data, koszt wysyłki, suma -->
                <p><strong>Data zamówienia:</strong> <?= htmlspecialchars($order['data_zamowienia']); ?></p>
                <p><strong>Koszt wysyłki:</strong> <?= number_format($order['koszt_wysylki'], 2); ?> PLN</p>
                <p><strong>Suma:</strong> <?= number_format($order['suma'], 2); ?> PLN</p>

                <h3>Adres dostawy:</h3>
                <p>
                    <?= htmlspecialchars($order['ulica']); ?> <?= htmlspecialchars($order['numer_domu']); ?><br>
                    <?= htmlspecialchars($order['miasto']); ?>, <?= htmlspecialchars($order['kod_pocztowy']); ?><br>
                    <?= htmlspecialchars($order['email']); ?>, <?= htmlspecialchars($order['telefon']); ?>
                </p>

                <h3>Zakupione produkty:</h3>
                <table class="order-products">
                    <thead>
                        <tr>
                            <th>Produkt</th>
                            <th>Cena</th>
                            <th>Ilość</th>
                            <th>Łączna cena</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Pętla przez wszystkie unikalne produkty w koszyku -->
                        <?php foreach ($unique_products as $product_id => $quantity): ?>
                            <?php
                            // Pobieramy dane produktu z bazy danych
                            $stmt = $db->prepare("SELECT * FROM produkty WHERE id = ?");
                            $stmt->bindValue(1, $product_id, SQLITE3_INTEGER);  // Powiązanie ID produktu
                            $result = $stmt->execute();  // Wykonanie zapytania
                            $product = $result->fetchArray(SQLITE3_ASSOC);  // Pobranie danych produktu

                            if ($product) {
                                // Obliczamy całkowitą cenę produktu (cena * ilość)
                                $total_product_price = $product['cena'] * $quantity;
                                $total_price += $total_product_price;  // Dodajemy do całkowitej ceny
                            }
                            ?>
                            <!-- Wyświetlamy dane o produkcie w tabeli -->
                            <tr>
                                <td><?= htmlspecialchars($product['nazwa']); ?></td>
                                <td><?= number_format($product['cena'], 2); ?> PLN</td>
                                <td><?= $quantity; ?></td>
                                <td><?= number_format($total_product_price, 2); ?> PLN</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            <?php else: ?>
                <!-- Jeśli zamówienie o podanym ID nie zostało znalezione -->
                <p>Nie znaleziono zamówienia o tym ID.</p>
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
