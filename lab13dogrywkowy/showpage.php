<?php
/**
 * Plik odpowiedzialny za wyświetlanie treści podstron
 */

include_once("cfg.php");

/**
 * Funkcja pobierająca i wyświetlająca treść podstrony o podanym ID
 * 
 * @param int|string $id ID strony do wyświetlenia
 * @return string Treść strony lub informacja o błędzie
 */
function PokazPodstrone($id)
{
   global $link;

   // Zabezpieczenie przed atakami SQL Injection
   // Czyszczenie zmiennej $id z niebezpiecznych znaków
   $id_clear = mysqli_real_escape_string($link, htmlspecialchars($id));

   // Zapytanie SQL z limitem 1 dla optymalizacji i bezpieczeństwa
   $qry = "SELECT * FROM page_list WHERE id='$id_clear' LIMIT 1";

   // Wykonanie zapytania
   $result = mysqli_query($link, $qry) or die(mysqli_error($link));
   $row = mysqli_fetch_array($result);

   // Sprawdzenie czy strona została znaleziona
   if (empty($row['id'])) {
      $web = '[nie_znaleziono_strony]';
   } else {
      $web = $row['page_content'];
   }

   return $web;
}
?>