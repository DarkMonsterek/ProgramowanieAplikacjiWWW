<?php

class Kategorie
{
    private $link;

    public function __construct($db_link)
    {
        $this->link = $db_link;
    }

    public function DodajKategorie()
    {
        if (isset($_POST['dodaj_kategorie'])) {
            $matka = intval($_POST['matka']);
            $nazwa = mysqli_real_escape_string($this->link, $_POST['nazwa']);

            $query = "INSERT INTO categories (matka, nazwa) VALUES ('$matka', '$nazwa')";
            if (mysqli_query($this->link, $query)) {
                echo "<p style='color:green;'>Kategoria została dodana.</p>";
            } else {
                echo "<p style='color:red;'>Błąd: " . mysqli_error($this->link) . "</p>";
            }
        }

        echo '<h3 class="heading">Dodaj Kategorię</h3>';
        echo '<div class="contact-container" style="max-width: 600px; margin: 0 auto;">';
        echo '<form method="post" action="">';
        echo '<div class="form-group"><label>Nazwa:</label><input type="text" name="nazwa" class="form-input" required></div>';
        echo '<div class="form-group"><label>Matka (ID):</label><input type="number" name="matka" value="0" class="form-input"></div>';
        echo '<div class="form-group"><input type="submit" name="dodaj_kategorie" value="Dodaj" class="form-button"></div>';
        echo '</form>';
        echo '</div>';
    }

    public function UsunKategorie()
    {
        if (isset($_GET['delete_id'])) {
            $id = intval($_GET['delete_id']);
            $query = "DELETE FROM categories WHERE id='$id' LIMIT 1";
            if (mysqli_query($this->link, $query)) {
                echo "<p style='color:green;'>Kategoria usunięta.</p>";
            } else {
                echo "<p style='color:red;'>Błąd usuwania: " . mysqli_error($this->link) . "</p>";
            }
        }
    }

    public function EdytujKategorie()
    {
        if (isset($_GET['edit_id'])) {
            $id = intval($_GET['edit_id']);

            if (isset($_POST['edytuj_kategorie'])) {
                $matka = intval($_POST['matka']);
                $nazwa = mysqli_real_escape_string($this->link, $_POST['nazwa']);

                $query = "UPDATE categories SET nazwa='$nazwa', matka='$matka' WHERE id='$id' LIMIT 1";
                if (mysqli_query($this->link, $query)) {
                    echo "<p style='color:green;'>Kategoria zaktualizowana.</p>";
                } else {
                    echo "<p style='color:red;'>Błąd edycji: " . mysqli_error($this->link) . "</p>";
                }
            }

            $query = "SELECT * FROM categories WHERE id='$id' LIMIT 1";
            $result = mysqli_query($this->link, $query);
            $row = mysqli_fetch_assoc($result);

            if ($row) {
                echo '<h3 class="heading">Edytuj Kategorię</h3>';
                echo '<div class="contact-container" style="max-width: 600px; margin: 0 auto;">';
                echo '<form method="post" action="">';
                echo '<div class="form-group"><label>Nazwa:</label><input type="text" name="nazwa" value="' . htmlspecialchars($row['nazwa']) . '" class="form-input" required></div>';
                echo '<div class="form-group"><label>Matka (ID):</label><input type="number" name="matka" value="' . $row['matka'] . '" class="form-input"></div>';
                echo '<div class="form-group"><input type="submit" name="edytuj_kategorie" value="Zapisz" class="form-button"></div>';
                echo '</form>';
                echo '</div>';
            }
        }
    }

    public function PokazKategorie()
    {
        echo '<h3>Drzewo Kategorii</h3>';

        // Pobierz kategorie główne (matka = 0)
        $query = "SELECT * FROM categories WHERE matka = 0";
        $result = mysqli_query($this->link, $query);

        if (mysqli_num_rows($result) > 0) {
            echo '<ul style="list-style: none; padding-left: 20px;">';
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<li style="margin-bottom: 10px; padding: 10px; background: rgba(255,255,255,0.05); border-radius: 4px;">';
                echo '<strong>' . htmlspecialchars($row['nazwa']) . '</strong> (ID: ' . $row['id'] . ') ';
                echo '<a href="admin.php?type=categories&action=edit&edit_id=' . $row['id'] . '" class="btn btn-primary btn-sm">Edytuj</a> ';
                echo '<a href="admin.php?type=categories&action=delete&delete_id=' . $row['id'] . '" class="btn btn-danger btn-sm" onclick="return confirm(\'Czy na pewno usunąć?\')">Usuń</a>';

                // Pętla zagnieżdżona dla podkategorii
                $this->PokazPodkategorie($row['id']);

                echo '</li>';
            }
            echo '</ul>';
        } else {
            echo "Brak kategorii.";
        }
    }

    private function PokazPodkategorie($parentId)
    {
        $query = "SELECT * FROM categories WHERE matka = '$parentId'";
        $result = mysqli_query($this->link, $query);

        if (mysqli_num_rows($result) > 0) {
            echo '<ul style="list-style: none; padding-left: 20px; border-left: 1px solid rgba(255,255,255,0.1); margin-top: 10px;">';
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<li style="margin-top: 5px; color: var(--text-gray);">';
                echo htmlspecialchars($row['nazwa']) . ' (ID: ' . $row['id'] . ') ';
                echo '<a href="admin.php?type=categories&action=edit&edit_id=' . $row['id'] . '" class="btn btn-primary btn-sm" style="font-size: 0.8em;">Edytuj</a> ';
                echo '<a href="admin.php?type=categories&action=delete&delete_id=' . $row['id'] . '" class="btn btn-danger btn-sm" style="font-size: 0.8em;" onclick="return confirm(\'Czy na pewno usunąć?\')">Usuń</a>';
                echo '</li>';
            }
            echo '</ul>';
        }
    }

    public function ZarzadzajKategoriami()
    {
        echo '<h2 class="heading">Zarządzanie Kategoriami</h2>';

        // Router akcji
        if (isset($_GET['action'])) {
            if ($_GET['action'] == 'delete') {
                $this->UsunKategorie();
            } elseif ($_GET['action'] == 'edit') {
                $this->EdytujKategorie();
            }
        }

        $this->DodajKategorie();
        echo '<hr>';
        $this->PokazKategorie();
    }
}
?>