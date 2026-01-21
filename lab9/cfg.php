<?php
/**
 * Plik konfiguracyjny bazy danych
 * Zawiera dane logowania do bazy MySQL oraz dane administratora
 */

// Konfiguracja połączenia z bazą danych
$dbhost = 'localhost'; // Adres serwera bazy danych
$dbuser = 'root';      // Nazwa użytkownika bazy danych
$dbpass = '';          // Hasło użytkownika bazy danych
$baza = 'moja_strona'; // Nazwa bazy danych

// Dane logowania do panelu administratora
$login = 'admin';      // Login administratora
$pass = 'pass';        // Hasło administratora

// Nawiązanie połączenia z bazą danych
$link = mysqli_connect($dbhost, $dbuser, $dbpass, $baza);

// Sprawdzenie czy połączenie zostało ustanowione
if (!$link) {
    echo '<b>przerwane połączenie </b>';
}
?>