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
$admin_email = 'przem204@gmail.com'; // Email administratora do formularza kontaktowego

// Konfiguracja SMTP (Gmail)
$smtp_host = 'smtp.gmail.com';
$smtp_port = 465;
$smtp_user = 'test.imap.paww@gmail.com';
$smtp_pass = 'xaka ocbg nddd udgv';

// Konfiguracja Google reCAPTCHA v2
$recaptcha_site_key = '6LckHVUsAAAAAPvMMWJUf9CJwto1b9NCGaTq60xE';
$recaptcha_secret_key = '6LckHVUsAAAAAFPeAWD-dmaFGJ7uNWu7xUEB9qN4';

// Nawiązanie połączenia z bazą danych
$link = mysqli_connect($dbhost, $dbuser, $dbpass, $baza);

// Sprawdzenie czy połączenie zostało ustanowione
if (!$link) {
    echo '<b>przerwane połączenie </b>';
}
?>