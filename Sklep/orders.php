<?php
session_start();  // Rozpoczynamy sesję, aby móc przechowywać dane użytkownika
include 'db.php';  // Dołączamy plik z połączeniem z bazą danych

// Sprawdzenie, czy użytkownik chce usunąć zamówienie (sprawdzamy parametr 'delete' w URL)
if (isset($_GET['delete'])) {
    $order_id = $_GET['delete'];  // Pobieramy ID zamówienia, które chcemy usunąć

    // Przygotowujemy zapytanie do usunięcia zamówienia z bazy danych na podstawie ID
    $stmt = $db->prepare("DELETE FROM zamowienia WHERE id = ?");
    $stmt->bindValue(1, $order_id, SQLITE3_INTEGER);  // Powiązujemy ID zamówienia z zapytaniem
    $stmt->execute();  // Wykonujemy zapytanie

    // Po usunięciu zamówienia, przekierowujemy użytkownika z powrotem do strony z historią zamówień
    header("Location: orders.php");
    exit();  // Kończymy działanie skryptu po przekierowaniu
}

// Pobieranie wszystkich zamówień z bazy danych
$orders = [];
$stmt = $db->query("SELECT * FROM zamowienia ORDER BY data_zamowienia DESC");  // Zapytanie do pobrania zamówień posortowanych po dacie (od najnowszych)

while ($row = $stmt->fetchArray(SQLITE3_ASSOC)) {
    $orders[] = $row;  // Dodajemy każdy wynik do tablicy $orders
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title>Historia Zamówień - Sklep Internetowy</title> 
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Historia Zamówień</h1>  <!-- Nagłówek strony -->
            <nav>
                <ul>
                    <li><a href="home.php">Strona Główna</a></li>  <!-- Link do strony głównej -->
                    <li><a href="products.php">Produkty</a></li>  <!-- Link do strony z produktami -->
                    <li><a href="cart.php">Koszyk</a></li>  <!-- Link do strony koszyka -->
                    <li><a href="orders.php">Historia Zamówień</a></li>  <!-- Link do historii zamówień -->
                </ul>
            </nav>
        </div>
    </header>

    <section class="order-history">
        <div class="container">
            <h2>Twoje zamówienia</h2>
            
            <?php if (empty($orders)): ?>  <!-- Sprawdzamy, czy nie ma żadnych zamówień -->
                <p>Nie masz jeszcze żadnych zamówień.</p>  <!-- Wyświetlamy komunikat, jeśli brak zamówień -->
            <?php else: ?>
                <table class="orders-table">  <!-- Tabela wyświetlająca zamówienia -->
                    <thead>
                        <tr>
                            <th>Data zamówienia</th>  <!-- Nagłówki kolumn -->
                            <th>Koszt wysyłki</th>
                            <th>Suma</th>
                            <th>Adres dostawy</th>
                            <th>Status</th>
                            <th>Akcje</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Pętla przez każde zamówienie i wyświetlanie jego szczegółów -->
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><?= htmlspecialchars($order['data_zamowienia']); ?></td>  <!-- Data zamówienia -->
                                <td><?= number_format($order['koszt_wysylki'], 2); ?> PLN</td>  <!-- Koszt wysyłki -->
                                <td><?= number_format($order['suma'], 2); ?> PLN</td>  <!-- Suma zamówienia -->
                                <td>
                                    <?= htmlspecialchars($order['ulica']); ?> <?= htmlspecialchars($order['numer_domu']); ?><br>
                                    <?= htmlspecialchars($order['miasto']); ?>, <?= htmlspecialchars($order['kod_pocztowy']); ?><br>
                                    <?= htmlspecialchars($order['email']); ?>, <?= htmlspecialchars($order['telefon']); ?>
                                </td>
                                <td><?= htmlspecialchars($order['status']); ?></td>  <!-- Status zamówienia -->
                                <td>
                                    <a href="order_details.php?id=<?= $order['id']; ?>">Szczegóły</a> |  <!-- Link do szczegółów zamówienia -->
                                    <a href="orders.php?delete=<?= $order['id']; ?>" onclick="return confirm('Czy na pewno chcesz usunąć to zamówienie?');">Usuń</a>  <!-- Link do usunięcia zamówienia, z potwierdzeniem -->
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </section>

    <footer>
        <div class="container">
            <p>&copy; 2025 Sklep Internetowy - Wszelkie prawa zastrzeżone</p>  <!-- Stopka strony -->
        </div>
    </footer>
</body>
</html>
