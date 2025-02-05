<?php
session_start();  // Rozpoczynamy sesję, aby przechowywać dane koszyka i inne dane użytkownika
include 'db.php';  // Włączamy plik z połączeniem do bazy danych

// Pobieranie koszyka z sesji lub inicjalizacja pustego koszyka, jeśli nie istnieje
$cart = $_SESSION['cart'] ?? [];  // Jeśli koszyk istnieje w sesji, przypisujemy go do zmiennej, w przeciwnym razie ustawiamy pustą tablicę

// Zliczanie liczby produktów w koszyku (ilość unikalnych produktów)
$unique_products = array_count_values($cart);
$total_price = 0;  // Inicjalizujemy zmienną do przechowywania całkowitej ceny
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title>Checkout - Sklep Internetowy</title>
    <link rel="stylesheet" href="style.css">
    <script>
        // Funkcja do aktualizacji łącznej ceny po dodaniu kosztu wysyłki
        function updateTotalPrice() {
            var shippingCost = parseFloat(document.getElementById('shipping').value);  // Pobranie wybranego kosztu wysyłki
            var productTotal = parseFloat(document.getElementById('product-total').textContent);  // Pobranie całkowitej ceny produktów
            var finalPrice = productTotal + shippingCost;  // Obliczenie całkowitej ceny (produkty + wysyłka)
            
            
            document.getElementById('total-price').textContent = finalPrice.toFixed(2);  // Wyświetlenie ceny z dwoma miejscami po przecinku
        }
    </script>
</head>
<body>
    <header>
        <div class="container">
            <h1>Realizacja zamówienia</h1>  <!-- Nagłówek strony -->
            <nav>
                <ul>
                    <li><a href="home.php">Strona Główna</a></li>  <!-- Link do strony głównej -->
                    <li><a href="products.php">Produkty</a></li>  <!-- Link do strony produktów -->
                    <li><a href="cart.php">Koszyk</a></li>  <!-- Link do strony koszyka -->
                </ul>
            </nav>
        </div>
    </header>

    <section class="checkout">  <!-- Sekcja zawierająca formularz podsumowania zamówienia -->
        <div class="container">
            <h2>Podsumowanie zamówienia</h2>

            <!-- Tabela z produktami w koszyku -->
            <table class="checkout-table">
                <thead>
                    <tr>
                        <th>Produkt</th>  <!-- Nagłówek tabeli -->
                        <th>Cena</th> 
                        <th>Ilość</th>
                        <th>Łączna cena</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($unique_products as $product_id => $quantity): ?>  <!-- Pętla po unikalnych produktach w koszyku -->
                        <?php
                        // Pobieranie danych o produkcie z bazy danych na podstawie ID
                        $stmt = $db->prepare("SELECT * FROM produkty WHERE id = ?");
                        $stmt->bindValue(1, $product_id);  // Rzutowanie na odpowiedni typ
                        $result = $stmt->execute();  // Wykonanie zapytania
                        $product = $result->fetchArray(SQLITE3_ASSOC);  // Pobranie danych produktu

                        if ($product) {
                            $total_product_price = $product['cena'] * $quantity;  // Obliczenie łącznej ceny dla danego produktu
                            $total_price += $total_product_price;  // Dodanie tej ceny do całkowitej ceny zamówienia
                        }
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($product['nazwa']); ?></td>  <!-- Wyświetlanie nazwy produktu -->
                            <td><?= number_format($product['cena'], 2); ?> PLN</td>  <!-- Wyświetlanie ceny produktu z dwoma miejscami po przecinku -->
                            <td><?= $quantity; ?></td>  <!-- Wyświetlanie ilości produktu w koszyku -->
                            <td><?= number_format($total_product_price, 2); ?> PLN</td>  <!-- Wyświetlanie łącznej ceny produktu -->
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Formularz do wyboru opcji wysyłki i wprowadzenia danych dostawy -->
            <form method="POST" action="checkout.php">
                <label for="shipping">Wybierz sposób wysyłki:</label>
                <select name="koszt_wysylki" id="shipping" onchange="updateTotalPrice()">
                    <option value="10">Poczta - 10 PLN</option>  <!-- Opcje wysyłki -->
                    <option value="20">Kurier - 20 PLN</option> 
                </select>
                <br><br>

                <p><strong>Całkowita cena produktów: <span id="product-total"><?= number_format($total_price, 2); ?></span> PLN</strong></p>
                <p><strong>Łączna kwota (wraz z wysyłką):</strong> <span id="total-price"><?= number_format($total_price + 10, 2); ?></span> PLN</p>  <!-- Wyświetlanie całkowitej kwoty z wysyłką -->

                <h3>Dane do dostawy:</h3>  <!-- Sekcja z danymi do dostawy -->
                <label for="ulica">Ulica:</label>
                <input type="text" name="ulica" id="ulica" required>  <!-- Pole do wpisania ulicy -->
                <br><br>

                <label for="numer_domu">Numer domu:</label>
                <input type="text" name="numer_domu" id="numer_domu" required>  <!-- Pole do wpisania numeru domu -->
                <br><br>

                <label for="miasto">Miasto:</label>
                <input type="text" name="miasto" id="miasto" required>  <!-- Pole do wpisania miasta -->
                <br><br>

                <label for="kod_pocztowy">Kod pocztowy:</label>
                <input type="text" name="kod_pocztowy" id="kod_pocztowy" required>  <!-- Pole do wpisania kodu pocztowego -->
                <br><br>

                <label for="imie_nazwisko">Imię i nazwisko:</label>
                <input type="text" name="imie_nazwisko" id="imie_nazwisko" required>  <!-- Pole do wpisania imienia i nazwiska -->
                <br><br>

                <label for="email">Adres email:</label>
                <input type="email" name="email" id="email" required>  <!-- Pole do wpisania adresu email -->
                <br><br>

                <label for="telefon">Numer telefonu:</label>
                <input type="tel" name="telefon" id="telefon" required>  <!-- Pole do wpisania numeru telefonu -->
                <br><br>

                <input type="submit" value="Złóż zamówienie">  <!-- Przycisk do złożenia zamówienia -->
            </form>

            <?php
            if ($_SERVER["REQUEST_METHOD"] === "POST") {  // Sprawdzamy, czy formularz został wysłany
                // Oblicz koszt wysyłki
                $shipping_cost = (float) $_POST['koszt_wysylki'];
                $final_price = $total_price + $shipping_cost;  // Obliczamy całkowitą cenę z uwzględnieniem wysyłki

                // Zapisanie danych kontaktowych i adresu dostawy
                $ulica = $_POST['ulica'];
                $numer_domu = $_POST['numer_domu'];
                $miasto = $_POST['miasto'];
                $kod_pocztowy = $_POST['kod_pocztowy'];
                $imie_nazwisko = $_POST['imie_nazwisko'];
                $email = $_POST['email'];
                $telefon = $_POST['telefon'];

                // Zaktualizuj ilość produktów w bazie danych (zmniejszamy dostępne ilości)
                foreach ($unique_products as $product_id => $quantity) {
                    $stmt = $db->prepare("UPDATE produkty SET ilosc = ilosc - ? WHERE id = ?");
                    $stmt->bindValue(1, (int) $quantity);  // Rzutowanie na liczbę całkowitą
                    $stmt->bindValue(2, (int) $product_id);  // Rzutowanie na liczbę całkowitą
                    $stmt->execute();
                }

                // Zapisanie zamówienia do bazy danych
                $cart_json = json_encode($cart);  // Zamiana koszyka na format JSON
                $stmt = $db->prepare("INSERT INTO zamowienia (koszyk, koszt_wysylki, suma, imie_nazwisko, ulica, numer_domu, miasto, kod_pocztowy, email, telefon) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bindValue(1, $cart_json, SQLITE3_TEXT);  // Koszyk zapisany w formacie JSON
                $stmt->bindValue(2, $shipping_cost);  // Koszt wysyłki
                $stmt->bindValue(3, $final_price);  // Całkowita suma
                $stmt->bindValue(4, $imie_nazwisko, SQLITE3_TEXT);
                $stmt->bindValue(5, $ulica, SQLITE3_TEXT);
                $stmt->bindValue(6, $numer_domu, SQLITE3_TEXT);
                $stmt->bindValue(7, $miasto, SQLITE3_TEXT);
                $stmt->bindValue(8, $kod_pocztowy, SQLITE3_TEXT);
                $stmt->bindValue(9, $email, SQLITE3_TEXT);
                $stmt->bindValue(10, $telefon, SQLITE3_TEXT);
                $stmt->execute();

                // Po złożeniu zamówienia, wyczyszczenie koszyka
                $_SESSION['cart'] = [];
                echo "<p>Twoje zamówienie zostało złożone!</p>";  // Komunikat o zakończeniu zamówienia
            }
            ?>
        </div>
    </section>

    <footer>
        <div class="container">
            <p>&copy; 2025 Sklep Internetowy - Wszelkie prawa zastrzeżone</p> 
    </footer>
</body>
</html>
