<?php
session_start();  // Rozpoczynamy sesję, aby przechować dane użytkownika, np. koszyk
include 'db.php';  // Dołączamy plik z połączeniem do bazy danych

// Sprawdzenie, czy parametr 'id' jest ustawiony i jest liczbą (ID produktu)
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Nieprawidłowy identyfikator produktu.");  // Jeśli nie, kończymy skrypt z błędem
}

$id = $_GET['id'];  // Pobieramy ID produktu z URL
$produkt = $db->querySingle("SELECT * FROM produkty WHERE id = $id", true);  // Pobieramy dane produktu z bazy danych

// Sprawdzenie, czy produkt istnieje w bazie
if (!$produkt) {
    die("Produkt nie istnieje.");  // Jeśli produkt nie istnieje, kończymy skrypt z błędem
}

// Obsługa dodania produktu do koszyka
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['ilosc'])) {
    $ilosc = max(1, intval($_POST['ilosc']));  // Ustalamy ilość produktu, nie mniej niż 1
    for ($i = 0; $i < $ilosc; $i++) {
        $_SESSION['cart'][] = $id;  // Dodajemy produkt do koszyka w sesji
    }
    header("Location: cart.php");  // Przekierowujemy do strony koszyka
    exit();  // Kończymy skrypt po przekierowaniu
}

// Obsługa usuwania produktu
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['usun_produkt'])) {
    // Przygotowujemy zapytanie do usunięcia produktu z bazy danych
    $stmt = $db->prepare("DELETE FROM produkty WHERE id = ?");
    $stmt->bindValue(1, $id, SQLITE3_INTEGER);  // Powiązanie ID produktu z zapytaniem
    $stmt->execute();  // Wykonanie zapytania

    // Po usunięciu produktu, przekierowujemy użytkownika z powrotem do listy produktów
    header("Location: products.php");
    exit();  // Kończymy skrypt po przekierowaniu
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0">  
    <title><?= htmlspecialchars($produkt['nazwa']); ?> - Sklep Internetowy</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1><?= htmlspecialchars($produkt['nazwa']); ?></h1>  <!-- Wyświetlamy nazwę produktu w nagłówku -->
            <nav>
                <ul>
                    <li><a href="home.php">Strona Główna</a></li>  <!-- Link do strony głównej -->
                    <li><a href="products.php">Lista Produktów</a></li>  <!-- Link do listy produktów -->
                    <li><a href="cart.php">Koszyk (<?= count($_SESSION['cart'] ?? []); ?>)</a></li>  <!-- Link do koszyka, z liczbą produktów w koszyku -->
                </ul>
            </nav>
        </div>
    </header>

    <section class="produkt-szczegoly">
        <div class="container">
            <!-- Wyświetlanie zdjęcia produktu -->
            <img src="<?= htmlspecialchars($produkt['zdjecie']); ?>" alt="<?= htmlspecialchars($produkt['nazwa']); ?>" class="produkt-zdjecie">
            
            <h2><?= htmlspecialchars($produkt['nazwa']); ?></h2>  <!-- Nazwa produktu -->
            <p><?= nl2br(htmlspecialchars($produkt['opis'])); ?></p>  <!-- Opis produktu, z zachowaniem nowych linii -->
            <p><strong>Cena:</strong> <?= number_format($produkt['cena'], 2); ?> PLN</p>  <!-- Cena produktu -->
            <p><strong>Dostępna ilość:</strong> <?= intval($produkt['ilosc']); ?></p>  <!-- Dostępna ilość produktu -->

            <!-- Formularz do dodania produktu do koszyka, jeśli produkt jest dostępny -->
            <?php if ($produkt['ilosc'] > 0): ?>
                <form method="post">
                    <label for="ilosc">Wybierz ilość:</label>  <!-- Pole wyboru ilości -->
                    <input type="number" name="ilosc" id="ilosc" value="1" min="1" max="<?= intval($produkt['ilosc']); ?>">  <!-- Pole input do wyboru ilości, min 1, max dostępna ilość -->
                    <input type="submit" value="Dodaj do koszyka">  <!-- Przycisk do dodania do koszyka -->
                </form>
            <?php else: ?>
                <!-- Jeśli produkt jest niedostępny, wyświetlamy komunikat -->
                <p style="color: red; font-weight: bold;">Produkt niedostępny</p>
            <?php endif; ?>

            <!-- Formularz do usuwania produktu ze sklepu -->
            <form method="post" onsubmit="return confirm('Czy na pewno chcesz usunąć ten produkt? Jest to NIEODWRACALNE!');">
                <input type="hidden" name="usun_produkt">  <!-- Ukryte pole, wskazujące na akcję usunięcia -->
                <button type="submit" class="btn btn-danger">Wycofaj produkt ze sklepu</button>  <!-- Przycisk do usunięcia produktu -->
            </form>

        </div>
    </section>

    <footer>
        <div class="container">
            <p>&copy; 2025 Sklep Internetowy - Wszelkie prawa zastrzeżone</p> 
    </footer>
</body>
</html>
