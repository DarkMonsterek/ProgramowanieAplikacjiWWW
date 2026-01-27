-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Sty 27, 2026 at 09:34 PM
-- Wersja serwera: 10.4.28-MariaDB
-- Wersja PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `moja_strona`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart_items`
--

INSERT INTO `cart_items` (`id`, `user_id`, `product_id`, `quantity`, `created_at`) VALUES
(1, 5, 2, 1, '2026-01-27 21:01:24');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `matka` int(11) DEFAULT 0,
  `nazwa` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `matka`, `nazwa`) VALUES
(3, 1, 'test'),
(7, 1, 'test'),
(8, 0, 'pamiątki'),
(9, 8, 'magnesy'),
(10, 8, 'statułetki');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT 0,
  `total_amount` decimal(10,2) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'nowe',
  `created_at` datetime DEFAULT current_timestamp(),
  `email` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `status`, `created_at`, `email`) VALUES
(4, 5, 12.30, 'w realizacji', '2026-01-24 17:54:05', 'test1@gmail.com'),
(5, 0, 12.30, 'oczekujące', '2026-01-26 14:48:09', 'przem204@gmail.com');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price_gross` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price_gross`) VALUES
(4, 4, 3, 1, 12.30),
(5, 5, 3, 1, 12.30);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `page_list`
--

CREATE TABLE `page_list` (
  `id` int(11) NOT NULL,
  `page_title` varchar(255) NOT NULL,
  `page_content` text NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `page_list`
--

INSERT INTO `page_list` (`id`, `page_title`, `page_content`, `status`) VALUES
(1, 'Strona Główna', '<main>\r\n    <section class=\"hero\">\r\n        <div class=\"hero-content\">\r\n            <h1>Sięgając Nieba</h1>\r\n            <p>Odkryj geniusz inżynierii i architektoniczne cuda, które definiują współczesny świat.</p>\r\n            <a href=\"#ranking\" id=\"ranking-btn\" class=\"btn btn-primary\">Zobacz Ranking</a>\r\n        </div>\r\n    </section>\r\n\r\n    <section class=\"intro container\">\r\n        <h2>Współczesne Kolosy</h2>\r\n        <p>\r\n            Drapacze chmur to nie tylko budynki; to symbole ludzkich ambicji, postępu technologicznego i potęgi\r\n            ekonomicznej.\r\n            Od pustynnych piasków Dubaju po tętniące życiem metropolie Chin, te struktury redefiniują granice tego,\r\n            co możliwe w budownictwie.\r\n            Na tej stronie przyjrzymy się bliżej pięciu najwyższym budynkom, które obecnie dominują na światowej\r\n            mapie wysokości.\r\n        </p>\r\n    </section>\r\n\r\n    <section id=\"ranking\" class=\"ranking container\">\r\n        <h2>Ranking Top 5</h2>\r\n        <div class=\"table-responsive\">\r\n            <table class=\"ranking-table\">\r\n                <thead>\r\n                    <tr>\r\n                        <th>Ranking</th>\r\n                        <th>Nazwa Budynku</th>\r\n                        <th>Miasto</th>\r\n                        <th>Kraj</th>\r\n                        <th>Wysokość (m)</th>\r\n                        <th>Liczba Pięter</th>\r\n                    </tr>\r\n                </thead>\r\n                <tbody>\r\n                    <tr>\r\n                        <td>1</td>\r\n                        <td><a href=\"index.php?idp=burj_khalifa\">Burj Khalifa</a></td>\r\n                        <td>Dubaj</td>\r\n                        <td>Zjednoczone Emiraty Arabskie</td>\r\n                        <td>828 m</td>\r\n                        <td>163</td>\r\n                    </tr>\r\n                    <tr>\r\n                        <td>2</td>\r\n                        <td><a href=\"index.php?idp=merdeka_118\">Merdeka 118</a></td>\r\n                        <td>Kuala Lumpur</td>\r\n                        <td>Malezja</td>\r\n                        <td>678.9 m</td>\r\n                        <td>118</td>\r\n                    </tr>\r\n                    <tr>\r\n                        <td>3</td>\r\n                        <td><a href=\"index.php?idp=shanghai_tower\">Shanghai Tower</a></td>\r\n                        <td>Szanghaj</td>\r\n                        <td>Chiny</td>\r\n                        <td>632 m</td>\r\n                        <td>128</td>\r\n                    </tr>\r\n                    <tr>\r\n                        <td>4</td>\r\n                        <td><a href=\"index.php?idp=abraj_al_bait\">Abraj Al-Bait</a></td>\r\n                        <td>Mekka</td>\r\n                        <td>Arabia Saudyjska</td>\r\n                        <td>601 m</td>\r\n                        <td>120</td>\r\n                    </tr>\r\n                    <tr>\r\n                        <td>5</td>\r\n                        <td><a href=\"index.php?idp=ping_an\">Ping An Finance Center</a></td>\r\n                        <td>Shenzhen</td>\r\n                        <td>Chiny</td>\r\n                        <td>599.1 m</td>\r\n                        <td>115</td>\r\n                    </tr>\r\n                </tbody>\r\n            </table>\r\n        </div>\r\n    </section>\r\n\r\n    <section id=\"gallery\" class=\"gallery container\">\r\n        <h2>Galeria Gigantów</h2>\r\n        <div class=\"gallery-grid\">\r\n            <article class=\"card\">\r\n                <img src=\"images/burj_khalifa.png\" alt=\"Burj Khalifa\">\r\n                <div class=\"card-content\">\r\n                    <h3>Burj Khalifa</h3>\r\n                    <a href=\"index.php?idp=burj_khalifa\" class=\"btn btn-secondary\">Zobacz więcej</a>\r\n                </div>\r\n            </article>\r\n            <article class=\"card\">\r\n                <img src=\"images/merdeka_118.png\" alt=\"Merdeka 118\">\r\n                <div class=\"card-content\">\r\n                    <h3>Merdeka 118</h3>\r\n                    <a href=\"index.php?idp=merdeka_118\" class=\"btn btn-secondary\">Zobacz więcej</a>\r\n                </div>\r\n            </article>\r\n            <article class=\"card\">\r\n                <img src=\"images/shanghai_tower.png\" alt=\"Shanghai Tower\">\r\n                <div class=\"card-content\">\r\n                    <h3>Shanghai Tower</h3>\r\n                    <a href=\"index.php?idp=shanghai_tower\" class=\"btn btn-secondary\">Zobacz więcej</a>\r\n                </div>\r\n            </article>\r\n            <article class=\"card\">\r\n                <img src=\"images/abraj_al_bait.png\" alt=\"Abraj Al-Bait\">\r\n                <div class=\"card-content\">\r\n                    <h3>Abraj Al-Bait</h3>\r\n                    <a href=\"index.php?idp=abraj_al_bait\" class=\"btn btn-secondary\">Zobacz więcej</a>\r\n                </div>\r\n            </article>\r\n            <article class=\"card\">\r\n                <img src=\"images/ping_an.png\" alt=\"Ping An Finance Center\">\r\n                <div class=\"card-content\">\r\n                    <h3>Ping An Finance Center</h3>\r\n                    <a href=\"index.php?idp=ping_an\" class=\"btn btn-secondary\">Zobacz więcej</a>\r\n                </div>\r\n            </article>\r\n        </div>\r\n    </section>\r\n</main>\r\n\r\n<script>\r\n    // Animacja przycisku Ranking\r\n    $(\"#ranking-btn\").on({\r\n        \"mouseenter\": function () {\r\n            $(this).stop().animate({\r\n                fontSize: \"1.2em\",\r\n                paddingLeft: \"30px\",\r\n                paddingRight: \"30px\"\r\n            }, 200);\r\n        },\r\n        \"mouseleave\": function () {\r\n            $(this).stop().animate({\r\n                fontSize: \"1em\",\r\n                paddingLeft: \"20px\",\r\n                paddingRight: \"20px\"\r\n            }, 200);\r\n        }\r\n    });\r\n</script>', 1),
(2, 'Burj Khalifa', '<header class=\"page-header\">\n    <div class=\"container\">\n        <h1>Burj Khalifa</h1>\n        <p>Dubaj, Zjednoczone Emiraty Arabskie</p>\n    </div>\n</header>\n\n<main>\n    <section class=\"building-detail container\">\n        <div class=\"detail-grid\">\n            <div class=\"detail-content\">\n                <h2>Ikona Nowoczesności</h2>\n                <p>\n                    Burj Khalifa to nie tylko najwyższy budynek na świecie, ale także globalna ikona i cud\n                    inżynierii.\n                    Wznoszący się na wysokość 828 metrów nad pustynią Dubaju, budynek ten stanowi serce nowej\n                    dzielnicy Downtown Dubai.\n                    Jego spiralna konstrukcja nawiązuje do kwiatu hymenocallis, co zapewnia stabilność przy silnych\n                    wiatrach.\n                </p>\n                <p>\n                    Budowa rozpoczęła się w 2004 roku, a oficjalne otwarcie nastąpiło w 2010 roku. Wieżowiec mieści\n                    apartamenty mieszkalne,\n                    biura, pierwszy na świecie hotel marki Armani oraz tarasy widokowe, z których roztacza się\n                    zapierający dech w piersiach widok na Zatokę Perską.\n                </p>\n\n                <div class=\"detail-stats\">\n                    <h3>Dane Techniczne</h3>\n                    <ul class=\"stats-list\">\n                        <li><span>Wysokość całkowita:</span> <strong>828 m</strong></li>\n                        <li><span>Liczba pięter:</span> <strong>163</strong></li>\n                        <li><span>Rok ukończenia:</span> <strong>2010</strong></li>\n                        <li><span>Architekt:</span> <strong>Adrian Smith (SOM)</strong></li>\n                        <li><span>Koszt budowy:</span> <strong>~1.5 mld USD</strong></li>\n                    </ul>\n                </div>\n            </div>\n            <div class=\"detail-image\">\n                <img src=\"images/burj_khalifa.png\" alt=\"Burj Khalifa w słońcu\">\n            </div>\n        </div>\n    </section>\n\n    <section class=\"fun-facts container\">\n        <h2>Ciekawostki</h2>\n        <ol>\n            <li>Burj Khalifa jest trzykrotnie wyższy od Wieży Eiffla.</li>\n            <li>Beton użyty do budowy waży tyle, co 100 000 słoni.</li>\n            <li>System kondensacji wody w budynku odzyskuje rocznie około 15 milionów litrów wody, która służy do\n                nawadniania roślin wokół wieży.</li>\n            <li>Winda wjeżdża na 124. piętro w zaledwie minutę, poruszając się z prędkością 10 m/s.</li>\n            <li>Czubek wieży jest dostrzegalny z odległości 95 kilometrów.</li>\n        </ol>\n    </section>\n</main>', 1),
(3, 'Merdeka 118', '<header class=\"page-header\">\n    <div class=\"container\">\n        <h1>Merdeka 118</h1>\n        <p>Kuala Lumpur, Malezja</p>\n    </div>\n</header>\n\n<main>\n    <section class=\"building-detail container\">\n        <div class=\"detail-grid\">\n            <div class=\"detail-content\">\n                <h2>Klejnot Azji Południowo-Wschodniej</h2>\n                <p>\n                    Merdeka 118, znany również jako PNB 118, to drugi co do wysokości budynek na świecie, dominujący\n                    nad panoramą Kuala Lumpur.\n                    Jego nazwa nawiązuje do liczby pięter oraz słowa \"Merdeka\", co w języku malajskim oznacza\n                    \"Niepodległość\".\n                    Lokalizacja wieżowca w pobliżu Stadionu Merdeka, miejsca deklaracji niepodległości Malezji,\n                    nadaje mu szczególne znaczenie historyczne.\n                </p>\n                <p>\n                    Fasetowa, szklana fasada budynku została zainspirowana wzorami tradycyjnej malajskiej sztuki i\n                    rzemiosła songket.\n                    Iglica wieńcząca budynek dodaje mu smukłości i jest kluczowym elementem osiągnięcia jego\n                    imponującej wysokości.\n                </p>\n\n                <div class=\"detail-stats\">\n                    <h3>Dane Techniczne</h3>\n                    <ul class=\"stats-list\">\n                        <li><span>Wysokość całkowita:</span> <strong>678.9 m</strong></li>\n                        <li><span>Liczba pięter:</span> <strong>118</strong></li>\n                        <li><span>Rok ukończenia:</span> <strong>2023</strong></li>\n                        <li><span>Architekt:</span> <strong>Fender Katsalidis</strong></li>\n                        <li><span>Użytkowanie:</span> <strong>Biura, Hotel, Galerie</strong></li>\n                    </ul>\n                </div>\n            </div>\n            <div class=\"detail-image\">\n                <img src=\"images/merdeka_118.png\" alt=\"Merdeka 118 w Kuala Lumpur\">\n            </div>\n        </div>\n    </section>\n\n    <section class=\"fun-facts container\">\n        <h2>Ciekawostki</h2>\n        <ol>\n            <li>Jest to pierwszy budynek w południowo-wschodniej Azji, który przekroczył wysokość 600 metrów\n                (megatall).</li>\n            <li>Taras widokowy \"The View at 118\" jest najwyższym w Azji Południowo-Wschodniej.</li>\n            <li>Kompleks Merdeka 118 obejmuje również centrum handlowe ze szklaną kopułą.</li>\n            <li>Kształt wieży przypomina sylwetkę Tunku Abdula Rahmana z uniesioną ręką, wykrzykującego \"Merdeka!\" w\n                1957 roku.</li>\n            <li>Budynek posiada certyfikat LEED Platinum, co świadczy o jego ekologicznym charakterze.</li>\n        </ol>\n    </section>\n</main>', 1),
(4, 'Shanghai Tower', '<header class=\"page-header\">\n    <div class=\"container\">\n        <h1>Shanghai Tower</h1>\n        <p>Szanghaj, Chiny</p>\n    </div>\n</header>\n\n<main>\n    <section class=\"building-detail container\">\n        <div class=\"detail-grid\">\n            <div class=\"detail-content\">\n                <h2>Skręcony Smok</h2>\n                <p>\n                    Shanghai Tower to najwyższy budynek w Chinach i trzeci na świecie. Jego najbardziej\n                    charakterystyczną cechą jest\n                    spiralna fasada, która skręca się o około 120 stopni w miarę wznoszenia się ku niebu.\n                    Taki kształt nie jest tylko zabiegiem estetycznym – zmniejsza on obciążenie wiatrem o 24%, co\n                    pozwoliło zaoszczędzić miliony dolarów na materiałach konstrukcyjnych.\n                </p>\n                <p>\n                    Wieżowiec jest podzielony na dziewięć pionowych stref, z których każda posiada własne atrium\n                    (\"niebiański ogród\").\n                    Działa to jak wertykalne miasto, zapewniając mieszkańcom i pracownikom dostęp do usług bez\n                    konieczności zjeżdżania na parter.\n                </p>\n\n                <div class=\"detail-stats\">\n                    <h3>Dane Techniczne</h3>\n                    <ul class=\"stats-list\">\n                        <li><span>Wysokość całkowita:</span> <strong>632 m</strong></li>\n                        <li><span>Liczba pięter:</span> <strong>128</strong></li>\n                        <li><span>Rok ukończenia:</span> <strong>2015</strong></li>\n                        <li><span>Architekt:</span> <strong>Gensler</strong></li>\n                        <li><span>Windy:</span> <strong>Prędkość do 20.5 m/s</strong></li>\n                    </ul>\n                </div>\n            </div>\n            <div class=\"detail-image\">\n                <img src=\"images/shanghai_tower.png\" alt=\"Shanghai Tower w chmurach\">\n            </div>\n        </div>\n    </section>\n\n    <section class=\"fun-facts container\">\n        <h2>Ciekawostki</h2>\n        <ol>\n            <li>Posiada jedne z najszybszych wind na świecie.</li>\n            <li>Podwójna szklana fasada działa jak termos, poprawiając efektywność energetyczną budynku.</li>\n            <li>Budynek zbiera deszczówkę, która jest wykorzystywana w systemach klimatyzacji i ogrzewania.</li>\n            <li>Na 118. piętrze znajduje się taras widokowy z widokiem 360 stopni na Szanghaj.</li>\n            <li>Fundamenty budynku składają się z niemal 1000 pali wbitych na głębokość 86 metrów.</li>\n        </ol>\n    </section>\n</main>', 1),
(5, 'Abraj Al-Bait', '<header class=\"page-header\">\n    <div class=\"container\">\n        <h1>Abraj Al-Bait</h1>\n        <p>Mekka, Arabia Saudyjska</p>\n    </div>\n</header>\n\n<main>\n    <section class=\"building-detail container\">\n        <div class=\"detail-grid\">\n            <div class=\"detail-content\">\n                <h2>Strażnik Czasu</h2>\n                <p>\n                    Abraj Al-Bait, znany również jako Makkah Royal Clock Tower, to rządowy kompleks hotelowy w\n                    Mekce.\n                    Znajduje się zaledwie kilkadziesiąt metrów od Wielkiego Meczetu i Kaaby, najświętszego miejsca\n                    islamu.\n                    Kompleks składa się z siedmiu wieżowców, z których centralna wieża zegarowa dominuje nad\n                    całością.\n                </p>\n                <p>\n                    Zegar na szczycie wieży jest największym i najwyżej położonym zegarem wieżowym na świecie.\n                    Każda z czterech tarcz ma średnicę 43 metrów i jest widoczna z odległości 25 kilometrów.\n                    Szczyt wieńczy złoty półksiężyc o wadze 35 ton.\n                </p>\n\n                <div class=\"detail-stats\">\n                    <h3>Dane Techniczne</h3>\n                    <ul class=\"stats-list\">\n                        <li><span>Wysokość całkowita:</span> <strong>601 m</strong></li>\n                        <li><span>Liczba pięter:</span> <strong>120</strong></li>\n                        <li><span>Rok ukończenia:</span> <strong>2012</strong></li>\n                        <li><span>Funkcja:</span> <strong>Hotel, Centrum Handlowe, Muzeum</strong></li>\n                        <li><span>Powierzchnia:</span> <strong>1.5 mln m²</strong></li>\n                    </ul>\n                </div>\n            </div>\n            <div class=\"detail-image\">\n                <img src=\"images/abraj_al_bait.png\" alt=\"Abraj Al-Bait w Mekce\">\n            </div>\n        </div>\n    </section>\n\n    <section class=\"fun-facts container\">\n        <h2>Ciekawostki</h2>\n        <ol>\n            <li>Jest to najdroższy budynek na świecie, koszt budowy wyniósł około 15 miliardów dolarów.</li>\n            <li>Posiada największą powierzchnię użytkową ze wszystkich budynków na świecie.</li>\n            <li>Zegar jest 35 razy większy od zegara Big Ben w Londynie.</li>\n            <li>Wewnątrz półksiężyca na szczycie znajduje się najwyżej położony pokój modlitewny na świecie.</li>\n            <li>Kompleks może pomieścić do 100 000 gości jednocześnie.</li>\n        </ol>\n    </section>\n</main>', 1),
(6, 'Ping An Finance Center', '<header class=\"page-header\">\n    <div class=\"container\">\n        <h1>Ping An Finance Center</h1>\n        <p>Shenzhen, Chiny</p>\n    </div>\n</header>\n\n<main>\n    <section class=\"building-detail container\">\n        <div class=\"detail-grid\">\n            <div class=\"detail-content\">\n                <h2>Stalowy Gigant</h2>\n                <p>\n                    Ping An Finance Center to symbol szybkiego rozwoju Shenzhen – od wioski rybackiej do\n                    technologicznej potęgi.\n                    Budynek charakteryzuje się smukłą, stożkową formą, która została zaprojektowana tak, aby\n                    minimalizować napór wiatru.\n                    Jego fasada wykonana jest z największej na świecie ilości stali nierdzewnej użytej w jednym\n                    budynku (ok. 1 700 ton), co sprawia, że jest odporna na korozję w słonym, nadmorskim klimacie.\n                </p>\n                <p>\n                    Początkowo planowano, że budynek będzie miał 660 metrów i iglicę, jednak ze względu na\n                    ograniczenia lotnicze, iglica została usunięta, ustalając ostateczną wysokość na 599 metrów.\n                </p>\n\n                <div class=\"detail-stats\">\n                    <h3>Dane Techniczne</h3>\n                    <ul class=\"stats-list\">\n                        <li><span>Wysokość całkowita:</span> <strong>599.1 m</strong></li>\n                        <li><span>Liczba pięter:</span> <strong>115</strong></li>\n                        <li><span>Rok ukończenia:</span> <strong>2017</strong></li>\n                        <li><span>Architekt:</span> <strong>Kohn Pedersen Fox (KPF)</strong></li>\n                        <li><span>Konstrukcja:</span> <strong>Kompozyt (Beton/Stal)</strong></li>\n                    </ul>\n                </div>\n            </div>\n            <div class=\"detail-image\">\n                <img src=\"images/ping_an.png\" alt=\"Ping An Finance Center\">\n            </div>\n        </div>\n    </section>\n\n    <section class=\"fun-facts container\">\n        <h2>Ciekawostki</h2>\n        <ol>\n            <li>Na 116. piętrze znajduje się taras widokowy Free Sky 116.</li>\n            <li>Kształt budynku został przetestowany w tunelu aerodynamicznym, co pozwoliło zmniejszyć obciążenie\n                wiatrem o 35%.</li>\n            <li>Winda w budynku jest jedną z niewielu na świecie, która wykorzystuje technologię podwójnej kabiny.\n            </li>\n            <li>Fasada ze stali nierdzewnej ma powierzchnię równą 14 boiskom do piłki nożnej.</li>\n            <li>Budynek posiada 33 windy double-decker (dwupoziomowe).</li>\n        </ol>\n    </section>\n</main>', 1),
(7, 'Filmy', '<div class=\"fade-in\">\n    <h2>Filmy o największych budynkach</h2>\n    <div style=\"display: flex; flex-wrap: wrap; gap: 20px; justify-content: center;\">\n        <div class=\"video-container\">\n            <iframe width=\"560\" height=\"315\" src=\"https://www.youtube.com/embed/r9omqwqHNiE?si=wJxRN7k2txwYAT2H\"\n                title=\"YouTube video player\" frameborder=\"0\"\n                allow=\"accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share\"\n                referrerpolicy=\"strict-origin-when-cross-origin\" allowfullscreen></iframe>\n        </div>\n        <div class=\"video-container\">\n            <iframe width=\"560\" height=\"315\" src=\"https://www.youtube.com/embed/jUEhFgZqNWE?si=ftVNE4VkAFx-kA7j\"\n                title=\"YouTube video player\" frameborder=\"0\"\n                allow=\"accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share\"\n                referrerpolicy=\"strict-origin-when-cross-origin\" allowfullscreen></iframe>\n        </div>\n        <div class=\"video-container\">\n            <iframe width=\"560\" height=\"315\" src=\"https://www.youtube.com/embed/CuK3SAzCrAA?si=3AAseyTqYNUZOwSl\"\n                title=\"YouTube video player\" frameborder=\"0\"\n                allow=\"accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share\"\n                referrerpolicy=\"strict-origin-when-cross-origin\" allowfullscreen></iframe>\n        </div>\n    </div>\n</div>', 1);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `tytul` varchar(255) NOT NULL,
  `opis` text DEFAULT NULL,
  `data_utworzenia` datetime DEFAULT current_timestamp(),
  `data_modyfikacji` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `data_wygasniecia` datetime DEFAULT NULL,
  `cena_netto` decimal(10,2) NOT NULL,
  `podatek_vat` decimal(5,2) DEFAULT 0.23,
  `ilosc_dostepnych_sztuk` int(11) DEFAULT 0,
  `status_dostepnosci` int(11) DEFAULT 1,
  `kategoria` int(11) DEFAULT NULL,
  `gabaryt_produktu` varchar(50) DEFAULT NULL,
  `zdjecie` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `tytul`, `opis`, `data_utworzenia`, `data_modyfikacji`, `data_wygasniecia`, `cena_netto`, `podatek_vat`, `ilosc_dostepnych_sztuk`, `status_dostepnosci`, `kategoria`, `gabaryt_produktu`, `zdjecie`) VALUES
(1, 'magnes z wieżą eiffla', 'test', '2026-01-20 23:44:51', '2026-01-27 20:12:38', '2026-01-30 23:42:00', 150.00, 0.23, 1, 1, 9, 'tak', 'https://viktoriia.pl/210910-large_default/magnes-na-lodowke-paryz-wieza-eiffla.jpg'),
(2, 'Shanghai Pamiątka', 'Vintage Model metalowy szanghajski posąg półka dekoracja prezent\r\n\r\nTen ma 20 cm wysokości i jest bardzo szczegółowy.\r\n\r\nMateriał : metal\r\n\r\nBardzo świetny artykuł meblowy do umieszczenia w sypialni, domu, barach, kawiarniach, restauracjach, na weselu lub w innych romantycznych miejscach do dekoracji.\r\n\r\nMożna również wysłać jako prezent lub pamiątkę znajomym/kochankom/rodzinom.', '2026-01-21 08:30:50', '2026-01-27 20:12:20', '2026-01-30 08:28:00', 21.00, 0.23, 100, 1, 10, 'nie', 'https://a.allegroimg.com/original/116b4c/97bddf094905a3321872a217b7ad/Slynny-budynek-Model-wiezy-w-Szanghaju-Chiny-Pamiatka-Kultowy-punkt-orientacyjny-8'),
(3, 'burj khalifa statuletka', 'statuletka', '2026-01-21 12:36:17', '2026-01-26 14:48:09', '2026-01-29 12:36:00', 10.00, 0.23, 5, 1, 10, 'nie', 'https://m.media-amazon.com/images/I/613N9AUM0SL._AC_UF894,1000_QL80_.jpg');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `login` varchar(50) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `login`, `pass`, `status`, `created_at`) VALUES
(5, 'test1@gmail.com', '$2y$10$9sP5b0GC3XafuYrNhUxtoe5AtRmt/4gBWn9sHSU7edelUDMgU8bjC', 1, '2026-01-24 17:53:35');

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeksy dla tabeli `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indeksy dla tabeli `page_list`
--
ALTER TABLE `page_list`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login` (`login`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `page_list`
--
ALTER TABLE `page_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
