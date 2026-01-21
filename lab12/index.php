<?php
/**
 * Główny plik indeksowy strony
 * Odpowiada za strukturę strony, routing i wyświetlanie treści
 */

session_start();
include('cfg.php');
include('showpage.php');
include('contact.php');
include('cart.php');
include('shop.php');

$cart = new ShoppingCart($link);
?>
<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Projekt 1">
    <meta name="keywords" content="HTML5, CSS3, JavaScript">
    <meta name="author" content="Przemysław Karpowicz">
    <title>Największe Budynki Świata | Summit</title>
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
    <script src="js/kolorujtlo.js" type="text/javascript"></script>
    <script src="js/timedate.js" type="text/javascript"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>

<body bgcolor="#0d1b2a" onload="startclock()">

    <header class="main-header">
        <div class="container header-container">
            <div class="logo">
                <a href="index.php">
                    <img src="images/logo.png" alt="Summit Logo" class="logo-img">
                </a>
            </div>
            <nav class="main-nav">
                <ul>
                    <li><a href="index.php?idp=glowna"
                            class="<?php echo (!isset($_GET['idp']) || $_GET['idp'] == '' || $_GET['idp'] == 'glowna') ? 'active' : ''; ?>">Strona
                            Główna</a></li>
                    <li><a href="index.php?idp=burj_khalifa"
                            class="<?php echo ($_GET['idp'] == 'burj_khalifa') ? 'active' : ''; ?>">Burj Khalifa</a>
                    </li>
                    <li><a href="index.php?idp=merdeka_118"
                            class="<?php echo ($_GET['idp'] == 'merdeka_118') ? 'active' : ''; ?>">Merdeka 118</a></li>
                    <li><a href="index.php?idp=shanghai_tower"
                            class="<?php echo ($_GET['idp'] == 'shanghai_tower') ? 'active' : ''; ?>">Shanghai Tower</a>
                    </li>
                    <li><a href="index.php?idp=abraj_al_bait"
                            class="<?php echo ($_GET['idp'] == 'abraj_al_bait') ? 'active' : ''; ?>">Abraj Al-Bait</a>
                    </li>
                    <li><a href="index.php?idp=ping_an"
                            class="<?php echo ($_GET['idp'] == 'ping_an') ? 'active' : ''; ?>">Ping An Center</a></li>
                    <li><a href="index.php?idp=filmy"
                            class="<?php echo ($_GET['idp'] == 'filmy') ? 'active' : ''; ?>">Filmy</a></li>
                    <li><a href="index.php?idp=sklep"
                            class="<?php echo ($_GET['idp'] == 'sklep') ? 'active' : ''; ?>">Sklep</a></li>
                    <li><a href="index.php?idp=koszyk"
                            class="<?php echo ($_GET['idp'] == 'koszyk') ? 'active' : ''; ?>">Koszyk</a></li>
                    <li><a href="index.php?idp=kontakt"
                            class="<?php echo ($_GET['idp'] == 'kontakt') ? 'active' : ''; ?>">Kontakt</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <?php
    error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

    // Dynamiczne ładowanie treści podstron na podstawie parametru 'idp'
    $stronaId = null;

    if (empty($_GET['idp'])) {
        $stronaId = 1;
    } else {
        switch ($_GET['idp']) {
            case 'glowna':
                $stronaId = 1;
                break;
            case 'burj_khalifa':
                $stronaId = 2;
                break;
            case 'merdeka_118':
                $stronaId = 3;
                break;
            case 'shanghai_tower':
                $stronaId = 4;
                break;
            case 'abraj_al_bait':
                $stronaId = 5;
                break;
            case 'ping_an':
                $stronaId = 6;
                break;
            case 'filmy':
                $stronaId = 7;
                break;
            case 'sklep':
                // Wyświetlenie sklepu
                PokazSklep($link, $cart);
                break;
            case 'koszyk':
                // Wyświetlenie koszyka
                $cart->handleCartAction();
                $cart->showCart();
                break;
            case 'kontakt':
                // Obsługa strony kontaktowej
                if (isset($_POST['wyslij_kontakt'])) {
                    WyslijMailKontakt("admin@moj_projekt.pl");
                } else {
                    echo PokazKontakt();
                }
                break;
            default:
                // Domyślnie strona główna w przypadku nieznanego parametru
                $stronaId = 1;
                break;
        }
    }

    // Wyświetlenie treści podstrony, jeśli przypisano ID
    if ($stronaId) {
        echo PokazPodstrone($stronaId);
    }
    ?>


    <footer class="main-footer">
        <div class="container footer-content">
            <div class="footer-info">
                <p>&copy; 2026 Summit - Największe Budynki Świata.</p>
                <div id="data" style="margin-top: 5px; font-size: 0.9em; color: var(--text-muted);"></div>
                <div id="zegarek" style="margin-top: 2px; font-size: 0.9em; color: var(--text-muted);"></div>
            </div>
            <div class="background-changer" style="margin-top: 20px; width: 100%; text-align: center;">
                <p style="margin-bottom: 10px; color: var(--accent-color);">Zmień kolor tła:</p>
                <FORM METHOD="POST" NAME="background">
                    <INPUT TYPE="button" VALUE="żółty" ONCLICK="changeBackground('#FFF000')">
                    <INPUT TYPE="button" VALUE="czarny" ONCLICK="changeBackground('#000000')">
                    <INPUT TYPE="button" VALUE="biały" ONCLICK="changeBackground('#FFFFFF')">
                    <INPUT TYPE="button" VALUE="zielony" ONCLICK="changeBackground('#00FF00')">
                    <INPUT TYPE="button" VALUE="niebieski" ONCLICK="changeBackground('#0000FF')">
                    <INPUT TYPE="button" VALUE="pomarańczowy" ONCLICK="changeBackground('#FF8000')">
                    <INPUT TYPE="button" VALUE="szary" ONCLICK="changeBackground('#c0c0c0')">
                    <INPUT TYPE="button" VALUE="czerwony" ONCLICK="changeBackground('#FF0000')">
                </FORM>
            </div>

            <nav class="footer-nav">
                <ul>
                    <li><a href="index.php">Strona Główna</a></li>
                    <li><a href="index.php#ranking">Ranking</a></li>
                    <li><a href="index.php#gallery">Galeria</a></li>
                    <li><a href="admin/admin.php">Panel Admina</a></li>
                </ul>
            </nav>
        </div>
    </footer>
    <?php
    $nr_indeksu = '175003';
    $nrGrupy = 'ISI2';
    // Informacja o autorze w stopce
    echo 'Autor: Przemysław Karpowicz ' . $nr_indeksu . ' grupa ' . $nrGrupy . ' <br /><br />';
    ?>
</body>

</html>