<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administratora</title>
    <link rel="stylesheet" href="../css/style.css?v=<?php echo time(); ?>">
    <style>
        body {
            background-color: var(--primary-color);
            padding: 20px;
        }
    </style>
</head>

<body>

    <?php
    /**
     * Panel administracyjny CMS
     * Umożliwia zarządzanie podstronami (dodawanie, edycja, usuwanie)
     */

    session_start();
    include('../cfg.php');
    include('../php/contact.php');

    // Obsługa wylogowania
    if (isset($_GET['action']) && $_GET['action'] == 'logout') {
        session_unset();
        session_destroy();
        header("Location: admin.php");
        exit;
    }

    /**
     * Wyświetla formularz logowania do panelu administracyjnego
     * 
     * @return string Kod HTML formularza logowania
     */
    function FormularzLogowania()
    {
        $wynik = '
    <div class="contact-container" style="max-width: 400px; margin: 100px auto;">
        <h1 class="heading" style="text-align: center;">Panel CMS</h1>
        <form method="post" name="LoginForm" enctype="multipart/form-data" action="' . htmlspecialchars($_SERVER['REQUEST_URI']) . '">
            <div class="form-group">
                <label>Login:</label>
                <input type="text" name="login_email" class="form-input" />
            </div>
            <div class="form-group">
                <label>Hasło:</label>
                <input type="password" name="login_pass" class="form-input" />
            </div>
            <div class="form-group">
                <input type="submit" name="x1_submit" class="form-button" value="Zaloguj" />
            </div>
            <div style="text-align: center;">
                <a href="admin.php?action=forgot_password" style="color: grey; font-size: 0.9em;">Zapomniałeś hasła?</a>
            </div>
        </form>
    </div>
    ';
        return $wynik;
    }

    // Obsługa przypomnienia hasła
    if (isset($_GET['action']) && $_GET['action'] == 'forgot_password') {
        if (isset($_POST['recover_email'])) {
            PrzypomnijHaslo($_POST['recover_email']);
            echo '<div class="notification success"><h3>Wysłano hasło!</h3></div>';
            echo '<br><center><a href="admin.php" class="btn btn-secondary">Powrót do logowania</a></center>';
            exit;
        } else {
            echo '
        <div class="contact-container" style="max-width: 400px; margin: 100px auto;">
            <h1 class="heading">Przypomnienie hasła</h1>
            <form method="post" name="RecoverForm" enctype="multipart/form-data" action="admin.php?action=forgot_password">
                <div class="form-group">
                    <label>Email:</label>
                    <input type="text" name="recover_email" class="form-input" />
                </div>
                <div class="form-group">
                    <input type="submit" name="x2_submit" class="form-button" value="Wyślij hasło" />
                </div>
            </form>
        </div>
        ';
            exit;
        }
    }

    // Weryfikacja danych logowania
    if (isset($_POST['login_email']) && isset($_POST['login_pass'])) {
        if ($_POST['login_email'] == $login && $_POST['login_pass'] == $pass) {
            $_SESSION['zalogowany'] = 1;
        } else {
            echo '<div class="admin-panel" style="max-width: 400px; margin: 20px auto; text-align: center; color: #e84118;">Logowanie nie powiodło się. Spróbuj ponownie.</div>';
            echo FormularzLogowania();
            exit;
        }
    }

    // Sprawdzenie sesji (czy użytkownik jest zalogowany)
    if (!isset($_SESSION['zalogowany']) || $_SESSION['zalogowany'] != 1) {
        echo FormularzLogowania();
        exit;
    }

    echo '<div class="admin-panel">';
    echo '<div class="admin-nav">';
    echo '<span>Witaj w Panelu!</span> ';
    echo '<span style="float: right;"><a href="admin.php?action=logout" style="color: #e84118;">[Wyloguj]</a></span>';
    echo '<br><br>';
    echo '<a href="admin.php">Strony</a>';
    echo '<a href="admin.php?type=categories">Kategorie</a>';
    echo '<a href="admin.php?type=products">Produkty</a>';
    echo '<a href="admin.php?type=orders">Zamówienia</a>';
    echo '<a href="../index.php" target="_blank">Podgląd Sklepu</a>';
    echo '</div>';

    /**
     * Wyświetla listę wszystkich podstron z opcjami edycji i usuwania
     */
    function ListaPodstron()
    {
        global $link;
        $query = "SELECT id, page_title FROM page_list ORDER BY id ASC LIMIT 100";
        $result = mysqli_query($link, $query);

        echo '<table border="1" cellpadding="5" cellspacing="0">';
        echo '<tr><th>ID</th><th>Tytuł Podstrony</th><th>Opcje</th></tr>';

        while ($row = mysqli_fetch_array($result)) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($row['id']) . '</td>';
            echo '<td>' . htmlspecialchars($row['page_title']) . '</td>';
            echo '<td>';
            echo '<a href="admin.php?funkcja=edytuj&id=' . $row['id'] . '" class="btn btn-primary btn-sm">Edytuj</a> ';
            echo '<a href="admin.php?funkcja=usun&id=' . $row['id'] . '" class="btn btn-danger btn-sm" onclick="return confirm(\'Czy na pewno usunąć?\')">Usuń</a>';
            echo '</td>';
            echo '</tr>';
        }
        echo '</table>';
        echo '<br><a href="admin.php?funkcja=dodaj" class="btn btn-primary">Dodaj nową podstronę</a>';
    }

    /**
     * Wyświetla formularz dodawania podstrony i obsługuje proces dodawania
     */
    function DodajNowaPodstrone()
    {
        global $link;

        if (isset($_POST['x1_submit'])) {
            // Zabezpieczenie przed SQL Injection
            $title = mysqli_real_escape_string($link, $_POST['page_title']);
            $content = mysqli_real_escape_string($link, $_POST['page_content']);
            $status = isset($_POST['status']) ? 1 : 0;

            $query = "INSERT INTO page_list (page_title, page_content, status) VALUES ('$title', '$content', '$status')";
            $result = mysqli_query($link, $query);

            if ($result) {
                echo "Strona została dodana pomyślnie.<br><a href='admin.php'>Powrót do listy</a>";
            } else {
                echo "Błąd podczas dodawania strony: " . mysqli_error($link);
            }
            return;
        }

        echo '<h3 class="heading">Dodaj nową stronę</h3>';
        echo '<div class="contact-container" style="max-width: 800px; margin: 0 auto;">
        <form method="post" action="' . htmlspecialchars($_SERVER['REQUEST_URI']) . '">
            <div class="form-group">
                <label>Tytuł podstrony:</label>
                <input type="text" name="page_title" class="form-input" />
            </div>
            <div class="form-group">
                <label>Treść strony:</label>
                <textarea rows="20" name="page_content" class="form-input" style="height: 300px;"></textarea>
            </div>
            <div class="form-group">
                <label>Strona aktywna:</label>
                <input type="checkbox" name="status" checked style="transform: scale(1.5); margin: 10px;" />
            </div>
            <div class="form-group">
                <input type="submit" name="x1_submit" class="form-button" value="Dodaj stronę" />
            </div>
        </form>
        <br><a href="admin.php" class="btn btn-secondary">Anuluj</a>
    </div>
    ';
    }

    /**
     * Wyświetla formularz edycji podstrony i obsługuje proces zapisu zmian
     */
    function EdytujPodstrone()
    {
        global $link;

        if (isset($_GET['id'])) {
            // Zabezpieczenie ID przed SQL Injection
            $id = mysqli_real_escape_string($link, $_GET['id']);
        } else {
            echo "Brak ID strony do edycji.";
            return;
        }

        // Obsługa zapisu formularza
        if (isset($_POST['submit_edit'])) {
            $title = mysqli_real_escape_string($link, $_POST['page_title']);
            $content = mysqli_real_escape_string($link, $_POST['page_content']);
            $status = isset($_POST['status']) ? 1 : 0;

            $query = "UPDATE page_list SET page_title='$title', page_content='$content', status='$status' WHERE id='$id' LIMIT 1";
            $result = mysqli_query($link, $query);

            if ($result) {
                echo "Strona została zaktualizowana.<br><a href='admin.php'>Powrót do listy</a>";
                return;
            } else {
                echo "Błąd podczas aktualizacji strony: " . mysqli_error($link);
            }
        }

        // Pobranie danych do edycji
        $query = "SELECT * FROM page_list WHERE id='$id' LIMIT 1";
        $result = mysqli_query($link, $query);
        $row = mysqli_fetch_array($result);

        if (!$row) {
            echo "Nie znaleziono strony.";
            return;
        }

        $title = $row['page_title'];

        $content = $row['page_content'];
        $is_active = $row['status'] == 1 ? 'checked' : '';

        echo '<h3 class="heading">Edytuj stronę: ' . htmlspecialchars($title) . '</h3>';
        echo '<div class="contact-container" style="max-width: 800px; margin: 0 auto;">
        <form method="post" action="">
            <div class="form-group">
                <label>Tytuł podstrony:</label>
                <input type="text" name="page_title" class="form-input" value="' . htmlspecialchars($title) . '" />
            </div>
            <div class="form-group">
                <label>Treść strony:</label>
                <textarea rows="20" name="page_content" class="form-input" style="height: 300px;">' . htmlspecialchars($content) . '</textarea>
            </div>
            <div class="form-group">
                <label>Strona aktywna:</label>
                <input type="checkbox" name="status" ' . $is_active . ' style="transform: scale(1.5); margin: 10px;" />
            </div>
            <div class="form-group">
                <input type="submit" name="submit_edit" class="form-button" value="Zapisz zmiany" />
            </div>
        </form>
         <br><a href="admin.php" class="btn btn-secondary">Anuluj</a>
    </div>
    </div>';
    }

    /**
     * Usuwa podstronę o podanym ID
     */
    function UsunPodstrone()
    {
        global $link;

        if (isset($_GET['id'])) {
            $id = mysqli_real_escape_string($link, $_GET['id']);

            $query = "DELETE FROM page_list WHERE id='$id' LIMIT 1";
            $result = mysqli_query($link, $query);

            if ($result) {
                echo "Strona została usunięta.<br><a href='admin.php'>Powrót do listy</a>";
            } else {
                echo "Błąd podczas usuwania strony: " . mysqli_error($link);
            }
        } else {
            echo "Brak ID strony do usunięcia.";
        }
    }

    /**
     * Wyświetla listę wszystkich zamówień w sklepie oraz szczegóły pojedynczego zamówienia
     */
    function PokazZamowienia()
    {
        global $link;

        // --- Obsługa Zmiany Statusu ---
        if (isset($_POST['update_status']) && isset($_POST['order_id']) && isset($_POST['status'])) {
            $orderId = intval($_POST['order_id']);
            $newStatus = mysqli_real_escape_string($link, $_POST['status']);
            $queryUpdate = "UPDATE orders SET status='$newStatus' WHERE id='$orderId' LIMIT 1";
            if (mysqli_query($link, $queryUpdate)) {
                echo '<div class="notification success">Status zamówienia #' . $orderId . ' został zmieniony na: ' . htmlspecialchars($newStatus) . '</div>';
            } else {
                echo '<div class="notification error">Błąd zmiany statusu: ' . mysqli_error($link) . '</div>';
            }
        }

        // --- Obsługa Usuwania Zamówienia ---
        if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $queryDelete = "DELETE FROM orders WHERE id='$id' LIMIT 1";
            if (mysqli_query($link, $queryDelete)) {
                echo '<div class="notification success">Zamówienie #' . $id . ' zostało usunięte.</div>';
            } else {
                echo '<div class="notification error">Błąd usuwania zamówienia: ' . mysqli_error($link) . '</div>';
            }
        }

        // --- Widok Szczegółów Zamówienia ---
        if (isset($_GET['action']) && $_GET['action'] == 'view' && isset($_GET['id'])) {
            $id = intval($_GET['id']);
            echo '<h3 class="heading">Szczegóły Zamówienia #' . $id . '</h3>';

            // Pobranie danych zamówienia
            $queryOrder = "SELECT orders.*, users.login FROM orders LEFT JOIN users ON orders.user_id = users.id WHERE orders.id = '$id' LIMIT 1";
            $resultOrder = mysqli_query($link, $queryOrder);
            $order = mysqli_fetch_assoc($resultOrder);

            if (!$order) {
                echo '<div class="notification error">Nie znaleziono zamówienia.</div>';
                echo '<a href="admin.php?type=orders" class="btn btn-secondary">Powrót do listy</a>';
                return;
            }

            // Formularz zmiany statusu
            echo '<div class="contact-container" style="max-width: 800px; margin: 20px auto;">';
            echo '<form method="post" action="admin.php?type=orders&action=view&id=' . $id . '" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap;">';
            echo '<div><strong>Status:</strong> ';

            $statuses = ['nowe', 'oczekujące', 'w realizacji', 'wysłane', 'zakończone', 'anulowane'];
            echo '<select name="status" class="form-input" style="display: inline-block; width: auto; padding: 5px; margin-left: 10px;">';
            foreach ($statuses as $st) {
                $selected = ($order['status'] == $st) ? 'selected' : '';
                echo '<option value="' . $st . '" ' . $selected . '>' . ucfirst($st) . '</option>';
            }
            echo '</select>';
            echo '</div>';
            echo '<input type="hidden" name="order_id" value="' . $id . '">';
            echo '<input type="submit" name="update_status" value="Zmień status" class="btn btn-primary btn-sm" style="margin-left: 10px;">';
            echo '</form>';

            echo '<hr style="margin: 15px 0; border: 0; border-top: 1px solid #ddd;">';

            echo '<p><strong>Data złożenia:</strong> ' . $order['created_at'] . '</p>';

            $email = $order['email'];
            if (empty($email) && $order['user_id'] > 0) {
                $email = $order['login'];
            }
            echo '<p><strong>Email klienta:</strong> ' . htmlspecialchars($email) . '</p>';

            if ($order['user_id'] > 0) {
                echo '<p><strong>Konto użytkownika:</strong> ' . htmlspecialchars($order['login']) . ' (ID: ' . $order['user_id'] . ')</p>';
            } else {
                echo '<p><strong>Typ klienta:</strong> Gość (bez rejestracji)</p>';
            }
            echo '</div>';

            // Pobranie pozycji zamówienia
            echo '<h4 class="heading">Pozycje zamówienia</h4>';
            $queryItems = "SELECT oi.*, p.tytul FROM order_items oi LEFT JOIN products p ON oi.product_id = p.id WHERE oi.order_id = '$id'";
            $resultItems = mysqli_query($link, $queryItems);

            echo '<div class="table-responsive" style="max-width: 800px; margin: 0 auto;">';
            echo '<table class="ranking-table">';
            echo '<tr><th>Produkt</th><th>Ilość</th><th>Cena Jedn. Brutto</th><th>Wartość</th></tr>';

            while ($item = mysqli_fetch_assoc($resultItems)) {
                $productName = $item['tytul'] ? htmlspecialchars($item['tytul']) : '<span style="color:red;">Produkt usunięty (ID: ' . $item['product_id'] . ')</span>';
                $value = $item['quantity'] * $item['price_gross'];

                echo '<tr>';
                echo '<td>' . $productName . '</td>';
                echo '<td style="text-align: center;">' . $item['quantity'] . '</td>';
                echo '<td style="text-align: right;">' . number_format($item['price_gross'], 2) . ' zł</td>';
                echo '<td style="text-align: right;">' . number_format($value, 2) . ' zł</td>';
                echo '</tr>';
            }
            echo '<tr><td colspan="3" style="text-align: right;"><strong>RAZEM:</strong></td><td style="text-align: right; color: var(--accent-color);"><strong>' . number_format($order['total_amount'], 2) . ' zł</strong></td></tr>';
            echo '</table>';
            echo '</div>';

            echo '<div style="text-align: center; margin-top: 30px; display: flex; justify-content: center; gap: 20px;">';
            echo '<a href="admin.php?type=orders" class="btn btn-secondary">Powrót do listy</a>';
            echo '<a href="admin.php?type=orders&action=delete&id=' . $id . '" class="btn btn-danger" onclick="return confirm(\'Czy na pewno chcesz usunąć to zamówienie? Nie można tego cofnąć.\')">Usuń zamówienie</a>';
            echo '</div>';

            return;
        }

        // --- Widok Listy Zamówień ---
        echo '<h3 class="heading">Zarządzaj Zamówieniami</h3>';

        $query = "SELECT orders.*, users.login FROM orders LEFT JOIN users ON orders.user_id = users.id ORDER BY created_at DESC";
        $result = mysqli_query($link, $query);

        if (mysqli_num_rows($result) > 0) {
            echo '<div class="table-responsive">';
            echo '<table class="ranking-table">';
            echo '<tr><th>ID</th><th>Klient</th><th>Data</th><th>Status</th><th>Kwota</th><th>Akcje</th></tr>';
            while ($row = mysqli_fetch_assoc($result)) {
                $userDisplay = '';
                if (!empty($row['email'])) {
                    $userDisplay = htmlspecialchars($row['email']);
                    if ($row['user_id'] > 0)
                        $userDisplay .= ' <br><small>(User ID: ' . $row['user_id'] . ')</small>';
                    else
                        $userDisplay .= ' <br><small>(Gość)</small>';
                } elseif (!empty($row['login'])) {
                    $userDisplay = htmlspecialchars($row['login']) . ' <br><small>(ID: ' . $row['user_id'] . ')</small>';
                } else {
                    $userDisplay = 'Nieznany';
                }

                echo '<tr>';
                echo '<td>#' . $row['id'] . '</td>';
                echo '<td>' . $userDisplay . '</td>';
                echo '<td>' . $row['created_at'] . '</td>';
                echo '<td>' . ucfirst($row['status']) . '</td>';
                echo '<td>' . number_format($row['total_amount'], 2) . ' zł</td>';
                echo '<td>';
                echo '<td>';
                echo '<a href="admin.php?type=orders&action=view&id=' . $row['id'] . '" class="btn btn-primary btn-sm" style="margin-right: 5px;">Szczegóły</a>';
                echo '<a href="admin.php?type=orders&action=delete&id=' . $row['id'] . '" class="btn btn-danger btn-sm" onclick="return confirm(\'Czy na pewno usunąć?\')">Usuń</a>';
                echo '</td>';
                echo '</tr>';
            }
            echo '</table>';
            echo '</div>';
        } else {
            echo '<p>Brak zamówień w systemie.</p>';
        }
    }

    // Główny router akcji w panelu admina
    include('categories.php'); // Dołączenie logiki kategorii
    include('products.php');   // Dołączenie logiki produktów
    
    if (isset($_GET['funkcja']) && $_GET['funkcja'] == 'edytuj') {
        EdytujPodstrone();
    } elseif (isset($_GET['funkcja']) && $_GET['funkcja'] == 'dodaj') {
        DodajNowaPodstrone();
    } elseif (isset($_GET['funkcja']) && $_GET['funkcja'] == 'usun') {
        UsunPodstrone();
    } elseif (isset($_GET['type']) && $_GET['type'] == 'categories') { // Obsługa kategorii
        $kategorie = new Kategorie($link);
        $kategorie->ZarzadzajKategoriami();
    } elseif (isset($_GET['type']) && $_GET['type'] == 'products') { // Obsługa produktów
        $produkty = new Produkty($link);
        $produkty->ZarzadzajProduktami();
    } elseif (isset($_GET['type']) && $_GET['type'] == 'orders') { // Obsługa zamówień
        PokazZamowienia();
    } else {
        ListaPodstron();
    }

    echo '</div>';
    ?>
</body>

</html>