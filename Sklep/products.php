<?php
session_start();  // Rozpoczynamy sesję, aby przechować dane użytkownika, np. koszyk
include 'db.php';  // Dołączamy plik z połączeniem do bazy danych

// Pobranie zapytania wyszukiwania z formularza (jeśli zostało wysłane)
$search = $_GET['search'] ?? ''; // Jeśli nie ma zapytania, domyślnie ustawiamy pusty ciąg

// Pobieranie produktów z bazy (z filtrowaniem, jeśli zapytanie jest niepuste)
if (!empty($search)) {
    // Przygotowanie zapytania do bazy danych, które szuka produktów w nazwie lub opisie
    // Uwzględniamy tylko produkty, które nie są usunięte (usunięty = 0)
    $stmt = $db->prepare("SELECT * FROM produkty WHERE (nazwa LIKE :search OR opis LIKE :search) AND usuniety = 0");
    $stmt->bindValue(':search', '%' . $search . '%', SQLITE3_TEXT);  // Przekazujemy zapytanie z parametrem (szukamy podobnych nazw lub opisów)
    $produkty = $stmt->execute();  // Wykonanie zapytania
} else {
    // Jeśli zapytanie jest puste, pobieramy wszystkie produkty, które nie są usunięte
    $produkty = $db->query("SELECT * FROM produkty WHERE usuniety = 0");
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produkty - Sklep Internetowy</title> 
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Nasze Produkty</h1> 
            <nav>
                <ul>
                    <li><a href="home.php">Strona Główna</a></li>  <!-- Link do strony głównej -->
                    <li><a href="cart.php">Koszyk (<?= count($_SESSION['cart'] ?? []); ?>)</a></li>  <!-- Link do koszyka z liczbą produktów -->
                    <li><a href="orders.php">Historia Zamówień</a></li>  <!-- Link do historii zamówień -->
                </ul>
            </nav>
        </div>
    </header>

    <section class="produkty">
        <div class="container">
            <h2>Dostępne produkty</h2>  <!-- Nagłówek sekcji produktów -->

            <!-- Formularz wyszukiwania produktów -->
            <form method="GET" action="products.php" class="search-form">
                <input type="text" name="search" placeholder="Szukaj produktu..." value="<?= htmlspecialchars($search); ?>">  <!-- Pole tekstowe do wpisania zapytania -->
                <button type="submit">Szukaj</button>  <!-- Przycisk do wysłania formularza -->
            </form>

            <div class="produkty-grid">
                <?php 
                $znaleziono = false;  // Zmienna pomocnicza do sprawdzenia, czy znaleziono jakiekolwiek produkty
                while ($produkt = $produkty->fetchArray(SQLITE3_ASSOC)): 
                    $znaleziono = true;  // Ustawiamy, że znaleziono produkt
                ?>
                    <div class="produkt">  <!-- Każdy produkt wyświetlany w gridzie -->
                        <a href="product.php?id=<?= $produkt['id']; ?>">  <!-- Link do szczegółów produktu -->
                            <img src="<?= htmlspecialchars($produkt['zdjecie']); ?>" alt="<?= htmlspecialchars($produkt['nazwa']); ?>">  <!-- Obrazek produktu -->
                        </a>
                        <h3><a href="product.php?id=<?= $produkt['id']; ?>"><?= htmlspecialchars($produkt['nazwa']); ?></a></h3>  <!-- Nazwa produktu, również jako link do szczegółów -->
                        <p>Cena: <?= number_format($produkt['cena'], 2); ?> PLN</p>  <!-- Wyświetlenie ceny produktu -->
                        <p><strong>Dostępna ilość:</strong> <?= intval($produkt['ilosc']); ?></p>  <!-- Wyświetlenie dostępnej ilości produktu -->
                        <a href="product.php?id=<?= $produkt['id']; ?>" class="btn">Zobacz więcej</a>  <!-- Link do szczegółów produktu -->
                    </div>
                <?php endwhile; ?>

                <!-- Komunikat, gdy nie znaleziono produktów odpowiadających zapytaniu -->
                <?php if (!$znaleziono): ?>
                    <p class="no-results">Brak wyników dla zapytania: <strong><?= htmlspecialchars($search); ?></strong></p>  <!-- Jeśli brak wyników -->
                <?php endif; ?>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <p>&copy; 2025 Sklep Internetowy - Wszelkie prawa zastrzeżone</p>  
        </div>
    </footer>
</body>
</html>
