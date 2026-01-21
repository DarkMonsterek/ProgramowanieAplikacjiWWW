<?php

class Produkty
{
    private $link;

    public function __construct($db_link)
    {
        $this->link = $db_link;
        $this->createTableIfNotExists();
    }

    private function createTableIfNotExists()
    {
        $query = "CREATE TABLE IF NOT EXISTS products (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tytul VARCHAR(255) NOT NULL,
            opis TEXT,
            data_utworzenia DATETIME DEFAULT CURRENT_TIMESTAMP,
            data_modyfikacji DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            data_wygasniecia DATETIME,
            cena_netto DECIMAL(10, 2) NOT NULL,
            podatek_vat DECIMAL(5, 2) DEFAULT 0.23,
            ilosc_dostepnych_sztuk INT DEFAULT 0,
            status_dostepnosci INT DEFAULT 1,
            kategoria INT,
            gabaryt_produktu VARCHAR(50),
            zdjecie VARCHAR(255)
        )";
        mysqli_query($this->link, $query);
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
            
            // Status: 1 - Dostępny, 0 - Niedostępny. Możemy też wyliczać dynamicznie.
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

        echo '<h3>Dodaj Produkt</h3>';
        echo '<form method="post" action="">';
        echo '<table>';
        echo '<tr><td>Tytuł:</td><td><input type="text" name="tytul" required></td></tr>';
        echo '<tr><td>Opis:</td><td><textarea name="opis"></textarea></td></tr>';
        echo '<tr><td>Data wygaśnięcia:</td><td><input type="datetime-local" name="data_wygasniecia"></td></tr>';
        echo '<tr><td>Cena netto:</td><td><input type="number" step="0.01" name="cena_netto" required></td></tr>';
        echo '<tr><td>Podatek VAT (np. 0.23):</td><td><input type="number" step="0.01" name="podatek_vat" value="0.23"></td></tr>';
        echo '<tr><td>Ilość sztuk:</td><td><input type="number" name="ilosc" required></td></tr>';
        echo '<tr><td>Kategoria (ID):</td><td><input type="number" name="kategoria"></td></tr>';
        echo '<tr><td>Gabaryt:</td><td><input type="text" name="gabaryt"></td></tr>';
        echo '<tr><td>Link do zdjęcia:</td><td><input type="text" name="zdjecie"></td></tr>';
        echo '<tr><td colspan="2"><input type="submit" name="dodaj_produkt" value="Dodaj"></td></tr>';
        echo '</table>';
        echo '</form>';
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
                    // Refresh data for display
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

                echo '<h3>Edytuj Produkt</h3>';
                echo '<form method="post" action="">';
                echo '<table>';
                echo '<tr><td>Tytuł:</td><td><input type="text" name="tytul" value="' . htmlspecialchars($row['tytul']) . '" required></td></tr>';
                echo '<tr><td>Opis:</td><td><textarea name="opis">' . htmlspecialchars($row['opis']) . '</textarea></td></tr>';
                echo '<tr><td>Data wygaśnięcia:</td><td><input type="datetime-local" name="data_wygasniecia" value="' . $data_wyg_val . '"></td></tr>';
                echo '<tr><td>Cena netto:</td><td><input type="number" step="0.01" name="cena_netto" value="' . $row['cena_netto'] . '" required></td></tr>';
                echo '<tr><td>Podatek VAT:</td><td><input type="number" step="0.01" name="podatek_vat" value="' . $row['podatek_vat'] . '"></td></tr>';
                echo '<tr><td>Ilość sztuk:</td><td><input type="number" name="ilosc" value="' . $row['ilosc_dostepnych_sztuk'] . '" required></td></tr>';
                echo '<tr><td>Kategoria (ID):</td><td><input type="number" name="kategoria" value="' . $row['kategoria'] . '"></td></tr>';
                echo '<tr><td>Gabaryt:</td><td><input type="text" name="gabaryt" value="' . htmlspecialchars($row['gabaryt_produktu']) . '"></td></tr>';
                echo '<tr><td>Link do zdjęcia:</td><td><input type="text" name="zdjecie" value="' . htmlspecialchars($row['zdjecie']) . '"></td></tr>';
                echo '<tr><td colspan="2"><input type="submit" name="edytuj_produkt" value="Zapisz zmiany"></td></tr>';
                echo '</table>';
                echo '</form>';
            }
        }
    }

    public function PokazProdukty()
    {
        echo '<h3>Lista Produktów</h3>';
        $query = "SELECT * FROM products ORDER BY id DESC";
        $result = mysqli_query($this->link, $query);

        echo '<table border="1" cellpadding="5" cellspacing="0">';
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
            echo '<a href="admin.php?type=products&action=edit&edit_id=' . $row['id'] . '">[Edytuj]</a> ';
            echo '<a href="admin.php?type=products&action=delete&delete_id=' . $row['id'] . '" onclick="return confirm(\'Czy na pewno usunąć?\')">[Usuń]</a>';
            echo '</td>';
            echo '</tr>';
        }
        echo '</table>';
    }

    public function ZarzadzajProduktami()
    {
        echo '<a href="admin.php">Powrót do panelu głównego</a><br>';
        echo '<h2>Zarządzanie Produktami</h2>';

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
