<?php

class Produkty
{
    private $link;

    public function __construct($db_link)
    {
        $this->link = $db_link;
    }

    public function DodajProdukt()
    {
        if (isset($_POST['dodaj_produkt'])) {
            $tytul = mysqli_real_escape_string($this->link, $_POST['tytul']);
            $opis = mysqli_real_escape_string($this->link, $_POST['opis']);
            $data_wygasniecia = $_POST['data_wygasniecia'] ? "'" . mysqli_real_escape_string($this->link, $_POST['data_wygasniecia']) . "'" : "NULL";
            $cena_netto = floatval($_POST['cena_netto']);
            $podatek_vat = floatval($_POST['podatek_vat']);
            $ilosc = intval($_POST['ilosc']);
            $kategoria = intval($_POST['kategoria']);
            $gabaryt = mysqli_real_escape_string($this->link, $_POST['gabaryt']);
            $zdjecie = mysqli_real_escape_string($this->link, $_POST['zdjecie']);

            // Status: 1 - Dostępny, 0 - Niedostępny.
            $status = ($ilosc > 0) ? 1 : 0;
            if ($_POST['data_wygasniecia'] && strtotime($_POST['data_wygasniecia']) < time()) {
                $status = 0;
            }

            $query = "INSERT INTO products (tytul, opis, data_wygasniecia, cena_netto, podatek_vat, ilosc_dostepnych_sztuk, status_dostepnosci, kategoria, gabaryt_produktu, zdjecie) 
                      VALUES ('$tytul', '$opis', $data_wygasniecia, '$cena_netto', '$podatek_vat', '$ilosc', '$status', '$kategoria', '$gabaryt', '$zdjecie')";

            if (mysqli_query($this->link, $query)) {
                echo "<p style='color:green;'>Produkt dodany pomyślnie.</p>";
            } else {
                echo "<p style='color:red;'>Błąd: " . mysqli_error($this->link) . "</p>";
            }
        }

        echo '<h3 class="heading">Dodaj Produkt</h3>';
        echo '<div class="contact-container" style="max-width: 800px; margin: 0 auto;">';
        echo '<form method="post" action="">';
        echo '<div class="form-group"><label>Tytuł:</label><input type="text" name="tytul" class="form-input" required></div>';
        echo '<div class="form-group"><label>Opis:</label><textarea name="opis" class="form-input"></textarea></div>';
        echo '<div class="form-group"><label>Data wygaśnięcia:</label><input type="datetime-local" name="data_wygasniecia" class="form-input"></div>';
        echo '<div class="form-group"><label>Cena netto:</label><input type="number" step="0.01" name="cena_netto" class="form-input" required></div>';
        echo '<div class="form-group"><label>Podatek VAT (np. 0.23):</label><input type="number" step="0.01" name="podatek_vat" value="0.23" class="form-input"></div>';
        echo '<div class="form-group"><label>Ilość sztuk:</label><input type="number" name="ilosc" class="form-input" required></div>';
        echo '<div class="form-group"><label>Kategoria (ID):</label><input type="number" name="kategoria" class="form-input"></div>';
        echo '<div class="form-group"><label>Gabaryt:</label><input type="text" name="gabaryt" class="form-input"></div>';
        echo '<div class="form-group"><label>Link do zdjęcia:</label><input type="text" name="zdjecie" class="form-input"></div>';
        echo '<div class="form-group"><input type="submit" name="dodaj_produkt" value="Dodaj" class="form-button"></div>';
        echo '</form>';
        echo '</div>';
    }

    public function UsunProdukt()
    {
        if (isset($_GET['delete_id'])) {
            $id = intval($_GET['delete_id']);
            $query = "DELETE FROM products WHERE id='$id' LIMIT 1";
            if (mysqli_query($this->link, $query)) {
                echo "<p style='color:green;'>Produkt usunięty.</p>";
            } else {
                echo "<p style='color:red;'>Błąd usuwania: " . mysqli_error($this->link) . "</p>";
            }
        }
    }

    public function EdytujProdukt()
    {
        if (isset($_GET['edit_id'])) {
            $id = intval($_GET['edit_id']);

            if (isset($_POST['edytuj_produkt'])) {
                $tytul = mysqli_real_escape_string($this->link, $_POST['tytul']);
                $opis = mysqli_real_escape_string($this->link, $_POST['opis']);
                $data_wygasniecia = $_POST['data_wygasniecia'] ? "'" . mysqli_real_escape_string($this->link, $_POST['data_wygasniecia']) . "'" : "NULL";
                $cena_netto = floatval($_POST['cena_netto']);
                $podatek_vat = floatval($_POST['podatek_vat']);
                $ilosc = intval($_POST['ilosc']);
                $kategoria = intval($_POST['kategoria']);
                $gabaryt = mysqli_real_escape_string($this->link, $_POST['gabaryt']);
                $zdjecie = mysqli_real_escape_string($this->link, $_POST['zdjecie']);

                // Przeliczenie statusu
                $status = ($ilosc > 0) ? 1 : 0;
                if ($_POST['data_wygasniecia'] && strtotime($_POST['data_wygasniecia']) < time()) {
                    $status = 0;
                }

                $query = "UPDATE products SET 
                    tytul='$tytul', 
                    opis='$opis', 
                    data_wygasniecia=$data_wygasniecia, 
                    cena_netto='$cena_netto', 
                    podatek_vat='$podatek_vat', 
                    ilosc_dostepnych_sztuk='$ilosc', 
                    status_dostepnosci='$status', 
                    kategoria='$kategoria', 
                    gabaryt_produktu='$gabaryt', 
                    zdjecie='$zdjecie' 
                    WHERE id='$id' LIMIT 1";

                if (mysqli_query($this->link, $query)) {
                    echo "<p style='color:green;'>Produkt zaktualizowany.</p>";
                } else {
                    echo "<p style='color:red;'>Błąd edycji: " . mysqli_error($this->link) . "</p>";
                }
            }

            $query = "SELECT * FROM products WHERE id='$id' LIMIT 1";
            $result = mysqli_query($this->link, $query);
            $row = mysqli_fetch_assoc($result);

            if ($row) {
                // Formatowanie daty do input type="datetime-local" (YYYY-MM-DDTHH:MM)
                $data_wyg_val = '';
                if ($row['data_wygasniecia']) {
                    $data_wyg_val = date('Y-m-d\TH:i', strtotime($row['data_wygasniecia']));
                }

                echo '<h3 class="heading">Edytuj Produkt</h3>';
                echo '<div class="contact-container" style="max-width: 800px; margin: 0 auto;">';
                echo '<form method="post" action="">';
                echo '<div class="form-group"><label>Tytuł:</label><input type="text" name="tytul" class="form-input" value="' . htmlspecialchars($row['tytul']) . '" required></div>';
                echo '<div class="form-group"><label>Opis:</label><textarea name="opis" class="form-input">' . htmlspecialchars($row['opis']) . '</textarea></div>';
                echo '<div class="form-group"><label>Data wygaśnięcia:</label><input type="datetime-local" name="data_wygasniecia" class="form-input" value="' . $data_wyg_val . '"></div>';
                echo '<div class="form-group"><label>Cena netto:</label><input type="number" step="0.01" name="cena_netto" class="form-input" value="' . $row['cena_netto'] . '" required></div>';
                echo '<div class="form-group"><label>Podatek VAT:</label><input type="number" step="0.01" name="podatek_vat" class="form-input" value="' . $row['podatek_vat'] . '"></div>';
                echo '<div class="form-group"><label>Ilość sztuk:</label><input type="number" name="ilosc" class="form-input" value="' . $row['ilosc_dostepnych_sztuk'] . '" required></div>';
                echo '<div class="form-group"><label>Kategoria (ID):</label><input type="number" name="kategoria" class="form-input" value="' . $row['kategoria'] . '"></div>';
                echo '<div class="form-group"><label>Gabaryt:</label><input type="text" name="gabaryt" class="form-input" value="' . htmlspecialchars($row['gabaryt_produktu']) . '"></div>';
                echo '<div class="form-group"><label>Link do zdjęcia:</label><input type="text" name="zdjecie" class="form-input" value="' . htmlspecialchars($row['zdjecie']) . '"></div>';
                echo '<div class="form-group"><input type="submit" name="edytuj_produkt" value="Zapisz zmiany" class="form-button"></div>';
                echo '</form>';
                echo '</div>';
            }
        }
    }

    public function PokazProdukty()
    {
        echo '<h3>Lista Produktów</h3>';
        $query = "SELECT * FROM products ORDER BY id DESC";
        $result = mysqli_query($this->link, $query);

        echo '<table class="ranking-table">';
        echo '<tr><th>ID</th><th>Tytuł</th><th>Cena Netto</th><th>VAT</th><th>Ilość</th><th>Status</th><th>Opcje</th></tr>';

        while ($row = mysqli_fetch_assoc($result)) {
            $status_text = $row['status_dostepnosci'] == 1 ? 'Dostępny' : 'Niedostępny';
            // Sprawdzenie dodatkowe przy wyświetlaniu (np. czy wygasł, ale w bazie jeszcze status = 1)
            if ($row['status_dostepnosci'] == 1) {
                if ($row['data_wygasniecia'] && strtotime($row['data_wygasniecia']) < time()) {
                    $status_text = 'Wygasł';
                } elseif ($row['ilosc_dostepnych_sztuk'] <= 0) {
                    $status_text = 'Brak w magazynie';
                }
            }

            echo '<tr>';
            echo '<td>' . $row['id'] . '</td>';
            echo '<td>' . htmlspecialchars($row['tytul']) . '</td>';
            echo '<td>' . $row['cena_netto'] . '</td>';
            echo '<td>' . $row['podatek_vat'] . '</td>';
            echo '<td>' . $row['ilosc_dostepnych_sztuk'] . '</td>';
            echo '<td>' . $status_text . '</td>';
            echo '<td>';
            echo '<td>';
            echo '<a href="admin.php?type=products&action=edit&edit_id=' . $row['id'] . '" class="btn btn-primary btn-sm">Edytuj</a> ';
            echo '<a href="admin.php?type=products&action=delete&delete_id=' . $row['id'] . '" class="btn btn-danger btn-sm" onclick="return confirm(\'Czy na pewno usunąć?\')">Usuń</a>';
            echo '</td>';
            echo '</tr>';
        }
        echo '</table>';
    }

    public function ZarzadzajProduktami()
    {
        echo '<h2 class="heading">Zarządzanie Produktami</h2>';

        // Router akcji
        if (isset($_GET['action'])) {
            if ($_GET['action'] == 'delete') {
                $this->UsunProdukt();
            } elseif ($_GET['action'] == 'edit') {
                $this->EdytujProdukt();
            }
        }

        $this->DodajProdukt();
        echo '<hr>';
        $this->PokazProdukty();
    }
}
?>