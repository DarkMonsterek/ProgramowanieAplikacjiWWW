<?php
session_start();
include('../cfg.php');

function FormularzLogowania()
{
    $wynik = '
    <div class="logowanie">
        <h1 class="heading">Panel CMS:</h1>
        <div class="logowanie">
            <form method="post" name="LoginForm" enctype="multipart/form-data" action="' . $_SERVER['REQUEST_URI'] . '">
                <table class="logowanie">
                    <tr><td class="log4_t">[email]</td><td><input type="text" name="login_email" class="logowanie" /></td></tr>
                    <tr><td class="log4_t">[haslo]</td><td><input type="password" name="login_pass" class="logowanie" /></td></tr>
                    <tr><td>&nbsp;</td><td><input type="submit" name="x1_submit" class="logowanie" value="zaloguj" /></td></tr>
                </table>
            </form>
        </div>
    </div>
    ';
    return $wynik;
}

if (isset($_POST['login_email']) && isset($_POST['login_pass'])) {
    if ($_POST['login_email'] == $login && $_POST['login_pass'] == $pass) {
        $_SESSION['zalogowany'] = 1;
    } else {
        echo '<div style="color:red; font-weight:bold; padding:10px;">Logowanie nie powiodło się. Spróbuj ponownie.</div>';
        echo FormularzLogowania();
        exit;
    }
}

if (!isset($_SESSION['zalogowany']) || $_SESSION['zalogowany'] != 1) {
    echo FormularzLogowania();
    exit;
}

echo "Witamy w panelu administracyjnym!<br><br>";

function ListaPodstron()
{
    global $link;
    $query = "SELECT id, page_title FROM page_list ORDER BY id ASC LIMIT 100";
    $result = mysqli_query($link, $query);

    echo '<table border="1" cellpadding="5" cellspacing="0">';
    echo '<tr><th>ID</th><th>Tytuł Podstrony</th><th>Opcje</th></tr>';

    while ($row = mysqli_fetch_array($result)) {
        echo '<tr>';
        echo '<td>' . $row['id'] . '</td>';
        echo '<td>' . $row['page_title'] . '</td>';
        echo '<td>';
        echo '<a href="admin.php?funkcja=usun&id=' . $row['id'] . '">Usuń</a> | ';
        echo '<a href="admin.php?funkcja=edytuj&id=' . $row['id'] . '">Edytuj</a>';
        echo '</td>';
        echo '</tr>';
    }
    echo '</table>';
    echo '<br><a href="admin.php?funkcja=dodaj">Dodaj nową podstronę</a>';
}

function DodajNowaPodstrone()
{
    global $link;

    if (isset($_POST['x1_submit'])) {
        $title = $_POST['page_title'];
        $content = $_POST['page_content'];
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
        <form method="post" action="' . $_SERVER['REQUEST_URI'] . '">
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
    </div>
    ';
}

function EdytujPodstrone()
{
    global $link;

    if (isset($_GET['id'])) {
        $id = $_GET['id'];
    } else {
        echo "Brak ID strony do edycji.";
        return;
    }

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

    echo '
    <div class="edycja">
        <h3 class="heading">Edytuj stronę: ' . $title . '</h3>
        <form method="post" action="' . $_SERVER['REQUEST_URI'] . '">
            <table>
                <tr>
                    <td>Tytuł podstrony:</td>
                    <td><input type="text" name="page_title" size="50" value="' . $title . '" /></td>
                </tr>
                <tr>
                    <td>Treść strony:</td>
                    <td><textarea rows="20" cols="100" name="page_content">' . $content . '</textarea></td>
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
    </div>
    ';
}

function UsunPodstrone()
{
    global $link;

    if (isset($_GET['id'])) {
        $id = $_GET['id'];

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