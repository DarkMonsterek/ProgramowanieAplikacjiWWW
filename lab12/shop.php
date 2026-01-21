<?php

function PokazSklep($link, $cart)
{
    echo '<h2>Sklep Internetowy</h2>';

    // Obsługa akcji koszyka (dodawanie)
    $cart->handleCartAction();

    $query = "SELECT * FROM products WHERE status_dostepnosci = 1 ORDER BY id DESC";
    $result = mysqli_query($link, $query);

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

        echo '<div style="border: 1px solid #ccc; padding: 15px; width: 250px; background-color: rgba(255,255,255,0.1); border-radius: 8px;">';
        if (!empty($row['zdjecie'])) {
            echo '<img src="' . htmlspecialchars($row['zdjecie']) . '" alt="' . htmlspecialchars($row['tytul']) . '" style="width: 100%; height: 150px; object-fit: cover; border-radius: 4px;">';
        }
        echo '<h3>' . htmlspecialchars($row['tytul']) . '</h3>';
        // echo '<p>' . htmlspecialchars($row['opis']) . '</p>';
        echo '<p>Cena netto: ' . number_format($cena_netto, 2) . ' zł</p>';
        echo '<p style="font-weight: bold; font-size: 1.1em;">Cena: ' . number_format($cena_brutto, 2) . ' zł</p>';
        echo '<p style="font-size: 0.9em; color: var(--text-gray);">Dostępna ilość: ' . $row['ilosc_dostepnych_sztuk'] . '</p>';

        echo '<form method="post" action="">';
        echo '<input type="hidden" name="id" value="' . $row['id'] . '">';
        echo '<input type="submit" name="add_to_cart" value="Dodaj do koszyka" style="width: 100%; padding: 10px; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer;">';
        echo '</form>';

        echo '</div>';
    }

    echo '</div>';
}
?>