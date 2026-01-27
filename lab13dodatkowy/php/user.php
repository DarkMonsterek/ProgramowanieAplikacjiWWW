<?php

/**
 * Klasa UserManager
 * 
 * Odpowiada za zarządzanie użytkownikami: rejestracja, logowanie, wylogowanie,
 * a także wyświetlanie formularzy i panelu użytkownika.
 * Współpracuje z klasą ShoppingCart w celu synchronizacji koszyka po zalogowaniu.
 */
class UserManager
{
    /** @var mysqli Uchwyt połączenia z bazą danych */
    private $link;
    /** @var ShoppingCart|null Obiekt koszyka do synchronizacji */
    private $cart;

    /**
     * Konstruktor klasy
     * 
     * @param mysqli $db_link Połączenie z bazą
     * @param ShoppingCart|null $cart Obiekt koszyka (opcjonalny)
     */
    public function __construct($db_link, $cart = null)
    {
        $this->link = $db_link;
        $this->cart = $cart;
    }

    /**
     * Weryfikuje token reCAPTCHA po stronie serwera.
     * 
     * @param string $response Token odpowiedzi z widgetu
     * @return bool Wynik weryfikacji (true/false)
     */
    private function verifyRecaptcha($response)
    {
        global $recaptcha_secret_key;
        if (empty($response))
            return false;

        $verifyUrl = "https://www.google.com/recaptcha/api/siteverify?secret={$recaptcha_secret_key}&response={$response}";
        $json = file_get_contents($verifyUrl);
        $data = json_decode($json);
        return $data->success;
    }

    /**
     * Obsługuje proces rejestracji nowego użytkownika.
     * Sprawdza poprawność danych (email, hasła, unikalność) i weryfikuje recaptchę.
     */
    public function RejestrujUzytkownika()
    {
        if (isset($_POST['register_submit'])) {
            // Verify reCAPTCHA
            if (!$this->verifyRecaptcha($_POST['g-recaptcha-response'])) {
                $this->pokazFormularzRejestracji("Weryfikacja reCAPTCHA nie powiodła się.");
                return;
            }

            $login = mysqli_real_escape_string($this->link, $_POST['reg_login']);
            $pass = $_POST['reg_pass'];
            $pass_repeat = $_POST['reg_pass2'];

            if (!filter_var($login, FILTER_VALIDATE_EMAIL)) {
                $this->pokazFormularzRejestracji("Login musi być poprawnym adresem e-mail.");
                return;
            }

            if ($pass !== $pass_repeat) {
                $this->pokazFormularzRejestracji("Hasła nie są identyczne.");
                return;
            }

            // Sprawdzenie czy login zajęty
            $check = mysqli_query($this->link, "SELECT id FROM users WHERE login='$login' LIMIT 1");
            if (mysqli_num_rows($check) > 0) {
                $this->pokazFormularzRejestracji("Podany adres e-mail jest już zarejestrowany.");
                return;
            }

            $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);

            $query = "INSERT INTO users (login, pass, status) VALUES ('$login', '$hashed_pass', 1)";
            if (mysqli_query($this->link, $query)) {
                // Auto-login after registration
                $_SESSION['user_id'] = mysqli_insert_id($this->link);
                $_SESSION['user_login'] = $login;

                // Sync cart if available
                if ($this->cart) {
                    $this->cart->syncWithDb($_SESSION['user_id']);
                }

                echo '<div class="notification success"><p>Konto utworzone. Logowanie...</p></div>';
                header("Location: index.php?idp=konto");
                echo '<script>window.location.href="index.php?idp=konto";</script>';
            } else {
                $this->pokazFormularzRejestracji("Błąd bazy danych: " . mysqli_error($this->link));
            }
        } else {
            $this->pokazFormularzRejestracji();
        }
    }

    /**
     * Wyświetla formularz rejestracji.
     * 
     * @param string $error Komunikat błędu do wyświetlenia (opcjonalny)
     */
    public function pokazFormularzRejestracji($error = '')
    {
        global $recaptcha_site_key;
        echo '
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
        <div class="contact-container" style="max-width: 400px; margin: 40px auto;">
            <h2 class="heading">Rejestracja</h2>';

        if ($error) {
            echo '<div class="notification error" style="margin-bottom: 20px;"><p style="color: #ff9999;">' . htmlspecialchars($error) . '</p></div>';
        }

        echo '
            <form method="post" action="index.php?idp=konto&action=register">
                <div class="form-group">
                    <label>E-mail (Login):</label>
                    <input type="email" name="reg_login" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>Hasło:</label>
                    <input type="password" name="reg_pass" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>Powtórz Hasło:</label>
                    <input type="password" name="reg_pass2" class="form-input" required>
                </div>
                <div class="form-group" style="margin: 15px 0;">
                    <div class="g-recaptcha" data-sitekey="' . $recaptcha_site_key . '"></div>
                </div>
                <div class="form-group">
                    <input type="submit" name="register_submit" value="Zarejestruj się" class="form-button">
                </div>
            </form>
            <div style="text-align: center; margin-top: 15px;">
                <a href="index.php?idp=konto" style="color: grey;">Masz już konto? Zaloguj się</a>
            </div>
        </div>
        ';
    }

    /**
     * Obsługuje proces logowania użytkownika.
     */
    public function ZalogujUzytkownika()
    {
        if (isset($_POST['login_submit'])) {
            // Verify reCAPTCHA
            if (!$this->verifyRecaptcha($_POST['g-recaptcha-response'])) {
                echo '<div class="notification error">Weryfikacja reCAPTCHA nie powiodła się.</div>';
                $this->pokazFormularzLogowania();
                return;
            }

            $login = mysqli_real_escape_string($this->link, $_POST['login_user']);
            $pass = $_POST['login_pass'];

            $query = "SELECT * FROM users WHERE login='$login' LIMIT 1";
            $result = mysqli_query($this->link, $query);

            if ($row = mysqli_fetch_assoc($result)) {
                if (password_verify($pass, $row['pass'])) {
                    // Logowanie poprawne
                    $_SESSION['user_id'] = $row['id'];
                    $_SESSION['user_login'] = $row['login'];

                    // Sync cart if available
                    if ($this->cart) {
                        $this->cart->syncWithDb($_SESSION['user_id']);
                    }

                    echo '<div class="notification success">Zalogowano pomyślnie.</div>';
                    header("Location: index.php?idp=konto");
                    echo '<script>window.location.href="index.php?idp=konto";</script>';
                } else {
                    echo '<div class="notification error">Nieprawidłowe hasło.</div>';
                    $this->pokazFormularzLogowania();
                }
            } else {
                echo '<div class="notification error">Nieprawidłowy login.</div>';
                $this->pokazFormularzLogowania();
            }
        } else {
            $this->pokazFormularzLogowania();
        }
    }

    /**
     * Wyświetla formularz logowania.
     */
    public function pokazFormularzLogowania()
    {
        global $recaptcha_site_key;
        echo '
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
        <div class="contact-container" style="max-width: 400px; margin: 40px auto;">
            <h2 class="heading">Logowanie</h2>
            <form method="post" action="index.php?idp=konto">
                <div class="form-group">
                    <label>E-mail (Login):</label>
                    <input type="text" name="login_user" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>Hasło:</label>
                    <input type="password" name="login_pass" class="form-input" required>
                </div>
                <div class="form-group" style="margin: 15px 0;">
                     <div class="g-recaptcha" data-sitekey="' . $recaptcha_site_key . '"></div>
                </div>
                <div class="form-group">
                    <input type="submit" name="login_submit" value="Zaloguj" class="form-button">
                </div>
            </form>
            <div style="text-align: center; margin-top: 15px;">
                <p>Nie masz konta?</p>
                <a href="index.php?idp=konto&action=register" class="btn btn-secondary" style="font-size: 0.9em;">Zarejestruj się</a>
            </div>
        </div>
        ';
    }

    /**
     * Wylogowuje użytkownika i przekierowuje na stronę konta.
     */
    public function Wyloguj()
    {
        unset($_SESSION['user_id']);
        unset($_SESSION['user_login']);
        session_destroy();
        session_start();
        header("Location: index.php?idp=konto");
        echo '<script>window.location.href="index.php?idp=konto";</script>';
    }

    /**
     * Wyświetla szczegóły konkretnego zamówienia dla zalogowanego użytkownika.
     * 
     * @param int $orderId ID zamówienia do wyświetlenia.
     */
    public function PokazSzczegolyZamowienia($orderId)
    {
        $userId = $_SESSION['user_id'];
        $orderId = intval($orderId);

        // 1. Sprawdź czy zamówienie należy do użytkownika
        $queryOrder = "SELECT * FROM orders WHERE id='$orderId' AND user_id='$userId' LIMIT 1";
        $resultOrder = mysqli_query($this->link, $queryOrder);

        if (!$rowOrder = mysqli_fetch_assoc($resultOrder)) {
            echo '<div class="notification error">Nie znaleziono zamówienia lub brak dostępu.</div>';
            echo '<div style="text-align: center;"><a href="index.php?idp=konto&action=orders" class="btn btn-secondary">Wróć do listy</a></div>';
            return;
        }

        echo '<div class="contact-container" style="max-width: 800px; margin: 40px auto;">';
        echo '<h2 class="heading">Szczegóły Zamówienia #' . $orderId . '</h2>';

        echo '<div style="margin-bottom: 20px;">';
        echo '<p><strong>Data:</strong> ' . $rowOrder['created_at'] . '</p>';
        echo '<p><strong>Status:</strong> ' . ucfirst($rowOrder['status']) . '</p>';
        echo '<p><strong>Łączna kwota:</strong> ' . number_format($rowOrder['total_amount'], 2) . ' zł</p>';
        echo '</div>';

        // 2. Pobierz pozycje zamówienia
        $queryItems = "SELECT oi.*, p.tytul 
                       FROM order_items oi 
                       LEFT JOIN products p ON oi.product_id = p.id 
                       WHERE oi.order_id='$orderId'";

        $resultItems = mysqli_query($this->link, $queryItems);

        echo '<div class="table-responsive">';
        echo '<table class="ranking-table">';
        echo '<thead><tr><th>Produkt</th><th>Cena (brutto)</th><th>Ilość</th><th>Wartość</th></tr></thead><tbody>';

        if (mysqli_num_rows($resultItems) > 0) {
            while ($item = mysqli_fetch_assoc($resultItems)) {
                $productName = $item['tytul'] ? htmlspecialchars($item['tytul']) : 'Produkt usunięty (ID: ' . $item['product_id'] . ')';
                $price = floatval($item['price_gross']);
                $qty = intval($item['quantity']);
                $val = $price * $qty;

                echo '<tr>';
                echo '<td>' . $productName . '</td>';
                echo '<td>' . number_format($price, 2) . ' zł</td>';
                echo '<td>' . $qty . '</td>';
                echo '<td>' . number_format($val, 2) . ' zł</td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="4">Brak pozycji w zamówieniu.</td></tr>';
        }

        echo '</tbody></table></div>';

        echo '<div style="text-align: center; margin-top: 20px;">';
        echo '<a href="index.php?idp=konto&action=orders" class="btn btn-secondary">Wróć do listy zamówień</a>';
        echo '</div>';
        echo '</div>';
    }

    /**
     * Wyświetla listę wszystkich zamówień złożonych przez zalogowanego użytkownika.
     */
    public function PokazZamowieniaUzytkownika()
    {
        $userId = $_SESSION['user_id'];
        echo '<div class="contact-container" style="max-width: 800px; margin: 40px auto;">';
        echo '<h2 class="heading">Twoje Zamówienia</h2>';

        $query = "SELECT * FROM orders WHERE user_id='$userId' ORDER BY created_at DESC";
        $result = mysqli_query($this->link, $query);

        if (mysqli_num_rows($result) > 0) {
            echo '<div class="table-responsive">';
            echo '<table class="ranking-table">';
            echo '<thead><tr><th>ID</th><th>Data</th><th>Status</th><th>Kwota</th><th>Akcje</th></tr></thead><tbody>';
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<tr>';
                echo '<td>#' . $row['id'] . '</td>';
                echo '<td>' . $row['created_at'] . '</td>';
                echo '<td>' . ucfirst($row['status']) . '</td>';
                echo '<td>' . number_format($row['total_amount'], 2) . ' zł</td>';
                echo '<td><a href="index.php?idp=konto&action=order_details&id=' . $row['id'] . '" class="btn btn-primary" style="padding: 5px 10px; font-size: 0.8rem;">Szczegóły</a></td>';
                echo '</tr>';
            }
            echo '</tbody></table></div>';
        } else {
            echo '<p style="text-align: center;">Brak zamówień.</p>';
        }

        echo '<div style="text-align: center; margin-top: 20px;">';
        echo '<a href="index.php?idp=konto" class="btn btn-secondary">Powrót do profilu</a>';
        echo '</div>';
        echo '</div>';
    }

    /**
     * Główna metoda wyświetlająca panel użytkownika.
     * Działa jako ruter dla akcji: logowanie, rejestracja, historia zamówień, wylogowanie.
     */
    public function PokazKonto()
    {
        // Router akcji
        if (isset($_GET['action'])) {
            if ($_GET['action'] == 'logout') {
                $this->Wyloguj();
                return;
            }
            if ($_GET['action'] == 'orders') {
                $this->PokazZamowieniaUzytkownika();
                return;
            }
            if ($_GET['action'] == 'order_details' && isset($_GET['id'])) {
                $this->PokazSzczegolyZamowienia($_GET['id']);
                return;
            }
        }

        if (isset($_SESSION['user_id'])) {
            // Panel użytkownika
            echo '<div class="contact-container" style="max-width: 600px; margin: 40px auto; text-align: center;">';
            echo '<h2 class="heading">Witaj, ' . htmlspecialchars($_SESSION['user_login']) . '!</h2>';
            echo '<p>Jesteś zalogowany w sklepie.</p>';

            echo '<div style="margin-top: 30px; display: flex; flex-direction: column; gap: 15px; align-items: center;">';
            echo '<a href="index.php?idp=sklep" class="btn btn-primary" style="width: 200px;">Przejdź do sklepu</a> ';
            echo '<a href="index.php?idp=konto&action=orders" class="btn btn-primary" style="width: 200px;">Moje Zamówienia</a> ';
            echo '<a href="index.php?idp=konto&action=logout" class="btn btn-secondary" style="background-color: #d9534f; width: 200px;">Wyloguj się</a>';
            echo '</div>';
            echo '</div>';

        } else {
            // Niezalogowany - obsługa formularzy
            if (isset($_POST['login_submit'])) {
                $this->ZalogujUzytkownika();
            } elseif (isset($_POST['register_submit'])) {
                $this->RejestrujUzytkownika();
            } elseif (isset($_GET['action']) && $_GET['action'] == 'register') {
                $this->RejestrujUzytkownika(); // Wyświetlenie formularza
            } else {
                $this->ZalogujUzytkownika(); // Domyślnie logowanie
            }
        }
    }
}
?>