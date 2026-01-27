<?php


/**
 * Wyświetla szczegóły pojedynczego produktu.
 * Pozwala dodać produkt do koszyka.
 * 
 * @param mysqli $link Uchwyt połączenia z bazą danych
 * @param ShoppingCart $cart Obiekt koszyka (do obsługi akcji)
 * @param int $id ID produktu
 */
function PokazProdukt($link, $cart, $id)
{
    $id = intval($id);
    $query = "SELECT * FROM products WHERE id='$id' LIMIT 1";
    $result = mysqli_query($link, $query);
    $row = mysqli_fetch_assoc($result);

    if (!$row) {
        echo '<h2>Produkt nie istnieje.</h2>';
        echo '<a href="index.php?idp=sklep">Powrót do sklepu</a>';
        return;
    }

    echo '<div style="max-width: 1000px; margin: 0 auto; padding: 20px;">';
    echo '<a href="index.php?idp=sklep" style="color: var(--text-color); text-decoration: underline; margin-bottom: 20px; display: inline-block;">&larr; Powrót do sklepu</a>';

    echo '<div style="display: flex; flex-wrap: wrap; gap: 40px; background-color: rgba(255,255,255,0.05); padding: 30px; border-radius: 12px;">';

    // Lewa kolumna - Zdjęcie
    echo '<div style="flex: 1; min-width: 300px;">';
    if (!empty($row['zdjecie'])) {
        echo '<img src="' . htmlspecialchars($row['zdjecie']) . '" alt="' . htmlspecialchars($row['tytul']) . '" style="width: 100%; height: auto; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.2);">';
    } else {
        echo '<div style="width: 100%; height: 300px; background-color: #333; display: flex; align-items: center; justify-content: center; color: #aaa;">Brak zdjęcia</div>';
    }
    echo '</div>';

    // Prawa kolumna - Dane
    echo '<div style="flex: 1; min-width: 300px;">';
    echo '<h1 style="margin-top: 0; font-size: 2.5em; color: var(--accent-color);">' . htmlspecialchars($row['tytul']) . '</h1>';

    $cena_netto = $row['cena_netto'];
    $vat = $row['podatek_vat'];
    $cena_brutto = $cena_netto * (1 + $vat);

    echo '<p style="font-size: 1.5em; font-weight: bold; margin: 10px 0;">' . number_format($cena_brutto, 2) . ' zł <span style="font-size: 0.6em; font-weight: normal; color: #888;">(brutto)</span></p>';
    echo '<p style="margin: 5px 0; color: #aaa;">Cena netto: ' . number_format($cena_netto, 2) . ' zł</p>';

    // Status dostępności
    $dostepnosc = ($row['status_dostepnosci'] == 1 && $row['ilosc_dostepnych_sztuk'] > 0) ? '<span style="color: #4CAF50;">Dostępny</span>' : '<span style="color: #F44336;">Niedostępny</span>';
    if ($row['data_wygasniecia'] && strtotime($row['data_wygasniecia']) < time())
        $dostepnosc = '<span style="color: #F44336;">Produkt wygasł</span>';

    echo '<p style="margin: 20px 0;">Dostępność: ' . $dostepnosc . '</p>';
    echo '<p>Ilość w magazynie: ' . $row['ilosc_dostepnych_sztuk'] . ' szt.</p>';

    if ($row['gabaryt_produktu']) {
        echo '<p>Gabaryty: ' . htmlspecialchars($row['gabaryt_produktu']) . '</p>';
    }

    echo '<div style="margin-top: 30px; background-color: rgba(255,255,255,0.05); padding: 20px; border-radius: 8px;">';
    echo '<h3 style="margin-top: 0;">Opis produktu</h3>';
    echo '<div style="line-height: 1.6;">' . nl2br(htmlspecialchars($row['opis'])) . '</div>';
    echo '</div>';

    // Formularz dodawania do koszyka
    if ($row['status_dostepnosci'] == 1 && $row['ilosc_dostepnych_sztuk'] > 0 && (!$row['data_wygasniecia'] || strtotime($row['data_wygasniecia']) > time())) {
        echo '<form method="post" action="index.php?idp=koszyk" style="margin-top: 30px;">'; // Przekierowanie do koszyka po dodaniu? Lub zostać tu. Zostańmy tu, ale formularz w shop.php obsługuje koszyk w PokazSklep. Tutaj też trzeba obsłużyć.
        // W index.php idp=produkt można też wywołać $cart->handleCartAction()
        echo '<input type="hidden" name="id" value="' . $row['id'] . '">';
        echo '<input type="submit" name="add_to_cart" value="Dodaj do koszyka" style="padding: 15px 30px; background-color: var(--accent-color); color: white; border: none; border-radius: 4px; font-size: 1.1em; cursor: pointer; transition: background 0.3s;">';
        echo '</form>';
    }

    echo '</div>'; // Koniec prawej kolumny
    echo '</div>'; // Koniec flex
    echo '</div>'; // Koniec wrappera
}

/**
 * Wyświetla główny widok sklepu z listą produktów, filtrowaniem i wyszukiwarką.
 * 
 * @param mysqli $link Uchwyt połączenia z bazą danych
 * @param ShoppingCart $cart Obiekt koszyka
 */
function PokazSklep($link, $cart)
{
    echo '<h2>Sklep Internetowy</h2>';

    // Obsługa akcji koszyka (dodawanie)
    $cart->handleCartAction();

    // Formularz wyszukiwania
    echo '<div style="margin-bottom: 20px; background-color: rgba(255,255,255,0.05); padding: 15px; border-radius: 8px;">';
    echo '<form method="get" action="index.php" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">';
    echo '<input type="hidden" name="idp" value="sklep">';

    $search_val = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';
    $min_val = isset($_GET['price_min']) ? htmlspecialchars($_GET['price_min']) : '';
    $max_val = isset($_GET['price_max']) ? htmlspecialchars($_GET['price_max']) : '';

    echo '<input type="text" name="search" value="' . $search_val . '" placeholder="Szukaj produktu..." style="padding: 10px; border-radius: 4px; border: 1px solid #ccc; flex: 2; min-width: 200px;">';
    echo '<input type="number" name="price_min" value="' . $min_val . '" placeholder="Cena od" min="0" step="10" style="padding: 10px; border-radius: 4px; border: 1px solid #ccc; width: 100px;">';
    echo '<input type="number" name="price_max" value="' . $max_val . '" placeholder="Cena do" min="0" step="10" style="padding: 10px; border-radius: 4px; border: 1px solid #ccc; width: 100px;">';
    echo '<button type="submit" style="padding: 10px 20px; background-color: var(--accent-color); color: white; border: none; border-radius: 4px; cursor: pointer;">Szukaj</button>';
    echo '<a href="index.php?idp=sklep" style="padding: 10px; color: var(--text-color); text-decoration: underline;">Wyczyść</a>';
    echo '</form>';
    echo '</div>';

    // Budowanie zapytania
    $conditions = ["status_dostepnosci = 1"];

    if (!empty($_GET['search'])) {
        $term = mysqli_real_escape_string($link, $_GET['search']);
        $conditions[] = "(tytul LIKE '%$term%' OR opis LIKE '%$term%')";
    }

    if (!empty($_GET['price_min'])) {
        $min = floatval($_GET['price_min']);
        // Cena brutto = cena_netto * (1 + vat)
        $conditions[] = "(cena_netto * (1 + podatek_vat)) >= $min";
    }

    if (!empty($_GET['price_max'])) {
        $max = floatval($_GET['price_max']);
        $conditions[] = "(cena_netto * (1 + podatek_vat)) <= $max";
    }

    $whereSQL = implode(' AND ', $conditions);
    $query = "SELECT * FROM products WHERE $whereSQL ORDER BY id DESC";
    $result = mysqli_query($link, $query);

    if (!$result) {
        echo '<p>Błąd zapytania: ' . mysqli_error($link) . '</p>';
        return;
    }

    if (mysqli_num_rows($result) == 0) {
        echo '<p style="text-align: center; padding: 20px;">Nie znaleziono produktów spełniających kryteria.</p>';
        return;
    }

    // Grid produktów
    echo '<div style="display: flex; flex-wrap: wrap; gap: 20px;">';

    while ($row = mysqli_fetch_assoc($result)) {
        // Dodatkowe sprawdzenie daty wygaśnięcia i ilości
        if ($row['data_wygasniecia'] && strtotime($row['data_wygasniecia']) < time())
            continue;
        if ($row['ilosc_dostepnych_sztuk'] <= 0)
            continue;

        $cena_netto = $row['cena_netto'];
        $vat = $row['podatek_vat'];
        $cena_brutto = $cena_netto * (1 + $vat);

        echo '<div style="border: 1px solid #ccc; padding: 15px; width: 250px; background-color: rgba(255,255,255,0.1); border-radius: 8px; display: flex; flex-direction: column;">';
        if (!empty($row['zdjecie'])) {
            echo '<div style="height: 150px; overflow: hidden; border-radius: 4px; margin-bottom: 10px;">';
            echo '<img src="' . htmlspecialchars($row['zdjecie']) . '" alt="' . htmlspecialchars($row['tytul']) . '" style="width: 100%; height: 100%; object-fit: cover;">';
            echo '</div>';
        }
        echo '<h3 style="margin: 0 0 10px 0; font-size: 1.2em;"><a href="index.php?idp=produkt&id=' . $row['id'] . '" style="color: inherit; text-decoration: none; transition: color 0.3s;" onmouseover="this.style.color=\'var(--accent-color)\'" onmouseout="this.style.color=\'inherit\'">' . htmlspecialchars($row['tytul']) . '</a></h3>';

        echo '<div style="margin-top: auto;">'; // Push to bottom
        echo '<p style="font-weight: bold; font-size: 1.1em; margin: 5px 0;">' . number_format($cena_brutto, 2) . ' zł</p>';
        echo '<p style="font-size: 0.9em; color: var(--text-gray); margin-bottom: 10px;">Dostępna ilość: ' . $row['ilosc_dostepnych_sztuk'] . '</p>';

        echo '<div style="display: flex; gap: 10px;">';

        // Formularz dodawania do koszyka (mały, button tylko ikonka lub tekst)
        echo '<form method="post" action="" style="flex: 1;">';
        echo '<input type="hidden" name="id" value="' . $row['id'] . '">';
        echo '<input type="submit" name="add_to_cart" value="Do koszyka" style="width: 100%; padding: 10px; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.9em;">';
        echo '</form>';

        echo '</div>'; // End button container
        echo '</div>'; // End bottom push
        echo '</div>'; // End card
    }

    echo '</div>';
}
?>