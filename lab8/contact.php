<?php

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

function WyslijMailKontakt($odbiorca)
{
    if (empty($_POST['temat']) || empty($_POST['tresc']) || empty($_POST['email'])) {
        echo '<div class="contact-container notification error">
            <h3 class="heading">Błąd!</h3>
            <p>Nie wypełniłeś wszystkich pól.</p>
        </div>';
        echo PokazKontakt(); //ponowane wywolanie formularza
    } else {
        $mail['subject'] = $_POST['temat'];
        $mail['body'] = $_POST['tresc'];
        $mail['sender'] = $_POST['email'];
        $mail['reciptient'] = $odbiorca; //czyli my jestesmy odbiorca, jezeli tworzymy formularz kontaktowy

        $header = "From: Formularz kontaktowy <" . $mail['sender'] . ">\n";
        $header .= "MIME-Version: 1.0\nContent-Type: text/plain; charset=utf-8\nContent-Transfer-Encoding: 8bit\n";
        $header .= "X-Sender: <" . $mail['sender'] . ">\n";
        $header .= "X-Mailer: PRapwww mail 1.2\n";
        $header .= "X-Priority: 3\n";
        $header .= "Return-Path: <" . $mail['sender'] . ">\n";

        mail($mail['reciptient'], $mail['subject'], $mail['body'], $header);

        echo '<div class="contact-container notification success">
            <h3 class="heading">Sukces!</h3>
            <p>Wiadomość została wysłana pomyślnie.</p>
        </div>';
    }
}

function PrzypomnijHaslo($odbiorca)
{
    global $pass;

    $mail['subject'] = "Przypomnienie hasla";
    $mail['body'] = "Twoje haslo to: " . $pass;
    $mail['sender'] = "admin@system.pl";
    $mail['reciptient'] = $odbiorca;

    $header = "From: System <" . $mail['sender'] . ">\n";
    $header .= "MIME-Version: 1.0\nContent-Type: text/plain; charset=utf-8\nContent-Transfer-Encoding: 8bit\n";
    $header .= "X-Sender: <" . $mail['sender'] . ">\n";
    $header .= "X-Mailer: PRapwww mail 1.2\n";
    $header .= "X-Priority: 3\n";
    $header .= "Return-Path: <" . $mail['sender'] . ">\n";

    mail($mail['reciptient'], $mail['subject'], $mail['body'], $header);

    echo '<div class="contact-container notification success">
        <h3 class="heading">Hasło wysłane!</h3>
        <p>Twoje hasło zostało wysłane na podany adres email.</p>
    </div>';
}

?>