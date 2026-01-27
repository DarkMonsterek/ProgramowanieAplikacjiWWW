<?php
/**
 * Plik odpowiedzialny za obsługę formularza kontaktowego
 * oraz wysyłanie wiadomości email.
 */

/**
 * Funkcja wyświetlająca formularz kontaktowy
 * 
 * @return string Kod HTML formularza
 */
/**
 * Wyświetla formularz kontaktowy.
 * Pozwala użytkownikowi na wysłanie wiadomości, przypomnienie hasła, etc.
 */
function PokazKontakt()
{
    $html = '
    <div class="contact-container">
        <h2 class="heading">Formularz Kontaktowy</h2>
        <form action="" method="post" class="contact-form">
            <div class="form-group">
                <label for="temat">Temat:</label>
                <input type="text" id="temat" name="temat" class="form-input" required>
            </div>
            <div class="form-group">
                <label for="email">Twój E-mail:</label>
                <input type="email" id="email" name="email" class="form-input" required>
            </div>
            <div class="form-group">
                <label for="tresc">Treść:</label>
                <textarea id="tresc" name="tresc" class="form-input" rows="5" required></textarea>
            </div>
            <div class="form-group">
                <input type="submit" name="wyslij_kontakt" value="Wyślij" class="form-button">
            </div>
        </form>
    </div>
    ';
    return $html;
}

/**
 * Funkcja wysyłająca wiadomość email z formularza kontaktowego
 * 
 * @param string $odbiorca Adres email odbiorcy wiadomości
 */

/**
 * Wysyła wiadomość email przy użyciu PHPMailer (SMTP).
 * Funkcja ogólnego przeznaczenia.
 *
 * @param string $to Adres odbiorcy
 * @param string $subject Temat wiadomości
 * @param string $body Treść wiadomości (HTML)
 * @param string $fromName Nazwa nadawcy (opcjonalnie)
 * @param string $fromEmail Email nadawcy (opcjonalnie)
 * @return bool|string True jeśli wysłano, string z błędem w przeciwnym razie
 */
function SendSmtpMail($to, $subject, $body, $fromName = "Moja Strona", $fromEmail = "kontakt@mojastrona.pl")
{
    global $smtp_host, $smtp_port, $smtp_user, $smtp_pass;

    $connect = fsockopen("ssl://" . $smtp_host, $smtp_port, $errno, $errstr, 15);

    if (!$connect) {
        return "Błąd połączenia: $errno - $errstr";
    }

    // Funkcja pomocnicza do czytania odpowiedzi serwera
    if (!function_exists('get_server_response')) {
        function get_server_response($conn)
        {
            $data = "";
            while ($str = fgets($conn, 515)) {
                $data .= $str;
                if (substr($str, 3, 1) == " ") {
                    break;
                }
            }
            return $data;
        }
    }

    get_server_response($connect);

    // EHLO
    fputs($connect, "EHLO localhost\r\n");
    get_server_response($connect);

    // AUTH LOGIN
    fputs($connect, "AUTH LOGIN\r\n");
    get_server_response($connect);

    // Wysyłanie loginu (base64)
    fputs($connect, base64_encode($smtp_user) . "\r\n");
    get_server_response($connect);

    // Wysyłanie hasła (base64)
    fputs($connect, base64_encode($smtp_pass) . "\r\n");
    $auth_response = get_server_response($connect);

    if (strpos($auth_response, '235') === false) {
        return "Błąd autoryzacji SMTP: " . $auth_response;
    }

    // MAIL FROM
    fputs($connect, "MAIL FROM: <$smtp_user>\r\n");
    get_server_response($connect);

    // RCPT TO
    fputs($connect, "RCPT TO: <$to>\r\n");
    get_server_response($connect);

    // DATA
    fputs($connect, "DATA\r\n");
    get_server_response($connect);

    // Nagłówki i treść
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=utf-8\r\n";
    $headers .= "From: $fromName <$smtp_user>\r\n";
    $headers .= "Reply-To: $fromName <$fromEmail>\r\n";
    $headers .= "To: <$to>\r\n";
    $headers .= "Subject: $subject\r\n";

    fputs($connect, "$headers\r\n\r\n$body\r\n.\r\n");
    $send_response = get_server_response($connect);

    // QUIT
    fputs($connect, "QUIT\r\n");
    fclose($connect);

    if (strpos($send_response, '250') !== false) {
        return true;
    } else {
        return "Błąd wysyłania: " . $send_response;
    }
}

/**
 * Obsługuje wysyłanie wiadomości z formularza kontaktowego.
 *
 * @param string $odbiorca Adres email, na który ma zostać wysłana wiadomość (np. admin).
 */
function WyslijMailKontakt($odbiorca)
{
    // Sprawdzenie czy wszystkie pola zostały wypełnione
    if (empty($_POST['temat']) || empty($_POST['tresc']) || empty($_POST['email'])) {
        echo '<div class="contact-container notification error">
            <h3 class="heading">Błąd!</h3>
            <p>Nie wypełniłeś wszystkich pól.</p>
        </div>';
        echo PokazKontakt();
    } else {
        $temat = htmlspecialchars(trim($_POST['temat']));
        $tresc = nl2br(htmlspecialchars(trim($_POST['tresc'])));
        $email_nadawcy = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

        // Walidacja emaila
        if (!filter_var($email_nadawcy, FILTER_VALIDATE_EMAIL)) {
            echo '<div class="contact-container notification error"><p>Nieprawidłowy adres email.</p></div>';
            echo PokazKontakt();
            return;
        }

        // Przygotowanie wiadomości
        $message_body = "<h3>Nowa wiadomość ze strony</h3>";
        $message_body .= "<p><strong>Od:</strong> $email_nadawcy</p>";
        $message_body .= "<p><strong>Temat:</strong> $temat</p>";
        $message_body .= "<hr>";
        $message_body .= "<p><strong>Treść:</strong><br>$tresc</p>";

        // Wysyłka przez SMTP
        $result = SendSmtpMail($odbiorca, "[Formularz] $temat", $message_body, "Kontakt WWW", $email_nadawcy);

        if ($result === true) {
            echo '<div class="contact-container notification success">
                <h3 class="heading">Sukces!</h3>
                <p>Wiadomość została wysłana pomyślnie.</p>
            </div>';
        } else {
            echo '<div class="contact-container notification error">
                <h3 class="heading">Błąd!</h3>
                <p>Wystąpił problem z wysłaniem wiadomości.<br><small>' . htmlspecialchars($result) . '</small></p>
            </div>';
        }
    }
}

/**
 * Obsługuje funkcjonalność przypominania hasła (demo).
 * Wysłanie hasła na podany email.
 *
 * @param string $odbiorca Adres email użytkownika.
 */
function PrzypomnijHaslo($odbiorca)
{
    global $pass, $admin_email;
    $message_body = "<h3>Przypomnienie hasła</h3>";
    $message_body .= "<p>Twoje hasło do panelu administratora to: <strong>$pass</strong></p>";

    $result = SendSmtpMail($odbiorca, "Odzyskiwanie hasła", $message_body, "System CMS", $admin_email);

    if ($result === true) {
        echo '<div class="contact-container notification success"><p>Hasło zostało wysłane na podany adres email.</p></div>';
    } else {
        echo '<div class="contact-container notification error"><p>Błąd wysyłania hasła: ' . htmlspecialchars($result) . '</p></div>';
    }
}
?>