<?php
session_start();  // Rozpoczynamy sesję, aby przechowywać dane, takie jak zawartość koszyka

// Liczba produktów w koszyku
// Jeśli koszyk istnieje w sesji, zliczamy jego elementy, w przeciwnym razie ustawiamy wartość 0
$liczbaProduktowWKoszyku = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sklep Internetowy</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="container">  <!-- Kontener dla nagłówka -->
            <h1>Witamy w naszym sklepie!</h1>  <!-- Nagłówek witający użytkownika -->
            <nav>  <!-- Nawigacja z linkami do różnych sekcji sklepu -->
                <ul>
                    <li><a href="products.php">Produkty</a></li>  <!-- Link do strony z produktami -->
                    <li><a href="cart.php">Koszyk (<?= count($_SESSION['cart'] ?? []); ?>)</a></li>  <!-- Link do koszyka, z liczbą produktów w koszyku -->
                    <li><a href="orders.php">Historia Zamówień</a></li>  <!-- Link do historii zamówień -->
                    <li><a href="add_product.php">Dodaj Produkt</a></li>  <!-- Link do formularza dodawania produktów (dla administratora) -->
                </ul>
            </nav>

            <!-- WYSZUKIWARKA W PRAWYM GÓRNYM ROGU -->
            <form method="GET" action="products.php" class="search-form top-right">  <!-- Formularz wyszukiwania produktów -->
                <input type="text" name="search" placeholder="Szukaj produktu...">  <!-- Pole tekstowe do wpisania frazy wyszukiwania -->
                <button type="submit">🔍</button>  <!-- Przycisk do wysłania formularza (ikonka lupy) -->
            </form>
        </div>
    </header>

    <section class="main-content">  <!-- Sekcja główna strony, która wyświetla treść -->
        <div class="container">
            <h2>Wybierz produkty z naszej oferty!</h2>  <!-- Nagłówek w sekcji głównej -->
            <p>Sprawdź naszą bogatą ofertę produktów w zakładce <a href="products.php">Produkty</a>.</p>  <!-- Informacja o ofercie z linkiem do strony z produktami -->
        </div>
    </section>

    <footer>  <!-- Stopka strony -->
        <div class="container">
            <p>&copy; 2025 Sklep Internetowy - Wszelkie prawa zastrzeżone</p>
        </div>
    </footer>
</body>
</html>
