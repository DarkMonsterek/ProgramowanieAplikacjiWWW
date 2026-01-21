<?php

class Kategorie
{
    private $link;

    public function __construct($db_link)
    {
        $this->link = $db_link;
        $this->createTableIfNotExists();
    }

    private function createTableIfNotExists()
    {
        $query = "CREATE TABLE IF NOT EXISTS categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            matka INT DEFAULT 0,
            nazwa VARCHAR(255) NOT NULL
        )";
        mysqli_query($this->link, $query);
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
            // header("Location: admin.php?action=categories"); // Opcjonalne przekierowanie
        }

        echo '<h3>Dodaj Kategorię</h3>';
        echo '<form method="post" action="">';
        echo 'Nazwa: <input type="text" name="nazwa" required><br>';
        echo 'Matka (ID): <input type="number" name="matka" value="0"><br>';
        echo '<input type="submit" name="dodaj_kategorie" value="Dodaj">';
        echo '</form>';
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
                echo '<h3>Edytuj Kategorię</h3>';
                echo '<form method="post" action="">';
                echo 'Nazwa: <input type="text" name="nazwa" value="' . htmlspecialchars($row['nazwa']) . '" required><br>';
                echo 'Matka (ID): <input type="number" name="matka" value="' . $row['matka'] . '"><br>';
                echo '<input type="submit" name="edytuj_kategorie" value="Zapisz">';
                echo '</form>';
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
            echo '<ul>';
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<li>' . htmlspecialchars($row['nazwa']) . ' (ID: ' . $row['id'] . ') ';
                echo '<a href="admin.php?type=categories&action=edit&edit_id=' . $row['id'] . '">[Edytuj]</a> ';
                echo '<a href="admin.php?type=categories&action=delete&delete_id=' . $row['id'] . '" onclick="return confirm(\'Czy na pewno usunąć?\')">[Usuń]</a>';

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
            echo '<ul>';
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<li>' . htmlspecialchars($row['nazwa']) . ' (ID: ' . $row['id'] . ') ';
                echo '<a href="admin.php?type=categories&action=edit&edit_id=' . $row['id'] . '">[Edytuj]</a> ';
                echo '<a href="admin.php?type=categories&action=delete&delete_id=' . $row['id'] . '" onclick="return confirm(\'Czy na pewno usunąć?\')">[Usuń]</a>';
                echo '</li>';
            }
            echo '</ul>';
        }
    }

    public function ZarzadzajKategoriami()
    {
        echo '<a href="admin.php">Powrót do panelu głównego</a><br>';
        echo '<h2>Zarządzanie Kategoriami</h2>';

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