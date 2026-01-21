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
    <div class="logowanie">
        <h1 class="heading">Panel CMS:</h1>
        <div class="logowanie">
            <form method="post" name="LoginForm" enctype="multipart/form-data" action="' . htmlspecialchars($_SERVER['REQUEST_URI']) . '">
                <table class="logowanie">
                    <tr><td class="log4_t">Login:</td><td><input type="text" name="login_email" class="logowanie" /></td></tr>
                    <tr><td class="log4_t">Hasło:</td><td><input type="password" name="login_pass" class="logowanie" /></td></tr>
                    <tr><td>&nbsp;</td><td><input type="submit" name="x1_submit" class="logowanie" value="Zaloguj" /></td></tr>
                    <tr><td colspan="2" align="center"><a href="admin.php?action=forgot_password">Zapomniałeś hasła?</a></td></tr>
                </table>
            </form>
        </div>
    </div>
    ';
    return $wynik;
}

// Obsługa przypomnienia hasła
if (isset($_GET['action']) && $_GET['action'] == 'forgot_password') {
    if (isset($_POST['recover_email'])) {
        PrzypomnijHaslo($_POST['recover_email']);
        echo '<br><a href="admin.php">Powrót do logowania</a>';
        exit;
    } else {
        echo '
        <div class="logowanie">
            <h1 class="heading">Przypomnienie hasła:</h1>
            <div class="logowanie">
                <form method="post" name="RecoverForm" enctype="multipart/form-data" action="admin.php?action=forgot_password">
                    <table class="logowanie">
                        <tr><td class="log4_t">Email:</td><td><input type="text" name="recover_email" class="logowanie" /></td></tr>
                        <tr><td>&nbsp;</td><td><input type="submit" name="x2_submit" class="logowanie" value="Wyślij hasło" /></td></tr>
                    </table>
                </form>
            </div>
        </div>
        ';
        exit;
    }
}

// Weryfikacja danych logowania
if (isset($_POST['login_email']) && isset($_POST['login_pass'])) {
    // Tutaj porównujemy wprost z danymi z cfg.php, bez zapytania SQL, więc injection mniej groźny,
    // ale warto dbać o spójność.
    if ($_POST['login_email'] == $login && $_POST['login_pass'] == $pass) {
        $_SESSION['zalogowany'] = 1;
    } else {
        echo '<div style="color:red; font-weight:bold; padding:10px;">Logowanie nie powiodło się. Spróbuj ponownie.</div>';
        echo FormularzLogowania();
        exit;
    }
}

// Sprawdzenie sesji (czy użytkownik jest zalogowany)
if (!isset($_SESSION['zalogowany']) || $_SESSION['zalogowany'] != 1) {
    echo FormularzLogowania();
    exit;
}

echo "Witamy w panelu administracyjnym!<br><br>";
echo '<a href="admin.php?action=logout">Wyloguj</a><br>';

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

    echo '
    <div class="dodawanie">
        <h3 class="heading">Dodaj nową stronę</h3>
        <form method="post" action="' . htmlspecialchars($_SERVER['REQUEST_URI']) . '">
            <table>
                <tr>
                    <td>Tytuł podstrony:</td>
                    <td><input type="text" name="page_title" size="50" /></td>
                </tr>
                <tr>
                    <td>Treść strony:</td>
                    <td><textarea rows="20" cols="100" name="page_content"></textarea></td>
                </tr>
                <tr>
                    <td>Strona aktywna:</td>
                    <td><input type="checkbox" name="status" checked /></td>
                </tr>
                <tr>
                    <td></td>
                    <td><input type="submit" name="x1_submit" value="Dodaj stronę" /></td>
                </tr>
            </table>
        </form>
        <br><a href="admin.php">Anuluj</a>
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

    echo '
    <div class="edycja">
        <h3 class="heading">Edytuj stronę: ' . htmlspecialchars($title) . '</h3>
        <form method="post" action="">
            <table>
                <tr>
                    <td>Tytuł podstrony:</td>
                    <td><input type="text" name="page_title" size="50" value="' . htmlspecialchars($title) . '" /></td>
                </tr>
                <tr>
                    <td>Treść strony:</td>
                    <td><textarea rows="20" cols="100" name="page_content">' . htmlspecialchars($content) . '</textarea></td>
                </tr>
                <tr>
                    <td>Strona aktywna:</td>
                    <td><input type="checkbox" name="status" ' . $is_active . ' /></td>
                </tr>
                <tr>
                    <td></td>
                    <td><input type="submit" name="submit_edit" value="Zapisz zmiany" /></td>
                </tr>
            </table>
        </form>
         <br><a href="admin.php">Anuluj</a>
    </div>
    ';
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
if (isset($_GET['funkcja']) && $_GET['funkcja'] == 'edytuj') {
    EdytujPodstrone();
} elseif (isset($_GET['funkcja']) && $_GET['funkcja'] == 'dodaj') {
    DodajNowaPodstrone();
} elseif (isset($_GET['funkcja']) && $_GET['funkcja'] == 'usun') {
    UsunPodstrone();
} else {
    ListaPodstron();
}
?>