-- Tworzenie tabeli page_list
CREATE TABLE IF NOT EXISTS `page_list` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `page_title` VARCHAR(255) NOT NULL,
  `page_content` TEXT NOT NULL,
  `status` INT NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Wstawianie zawartości podstron

-- Strona Główna
INSERT INTO `page_list` (`page_title`, `page_content`, `status`) VALUES
('Strona Główna', '<main>
    <section class="hero">
        <div class="hero-content">
            <h1>Sięgając Nieba</h1>
            <p>Odkryj geniusz inżynierii i architektoniczne cuda, które definiują współczesny świat.</p>
            <a href="#ranking" class="btn btn-primary">Zobacz Ranking</a>
        </div>
    </section>

    <section class="intro container">
        <h2>Współczesne Kolosy</h2>
        <p>
            Drapacze chmur to nie tylko budynki; to symbole ludzkich ambicji, postępu technologicznego i potęgi
            ekonomicznej.
            Od pustynnych piasków Dubaju po tętniące życiem metropolie Chin, te struktury redefiniują granice tego,
            co możliwe w budownictwie.
            Na tej stronie przyjrzymy się bliżej pięciu najwyższym budynkom, które obecnie dominują na światowej
            mapie wysokości.
        </p>
    </section>

    <section id="ranking" class="ranking container">
        <h2>Ranking Top 5</h2>
        <div class="table-responsive">
            <table class="ranking-table">
                <thead>
                    <tr>
                        <th>Ranking</th>
                        <th>Nazwa Budynku</th>
                        <th>Miasto</th>
                        <th>Kraj</th>
                        <th>Wysokość (m)</th>
                        <th>Liczba Pięter</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td><a href="index.php?idp=burj_khalifa">Burj Khalifa</a></td>
                        <td>Dubaj</td>
                        <td>Zjednoczone Emiraty Arabskie</td>
                        <td>828 m</td>
                        <td>163</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td><a href="index.php?idp=merdeka_118">Merdeka 118</a></td>
                        <td>Kuala Lumpur</td>
                        <td>Malezja</td>
                        <td>678.9 m</td>
                        <td>118</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td><a href="index.php?idp=shanghai_tower">Shanghai Tower</a></td>
                        <td>Szanghaj</td>
                        <td>Chiny</td>
                        <td>632 m</td>
                        <td>128</td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td><a href="index.php?idp=abraj_al_bait">Abraj Al-Bait</a></td>
                        <td>Mekka</td>
                        <td>Arabia Saudyjska</td>
                        <td>601 m</td>
                        <td>120</td>
                    </tr>
                    <tr>
                        <td>5</td>
                        <td><a href="index.php?idp=ping_an">Ping An Finance Center</a></td>
                        <td>Shenzhen</td>
                        <td>Chiny</td>
                        <td>599.1 m</td>
                        <td>115</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <section id="gallery" class="gallery container">
        <h2>Galeria Gigantów</h2>
        <div class="gallery-grid">
            <article class="card">
                <img src="images/burj_khalifa.png" alt="Burj Khalifa">
                <div class="card-content">
                    <h3>Burj Khalifa</h3>
                    <a href="index.php?idp=burj_khalifa" class="btn btn-secondary">Zobacz więcej</a>
                </div>
            </article>
            <article class="card">
                <img src="images/merdeka_118.png" alt="Merdeka 118">
                <div class="card-content">
                    <h3>Merdeka 118</h3>
                    <a href="index.php?idp=merdeka_118" class="btn btn-secondary">Zobacz więcej</a>
                </div>
            </article>
            <article class="card">
                <img src="images/shanghai_tower.png" alt="Shanghai Tower">
                <div class="card-content">
                    <h3>Shanghai Tower</h3>
                    <a href="index.php?idp=shanghai_tower" class="btn btn-secondary">Zobacz więcej</a>
                </div>
            </article>
            <article class="card">
                <img src="images/abraj_al_bait.png" alt="Abraj Al-Bait">
                <div class="card-content">
                    <h3>Abraj Al-Bait</h3>
                    <a href="index.php?idp=abraj_al_bait" class="btn btn-secondary">Zobacz więcej</a>
                </div>
            </article>
            <article class="card">
                <img src="images/ping_an.png" alt="Ping An Finance Center">
                <div class="card-content">
                    <h3>Ping An Finance Center</h3>
                    <a href="index.php?idp=ping_an" class="btn btn-secondary">Zobacz więcej</a>
                </div>
            </article>
        </div>
    </section>
</main>

<div id="animacjaTestowa1" class="test-block">kliknij, a się powiększe</div>

<script>

    $("#animacjaTestowa1").on("click", function () {
        $(this).stop().animate({
            width: "500px",
            opacity: 0.4,
            fontSize: "3em",
            borderWidth: "10px"
        }, 1500);
    });

</script>

<div id="animacjaTestowa2" class="test-block">
    Najedź kursorem, a się powiększe
</div>

<script>
    $("#animacjaTestowa2").on({
        "mouseenter": function () {
            $(this).stop().animate({
                width: 300
            }, 800);
        },
        "mouseleave": function () {
            $(this).stop().animate({
                width: 200
            }, 800);
        }
    });
</script>

<div id="animacjaTestowa3" class="test-block">
    Kliknij
</div>

<script>
    $("#animacjaTestowa3").on("click", function () {
        // Sprawdzenie: jeśli element NIE (!) jest w trakcie animowania...
        if (!$(this).is(":animated")) {
            $(this).animate({
                width: "+=" + 50,    // Zwiększ szerokość o 50px
                height: "+=" + 10,   // Zwiększ wysokość o 10px
                opacity: "-=" + 0.1, // Zmniejsz przezroczystość o 0.1
            }, 3000); // Czas trwania: 3000ms (3 sekundy)
        }
    });
</script>', 1);

-- Burj Khalifa
INSERT INTO `page_list` (`page_title`, `page_content`, `status`) VALUES
('Burj Khalifa', '<header class="page-header">
    <div class="container">
        <h1>Burj Khalifa</h1>
        <p>Dubaj, Zjednoczone Emiraty Arabskie</p>
    </div>
</header>

<main>
    <section class="building-detail container">
        <div class="detail-grid">
            <div class="detail-content">
                <h2>Ikona Nowoczesności</h2>
                <p>
                    Burj Khalifa to nie tylko najwyższy budynek na świecie, ale także globalna ikona i cud
                    inżynierii.
                    Wznoszący się na wysokość 828 metrów nad pustynią Dubaju, budynek ten stanowi serce nowej
                    dzielnicy Downtown Dubai.
                    Jego spiralna konstrukcja nawiązuje do kwiatu hymenocallis, co zapewnia stabilność przy silnych
                    wiatrach.
                </p>
                <p>
                    Budowa rozpoczęła się w 2004 roku, a oficjalne otwarcie nastąpiło w 2010 roku. Wieżowiec mieści
                    apartamenty mieszkalne,
                    biura, pierwszy na świecie hotel marki Armani oraz tarasy widokowe, z których roztacza się
                    zapierający dech w piersiach widok na Zatokę Perską.
                </p>

                <div class="detail-stats">
                    <h3>Dane Techniczne</h3>
                    <ul class="stats-list">
                        <li><span>Wysokość całkowita:</span> <strong>828 m</strong></li>
                        <li><span>Liczba pięter:</span> <strong>163</strong></li>
                        <li><span>Rok ukończenia:</span> <strong>2010</strong></li>
                        <li><span>Architekt:</span> <strong>Adrian Smith (SOM)</strong></li>
                        <li><span>Koszt budowy:</span> <strong>~1.5 mld USD</strong></li>
                    </ul>
                </div>
            </div>
            <div class="detail-image">
                <img src="images/burj_khalifa.png" alt="Burj Khalifa w słońcu">
            </div>
        </div>
    </section>

    <section class="fun-facts container">
        <h2>Ciekawostki</h2>
        <ol>
            <li>Burj Khalifa jest trzykrotnie wyższy od Wieży Eiffla.</li>
            <li>Beton użyty do budowy waży tyle, co 100 000 słoni.</li>
            <li>System kondensacji wody w budynku odzyskuje rocznie około 15 milionów litrów wody, która służy do
                nawadniania roślin wokół wieży.</li>
            <li>Winda wjeżdża na 124. piętro w zaledwie minutę, poruszając się z prędkością 10 m/s.</li>
            <li>Czubek wieży jest dostrzegalny z odległości 95 kilometrów.</li>
        </ol>
    </section>
</main>', 1);

-- Merdeka 118
INSERT INTO `page_list` (`page_title`, `page_content`, `status`) VALUES
('Merdeka 118', '<header class="page-header">
    <div class="container">
        <h1>Merdeka 118</h1>
        <p>Kuala Lumpur, Malezja</p>
    </div>
</header>

<main>
    <section class="building-detail container">
        <div class="detail-grid">
            <div class="detail-content">
                <h2>Klejnot Azji Południowo-Wschodniej</h2>
                <p>
                    Merdeka 118, znany również jako PNB 118, to drugi co do wysokości budynek na świecie, dominujący
                    nad panoramą Kuala Lumpur.
                    Jego nazwa nawiązuje do liczby pięter oraz słowa "Merdeka", co w języku malajskim oznacza
                    "Niepodległość".
                    Lokalizacja wieżowca w pobliżu Stadionu Merdeka, miejsca deklaracji niepodległości Malezji,
                    nadaje mu szczególne znaczenie historyczne.
                </p>
                <p>
                    Fasetowa, szklana fasada budynku została zainspirowana wzorami tradycyjnej malajskiej sztuki i
                    rzemiosła songket.
                    Iglica wieńcząca budynek dodaje mu smukłości i jest kluczowym elementem osiągnięcia jego
                    imponującej wysokości.
                </p>

                <div class="detail-stats">
                    <h3>Dane Techniczne</h3>
                    <ul class="stats-list">
                        <li><span>Wysokość całkowita:</span> <strong>678.9 m</strong></li>
                        <li><span>Liczba pięter:</span> <strong>118</strong></li>
                        <li><span>Rok ukończenia:</span> <strong>2023</strong></li>
                        <li><span>Architekt:</span> <strong>Fender Katsalidis</strong></li>
                        <li><span>Użytkowanie:</span> <strong>Biura, Hotel, Galerie</strong></li>
                    </ul>
                </div>
            </div>
            <div class="detail-image">
                <img src="images/merdeka_118.png" alt="Merdeka 118 w Kuala Lumpur">
            </div>
        </div>
    </section>

    <section class="fun-facts container">
        <h2>Ciekawostki</h2>
        <ol>
            <li>Jest to pierwszy budynek w południowo-wschodniej Azji, który przekroczył wysokość 600 metrów
                (megatall).</li>
            <li>Taras widokowy "The View at 118" jest najwyższym w Azji Południowo-Wschodniej.</li>
            <li>Kompleks Merdeka 118 obejmuje również centrum handlowe ze szklaną kopułą.</li>
            <li>Kształt wieży przypomina sylwetkę Tunku Abdula Rahmana z uniesioną ręką, wykrzykującego "Merdeka!" w
                1957 roku.</li>
            <li>Budynek posiada certyfikat LEED Platinum, co świadczy o jego ekologicznym charakterze.</li>
        </ol>
    </section>
</main>', 1);

-- Shanghai Tower
INSERT INTO `page_list` (`page_title`, `page_content`, `status`) VALUES
('Shanghai Tower', '<header class="page-header">
    <div class="container">
        <h1>Shanghai Tower</h1>
        <p>Szanghaj, Chiny</p>
    </div>
</header>

<main>
    <section class="building-detail container">
        <div class="detail-grid">
            <div class="detail-content">
                <h2>Skręcony Smok</h2>
                <p>
                    Shanghai Tower to najwyższy budynek w Chinach i trzeci na świecie. Jego najbardziej
                    charakterystyczną cechą jest
                    spiralna fasada, która skręca się o około 120 stopni w miarę wznoszenia się ku niebu.
                    Taki kształt nie jest tylko zabiegiem estetycznym – zmniejsza on obciążenie wiatrem o 24%, co
                    pozwoliło zaoszczędzić miliony dolarów na materiałach konstrukcyjnych.
                </p>
                <p>
                    Wieżowiec jest podzielony na dziewięć pionowych stref, z których każda posiada własne atrium
                    ("niebiański ogród").
                    Działa to jak wertykalne miasto, zapewniając mieszkańcom i pracownikom dostęp do usług bez
                    konieczności zjeżdżania na parter.
                </p>

                <div class="detail-stats">
                    <h3>Dane Techniczne</h3>
                    <ul class="stats-list">
                        <li><span>Wysokość całkowita:</span> <strong>632 m</strong></li>
                        <li><span>Liczba pięter:</span> <strong>128</strong></li>
                        <li><span>Rok ukończenia:</span> <strong>2015</strong></li>
                        <li><span>Architekt:</span> <strong>Gensler</strong></li>
                        <li><span>Windy:</span> <strong>Prędkość do 20.5 m/s</strong></li>
                    </ul>
                </div>
            </div>
            <div class="detail-image">
                <img src="images/shanghai_tower.png" alt="Shanghai Tower w chmurach">
            </div>
        </div>
    </section>

    <section class="fun-facts container">
        <h2>Ciekawostki</h2>
        <ol>
            <li>Posiada jedne z najszybszych wind na świecie.</li>
            <li>Podwójna szklana fasada działa jak termos, poprawiając efektywność energetyczną budynku.</li>
            <li>Budynek zbiera deszczówkę, która jest wykorzystywana w systemach klimatyzacji i ogrzewania.</li>
            <li>Na 118. piętrze znajduje się taras widokowy z widokiem 360 stopni na Szanghaj.</li>
            <li>Fundamenty budynku składają się z niemal 1000 pali wbitych na głębokość 86 metrów.</li>
        </ol>
    </section>
</main>', 1);

-- Abraj Al-Bait
INSERT INTO `page_list` (`page_title`, `page_content`, `status`) VALUES
('Abraj Al-Bait', '<header class="page-header">
    <div class="container">
        <h1>Abraj Al-Bait</h1>
        <p>Mekka, Arabia Saudyjska</p>
    </div>
</header>

<main>
    <section class="building-detail container">
        <div class="detail-grid">
            <div class="detail-content">
                <h2>Strażnik Czasu</h2>
                <p>
                    Abraj Al-Bait, znany również jako Makkah Royal Clock Tower, to rządowy kompleks hotelowy w
                    Mekce.
                    Znajduje się zaledwie kilkadziesiąt metrów od Wielkiego Meczetu i Kaaby, najświętszego miejsca
                    islamu.
                    Kompleks składa się z siedmiu wieżowców, z których centralna wieża zegarowa dominuje nad
                    całością.
                </p>
                <p>
                    Zegar na szczycie wieży jest największym i najwyżej położonym zegarem wieżowym na świecie.
                    Każda z czterech tarcz ma średnicę 43 metrów i jest widoczna z odległości 25 kilometrów.
                    Szczyt wieńczy złoty półksiężyc o wadze 35 ton.
                </p>

                <div class="detail-stats">
                    <h3>Dane Techniczne</h3>
                    <ul class="stats-list">
                        <li><span>Wysokość całkowita:</span> <strong>601 m</strong></li>
                        <li><span>Liczba pięter:</span> <strong>120</strong></li>
                        <li><span>Rok ukończenia:</span> <strong>2012</strong></li>
                        <li><span>Funkcja:</span> <strong>Hotel, Centrum Handlowe, Muzeum</strong></li>
                        <li><span>Powierzchnia:</span> <strong>1.5 mln m²</strong></li>
                    </ul>
                </div>
            </div>
            <div class="detail-image">
                <img src="images/abraj_al_bait.png" alt="Abraj Al-Bait w Mekce">
            </div>
        </div>
    </section>

    <section class="fun-facts container">
        <h2>Ciekawostki</h2>
        <ol>
            <li>Jest to najdroższy budynek na świecie, koszt budowy wyniósł około 15 miliardów dolarów.</li>
            <li>Posiada największą powierzchnię użytkową ze wszystkich budynków na świecie.</li>
            <li>Zegar jest 35 razy większy od zegara Big Ben w Londynie.</li>
            <li>Wewnątrz półksiężyca na szczycie znajduje się najwyżej położony pokój modlitewny na świecie.</li>
            <li>Kompleks może pomieścić do 100 000 gości jednocześnie.</li>
        </ol>
    </section>
</main>', 1);

-- Ping An Finance Center
INSERT INTO `page_list` (`page_title`, `page_content`, `status`) VALUES
('Ping An Finance Center', '<header class="page-header">
    <div class="container">
        <h1>Ping An Finance Center</h1>
        <p>Shenzhen, Chiny</p>
    </div>
</header>

<main>
    <section class="building-detail container">
        <div class="detail-grid">
            <div class="detail-content">
                <h2>Stalowy Gigant</h2>
                <p>
                    Ping An Finance Center to symbol szybkiego rozwoju Shenzhen – od wioski rybackiej do
                    technologicznej potęgi.
                    Budynek charakteryzuje się smukłą, stożkową formą, która została zaprojektowana tak, aby
                    minimalizować napór wiatru.
                    Jego fasada wykonana jest z największej na świecie ilości stali nierdzewnej użytej w jednym
                    budynku (ok. 1 700 ton), co sprawia, że jest odporna na korozję w słonym, nadmorskim klimacie.
                </p>
                <p>
                    Początkowo planowano, że budynek będzie miał 660 metrów i iglicę, jednak ze względu na
                    ograniczenia lotnicze, iglica została usunięta, ustalając ostateczną wysokość na 599 metrów.
                </p>

                <div class="detail-stats">
                    <h3>Dane Techniczne</h3>
                    <ul class="stats-list">
                        <li><span>Wysokość całkowita:</span> <strong>599.1 m</strong></li>
                        <li><span>Liczba pięter:</span> <strong>115</strong></li>
                        <li><span>Rok ukończenia:</span> <strong>2017</strong></li>
                        <li><span>Architekt:</span> <strong>Kohn Pedersen Fox (KPF)</strong></li>
                        <li><span>Konstrukcja:</span> <strong>Kompozyt (Beton/Stal)</strong></li>
                    </ul>
                </div>
            </div>
            <div class="detail-image">
                <img src="images/ping_an.png" alt="Ping An Finance Center">
            </div>
        </div>
    </section>

    <section class="fun-facts container">
        <h2>Ciekawostki</h2>
        <ol>
            <li>Na 116. piętrze znajduje się taras widokowy Free Sky 116.</li>
            <li>Kształt budynku został przetestowany w tunelu aerodynamicznym, co pozwoliło zmniejszyć obciążenie
                wiatrem o 35%.</li>
            <li>Winda w budynku jest jedną z niewielu na świecie, która wykorzystuje technologię podwójnej kabiny.
            </li>
            <li>Fasada ze stali nierdzewnej ma powierzchnię równą 14 boiskom do piłki nożnej.</li>
            <li>Budynek posiada 33 windy double-decker (dwupoziomowe).</li>
        </ol>
    </section>
</main>', 1);

-- Filmy
INSERT INTO `page_list` (`page_title`, `page_content`, `status`) VALUES
('Filmy', '<div class="fade-in">
    <h2>Filmy o największych budynkach</h2>
    <div style="display: flex; flex-wrap: wrap; gap: 20px; justify-content: center;">
        <div class="video-container">
            <iframe width="560" height="315" src="https://www.youtube.com/embed/r9omqwqHNiE?si=wJxRN7k2txwYAT2H"
                title="YouTube video player" frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
        </div>
        <div class="video-container">
            <iframe width="560" height="315" src="https://www.youtube.com/embed/jUEhFgZqNWE?si=ftVNE4VkAFx-kA7j"
                title="YouTube video player" frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
        </div>
        <div class="video-container">
            <iframe width="560" height="315" src="https://www.youtube.com/embed/CuK3SAzCrAA?si=3AAseyTqYNUZOwSl"
                title="YouTube video player" frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
        </div>
    </div>
</div>', 1);
