<?php

class ShoppingCart
{
    private $db_link;

    public function __construct($link)
    {
        $this->db_link = $link;
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    // Dodawanie produktu do koszyka
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

        // Jeśli nie ma, dodaj nowy
        if (!$found) {
            $_SESSION['cart'][] = [
                'id' => $id,
                'title' => $title,
                'quantity' => $quantity,
                'netPrice' => $netPrice,
                'vat' => $vat,
                'date' => time() // opcjonalnie data dodania
            ];
        }

        // Przekierowanie aby uniknąć ponownego wysłania formularza
        // header("Location: index.php?idp=koszyk"); 
        // W tym przypadku może po prostu zwrócić komunikat
    }

    // Usuwanie produktu z koszyka
    public function removeFromCart($id)
    {
        if (isset($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $key => $product) {
                if ($product['id'] == $id) {
                    unset($_SESSION['cart'][$key]);
                    // Reindeksacja tablicy, aby uniknąć dziur (chociaż foreach sobie radzi, ale dla porządku)
                    $_SESSION['cart'] = array_values($_SESSION['cart']);
                    break;
                }
            }
        }
    }

    // Aktualizacja ilości
    // Aktualizacja ilości - Checkout logic needs to use this potentially? No, stock check is separate.
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
        }
    }

    // Finalizacja zamówienia
    public function checkout()
    {
        if (empty($_SESSION['cart']))
            return;

        $errors = [];
        // Sprawdzenie dostępności
        foreach ($_SESSION['cart'] as $item) {
            $id = $item['id'];
            $qty = $item['quantity'];

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

        // Aktualizacja stanów magazynowych
        foreach ($_SESSION['cart'] as $item) {
            $id = $item['id'];
            $qty = $item['quantity'];
            $query = "UPDATE products SET ilosc_dostepnych_sztuk = ilosc_dostepnych_sztuk - $qty WHERE id='$id'";
            mysqli_query($this->db_link, $query);
        }

        // Wyczyszczenie koszyka
        unset($_SESSION['cart']);
        echo "<p style='color:green; font-weight:bold; font-size:1.2em;'>Zamówienie zrealizowane pomyślnie! Dziękujemy za zakupy.</p>";
    }

    // Wyświetlanie koszyka
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
        echo '<div style="display: flex; gap: 10px;">';
        echo '<input type="submit" name="update" value="Zaktualizuj koszyk" class="btn btn-secondary">';
        echo '<input type="submit" name="checkout" value="Kup teraz" class="btn btn-primary" onclick="return confirm(\'Czy na pewno chcesz złożyć zamówienie?\')">';
        echo '</div>';
        echo '</div>';
        echo '</form>';
        echo '</div>';
    }

    public function handleCartAction()
    {
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

        if (isset($_POST['remove'])) {
            $id = intval($_POST['remove']);
            $this->removeFromCart($id);
            echo "<p>Produkt usunięty z koszyka.</p>";
        }

        if (isset($_POST['update'])) {
            if (isset($_POST['quantity']) && is_array($_POST['quantity'])) {
                foreach ($_POST['quantity'] as $id => $qty) {
                    $this->updateQuantity($id, intval($qty));
                }
                echo "<p>Koszyk zaktualizowany.</p>";
            }
        }

        if (isset($_POST['checkout'])) {
            $this->checkout();
        }
    }
}
?>