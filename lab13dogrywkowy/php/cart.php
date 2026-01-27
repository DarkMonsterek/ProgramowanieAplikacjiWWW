<?php

/**
 * Klasa ShoppingCart
 * 
 * Odpowiada za zarządzanie koszykiem zakupowym użytkownika.
 * Obsługuje dodawanie, usuwanie, aktualizację ilości produktów,
 * a także synchronizację koszyka z bazą danych dla zalogowanych użytkowników
 * oraz proces finalizacji zamówienia.
 */
class ShoppingCart
{
    /** @var mysqli Połączenie z bazą danych */
    private $db_link;

    /**
     * Konstruktor klasy ShoppingCart
     * 
     * @param mysqli $link Uchwyt połączenia z bazą danych
     */
    public function __construct($link)
    {
        $this->db_link = $link;
        // Inicjalizacja pustego koszyka w sesji, jeśli nie istnieje
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    /**
     * Zapisuje stan koszyka z sesji do bazy danych.
     * Używana dla trwałości koszyka zalogowanego użytkownika.
     * 
     * @param int $userId ID zalogowanego użytkownika
     */
    public function saveCartToDb($userId)
    {
        if ($userId <= 0)
            return;

        // 1. Wyczyść stare wpisy dla użytkownika w tabeli cart_items
        $queryDelete = "DELETE FROM cart_items WHERE user_id = '$userId'";
        mysqli_query($this->db_link, $queryDelete);

        // 2. Wstaw bieżące wpisy z sesji do bazy
        if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $item) {
                $prodId = intval($item['id']);
                $qty = intval($item['quantity']);

                $queryInsert = "INSERT INTO cart_items (user_id, product_id, quantity) VALUES ('$userId', '$prodId', '$qty')";
                mysqli_query($this->db_link, $queryInsert);
            }
        }
    }

    /**
     * Synchronizuje koszyk w sesji z koszykiem zapisanym w bazie.
     * Wywoływana przy logowaniu użytkownika.
     * 
     * Strategia:
     * - Pobiera koszyk z bazy.
     * - Jeśli sesja pusta, ładuje z bazy.
     * - Jeśli sesja pełna, scala z bazą (priororytet sesji/dodawanie).
     * 
     * @param int $userId ID zalogowanego użytkownika
     */
    public function syncWithDb($userId)
    {
        if ($userId <= 0)
            return;

        // 1. Pobierz koszyk z bazy (relacyjnie)
        // Łączymy z tabelą products, aby mieć aktualne nazwy i ceny
        $query = "SELECT c.quantity, p.id, p.tytul, p.cena_netto, p.podatek_vat 
                  FROM cart_items c 
                  JOIN products p ON c.product_id = p.id 
                  WHERE c.user_id = '$userId'";
        $result = mysqli_query($this->db_link, $query);

        $dbCart = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $dbCart[] = [
                'id' => $row['id'],
                'title' => $row['tytul'],
                'quantity' => intval($row['quantity']),
                'netPrice' => $row['cena_netto'],
                'vat' => $row['podatek_vat'],
                'date' => time() // data bieżąca
            ];
        }

        // 2. Logika scalania
        if (empty($dbCart) && !empty($_SESSION['cart'])) {
            // DB puste, Sesja pełna -> Zapisz sesję do DB
            $this->saveCartToDb($userId);
            return;
        }

        if (!empty($dbCart)) {
            if (empty($_SESSION['cart'])) {
                // Sesja pusta, DB pełna -> Wczytaj z DB do sesji
                $_SESSION['cart'] = $dbCart;
            } else {
                // Oba pełne -> Scalanie (Sumowanie ilości dla tych samych ID)
                $tempCart = $dbCart;
                foreach ($_SESSION['cart'] as $sItem) {
                    $found = false;
                    foreach ($tempCart as $k => $dItem) {
                        if ($sItem['id'] == $dItem['id']) {
                            $tempCart[$k]['quantity'] += $sItem['quantity'];
                            $found = true;
                            break;
                        }
                    }
                    if (!$found) {
                        $tempCart[] = $sItem;
                    }
                }
                $_SESSION['cart'] = $tempCart;
                // Zapisz połączony stan z powrotem do bazy
                $this->saveCartToDb($userId);
            }
        }
    }

    /**
     * Dodaje produkt do koszyka.
     * 
     * @param int $id ID produktu
     * @param string $title Nazwa produktu
     * @param int $quantity Ilość
     * @param float $netPrice Cena netto
     * @param float $vat Stawka VAT (np. 0.23)
     */
    public function addToCart($id, $title, $quantity, $netPrice, $vat)
    {
        // Sprawdź czy produkt już jest w koszyku
        $found = false;
        if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
            foreach ($_SESSION['cart'] as $key => $product) {
                if ($product['id'] == $id) {
                    $_SESSION['cart'][$key]['quantity'] += $quantity;
                    $found = true;
                    break;
                }
            }
        }

        // Jeśli nie ma, dodaj jako nową pozycję
        if (!$found) {
            $_SESSION['cart'][] = [
                'id' => $id,
                'title' => $title,
                'quantity' => $quantity,
                'netPrice' => $netPrice,
                'vat' => $vat,
                'date' => time()
            ];
        }

        // Jeśli użytkownik zalogowany, zapisz od razu do bazy
        if (isset($_SESSION['user_id'])) {
            $this->saveCartToDb($_SESSION['user_id']);
        }

        // Przekierowanie aby uniknąć ponownego wysłania formularza
        // header("Location: index.php?idp=koszyk"); 
    }

    /**
     * Usuwa produkt z koszyka na podstawie ID.
     * 
     * @param int $id ID produktu do usunięcia
     */
    public function removeFromCart($id)
    {
        if (isset($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $key => $product) {
                if ($product['id'] == $id) {
                    unset($_SESSION['cart'][$key]);
                    $_SESSION['cart'] = array_values($_SESSION['cart']); // Przeindeksowanie tablicy
                    break;
                }
            }
            if (isset($_SESSION['user_id'])) {
                $this->saveCartToDb($_SESSION['user_id']);
            }
        }
    }

    /**
     * Aktualizuje ilość produktu w koszyku.
     * 
     * @param int $id ID produktu
     * @param int $quantity Nowa ilość (jeśli <= 0, produkt jest usuwany)
     */
    public function updateQuantity($id, $quantity)
    {
        if (isset($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $key => $product) {
                if ($product['id'] == $id) {
                    if ($quantity <= 0) {
                        unset($_SESSION['cart'][$key]);
                        $_SESSION['cart'] = array_values($_SESSION['cart']);
                    } else {
                        $_SESSION['cart'][$key]['quantity'] = $quantity;
                    }
                    break;
                }
            }
            if (isset($_SESSION['user_id'])) {
                $this->saveCartToDb($_SESSION['user_id']);
            }
        }
    }

    /**
     * Finalizuje zamówienie.
     * Przetwarza koszyk, sprawdza dostępność, tworzy zamówienie w bazie,
     * wysyła maila i czyści koszyk.
     */
    public function checkout()
    {
        if (empty($_SESSION['cart']))
            return;

        $userId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
        $email = '';

        // Ustalenie adresu email (zalogowany vs gość)
        if ($userId > 0 && isset($_SESSION['user_login'])) {
            $email = $_SESSION['user_login'];
        } elseif (isset($_POST['guest_email'])) {
            $email = filter_var($_POST['guest_email'], FILTER_SANITIZE_EMAIL);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo '<div class="contact-container notification error"><p>Podaj poprawny adres e-mail.</p></div>';
                $this->showCart();
                return;
            }
        } else {
            echo '<div class="contact-container notification error"><p>Brak adresu e-mail. Zaloguj się lub podaj e-mail.</p></div>';
            $this->showCart();
            return;
        }

        $errors = [];
        $totalAmount = 0;
        $orderItemsHtml = ''; // Do treści maila

        // 1. Walidacja stanów magazynowych i obliczenie sumy
        foreach ($_SESSION['cart'] as $item) {
            $id = $item['id'];
            $qty = $item['quantity'];

            // Obliczenie ceny brutto dla sumy zamówienia
            $priceNet = floatval($item['netPrice']);
            $vatRate = floatval($item['vat']);
            $priceGross = $priceNet * (1 + $vatRate);
            $totalAmount += $priceGross * $qty;

            $orderItemsHtml .= "<li>" . htmlspecialchars($item['title']) . " - Ilość: $qty - Cena: " . number_format($priceGross, 2) . " zł</li>";

            $query = "SELECT ilosc_dostepnych_sztuk, tytul FROM products WHERE id='$id' LIMIT 1";
            $result = mysqli_query($this->db_link, $query);
            $row = mysqli_fetch_assoc($result);

            if (!$row || $row['ilosc_dostepnych_sztuk'] < $qty) {
                $errors[] = "Brak wystarczającej ilości towaru: " . $item['title'];
            }
        }

        if (!empty($errors)) {
            foreach ($errors as $err) {
                echo "<p style='color:red;'>$err</p>";
            }
            return;
        }

        // 2. Zapis zamówienia w tabeli `orders`
        $userId = intval($userId);
        $totalAmount = floatval($totalAmount);
        $emailEscaped = mysqli_real_escape_string($this->db_link, $email);

        // Status domyślny 'nowe' jest w bazie, ale możemy podać jawnie
        $queryOrder = "INSERT INTO orders (user_id, email, total_amount, status) VALUES ('$userId', '$emailEscaped', '$totalAmount', 'nowe')";

        if (mysqli_query($this->db_link, $queryOrder)) {
            $orderId = mysqli_insert_id($this->db_link);

            // 3. Zapis pozycji zamówienia w `order_items` i aktualizacja stanów
            foreach ($_SESSION['cart'] as $item) {
                $prodId = intval($item['id']);
                $qty = intval($item['quantity']);

                $priceNet = floatval($item['netPrice']);
                $vatRate = floatval($item['vat']);
                $priceGross = $priceNet * (1 + $vatRate);

                // Insert into order_items
                $queryItem = "INSERT INTO order_items (order_id, product_id, quantity, price_gross) VALUES ('$orderId', '$prodId', '$qty', '$priceGross')";
                mysqli_query($this->db_link, $queryItem);

                // Update stock (zmniejszenie stanu)
                $queryStock = "UPDATE products SET ilosc_dostepnych_sztuk = ilosc_dostepnych_sztuk - $qty WHERE id='$prodId'";
                mysqli_query($this->db_link, $queryStock);
            }

            // 4. Wysyłanie emaila z potwierdzeniem
            include_once(__DIR__ . '/contact.php');
            $messageBody = "<h3>Dziękujemy za zamówienie nr #$orderId</h3>";
            $messageBody .= "<p>Status: Przyjęte do realizacji</p>";
            $messageBody .= "<p>Kwota całkowita: <strong>" . number_format($totalAmount, 2) . " zł</strong></p>";
            $messageBody .= "<h4>Szczegóły zamówienia:</h4><ul>$orderItemsHtml</ul>";

            SendSmtpMail($email, "Potwierdzenie zamówienia nr #$orderId", $messageBody, "Sklep Internetowy", "no-reply@sklep.pl");

            // 5. Wyczyszczenie koszyka i sukces
            unset($_SESSION['cart']);
            if ($userId > 0) {
                // Wyczyść też koszyk w bazie
                $this->saveCartToDb($userId);
            }
            echo '<div class="contact-container" style="max-width: 600px; margin: 40px auto; text-align: center; border-color: #4cd137;">';
            echo '<h2 class="heading" style="color: #4cd137;">Dziękujemy!</h2>';
            echo '<p>Twoje zamówienie (NR: ' . $orderId . ') zostało przyjęte do realizacji.</p>';
            echo '<p>Potwierdzenie wysłano na adres: <strong>' . htmlspecialchars($email) . '</strong></p>';
            echo '<a href="index.php?idp=sklep" class="btn btn-secondary">Wróć do sklepu</a>';
            echo '</div>';

        } else {
            echo "<p style='color:red;'>Błąd podczas składania zamówienia: " . mysqli_error($this->db_link) . "</p>";
        }
    }

    /**
     * Wyświetla widok koszyka (HTML).
     * Tabela z produktami, sumą i przyciskami akcji.
     */
    public function showCart()
    {
        echo '<h2 class="heading">Twój Koszyk</h2>';
        echo '<div class="contact-container" style="max-width: 1000px; margin: 40px auto;">';
        if (empty($_SESSION['cart'])) {
            echo '<p style="text-align: center; font-size: 1.2rem;">Twój koszyk jest pusty.</p>';
            echo '<div style="text-align: center; margin-top: 20px;"><a href="index.php?idp=sklep" class="btn btn-primary">Wróć do sklepu</a></div>';
            echo '</div>';
            return;
        }

        echo '<form method="post" action="index.php?idp=koszyk">';
        echo '<div class="table-responsive">';
        echo '<table class="ranking-table">';
        echo '<thead>
              <tr>
                <th>Produkt</th>
                <th>Cena Netto</th>
                <th>VAT</th>
                <th>Cena Brutto</th>
                <th>Ilość</th>
                <th>Wartość Brutto</th>
                <th>Akcje</th>
              </tr>
              </thead>
              <tbody>';

        $totalValue = 0;

        foreach ($_SESSION['cart'] as $item) {
            $priceNet = floatval($item['netPrice']);
            $vatRate = floatval($item['vat']); // np. 0.23
            $priceGross = $priceNet * (1 + $vatRate);
            $valueGross = $priceGross * $item['quantity'];
            $totalValue += $valueGross;

            echo '<tr>';
            echo '<td>' . htmlspecialchars($item['title']) . '</td>';
            echo '<td>' . number_format($priceNet, 2) . ' zł</td>';
            echo '<td>' . ($vatRate * 100) . '%</td>';
            echo '<td>' . number_format($priceGross, 2) . ' zł</td>';
            echo '<td><input type="number" name="quantity[' . $item['id'] . ']" value="' . $item['quantity'] . '" min="0" class="form-input" style="width: 80px; padding: 5px;"></td>';
            echo '<td>' . number_format($valueGross, 2) . ' zł</td>';
            echo '<td><button type="submit" name="remove" value="' . $item['id'] . '" class="btn btn-secondary" style="padding: 5px 10px; font-size: 0.8rem;">Usuń</button></td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '<tfoot>';
        echo '<tr>
                <td colspan="5" style="text-align: right; border-bottom: none;"><strong>RAZEM:</strong></td>
                <td colspan="2" style="border-bottom: none; font-size: 1.2rem; color: var(--accent-color);"><strong>' . number_format($totalValue, 2) . ' zł</strong></td>
              </tr>';
        echo '</tfoot>';
        echo '</table>';
        echo '</div>';
        echo '<div style="margin-top: 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">';
        echo '<div><a href="index.php?idp=sklep" class="btn btn-secondary">Wróć do zakupów</a></div>';
        echo '<div style="display: flex; flex-direction: column; align-items: flex-end; gap: 10px;">';

        if (!isset($_SESSION['user_id'])) {
            echo '<input type="email" name="guest_email" placeholder="Twój e-mail (dla gości)" class="form-input" style="width: 250px;">';
        }

        echo '<div>';
        echo '<input type="submit" name="update" value="Zaktualizuj koszyk" class="btn btn-secondary" style="margin-right: 10px;">';
        echo '<input type="submit" name="checkout" value="Kup teraz" class="btn btn-primary" onclick="return confirm(\'Czy na pewno chcesz złożyć zamówienie?\')">';
        echo '</div>';
        echo '</div>';
        echo '</div>';
        echo '</form>';
        echo '</div>';
    }

    /**
     * Obsługuje akcje koszyka przesyłane metodą POST (dodawanie, usuwanie, update, checkout).
     * Powinna być wywoływana przed generowaniem widoku.
     */
    public function handleCartAction()
    {
        // Obsługa dodawania do koszyka
        if (isset($_POST['add_to_cart'])) {
            $id = intval($_POST['id']);
            // Pobranie danych produktu z bazy żeby mieć pewność ceny
            $query = "SELECT * FROM products WHERE id='$id' LIMIT 1";
            $result = mysqli_query($this->db_link, $query);
            if ($row = mysqli_fetch_assoc($result)) {
                $this->addToCart($row['id'], $row['tytul'], 1, $row['cena_netto'], $row['podatek_vat']);
                echo '<div class="notification success" style="max-width: 600px; margin: 20px auto; padding: 15px; border-radius: 8px; text-align: center;">';
                echo '<p style="margin: 0; font-weight: bold;">Produkt dodany do koszyka!</p>';
                echo '<div style="margin-top: 10px;">';
                echo '<a href="index.php?idp=koszyk" class="btn btn-primary" style="font-size: 0.9rem; padding: 8px 15px;">Przejdź do koszyka</a> ';
                echo '<a href="index.php?idp=sklep" class="btn btn-secondary" style="font-size: 0.9rem; padding: 8px 15px;">Kontynuuj zakupy</a>';
                echo '</div>';
                echo '</div>';
            }
        }

        // Obsługa usuwania pojedynczej pozycji
        if (isset($_POST['remove'])) {
            $id = intval($_POST['remove']);
            $this->removeFromCart($id);
            echo "<p>Produkt usunięty z koszyka.</p>";
        }

        // Obsługa aktualizacji ilości
        if (isset($_POST['update'])) {
            if (isset($_POST['quantity']) && is_array($_POST['quantity'])) {
                foreach ($_POST['quantity'] as $id => $qty) {
                    $this->updateQuantity($id, intval($qty));
                }
                echo "<p>Koszyk zaktualizowany.</p>";
            }
        }

        // Obsługa checkoutu
        if (isset($_POST['checkout'])) {
            $this->checkout();
        }
    }
}
?>