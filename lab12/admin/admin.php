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
    include('../contact.php');

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
            echo '<a href="admin.php?funkcja=usun&id=' . $row['id'] . '">Usuń</a> | ';
            echo '<a href="admin.php?funkcja=edytuj&id=' . $row['id'] . '">Edytuj</a>';
            echo '</td>';
            echo '</tr>';
        }
        echo '</table>';
        echo '<br><a href="admin.php?funkcja=dodaj">Dodaj nową podstronę</a>';
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
        <br><a href="admin.php" class="action-btn">Anuluj</a>
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

        $title = $row['page_title']; // Nie używamy htmlspecialchars w value inputa, bo chcemy edytować kod źródłowy (?) 
        // Zazwyczaj przy edycji treści HTML w textarea nie robimy htmlspecialchars, chyba że to edytor wizualny.
        // Tutaj zakładam, że admin wpisuje HTML.
        // Jednak tytuł bezpieczniej wyświetlić w input:value jako htmlspecialchars
    
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
         <br><a href="admin.php" class="action-btn">Anuluj</a>
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
    } else {
        ListaPodstron();
    }

    echo '</div>'; // Close admin-panel div
    ?>
</body>

</html>