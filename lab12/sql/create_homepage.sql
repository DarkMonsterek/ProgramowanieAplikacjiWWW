INSERT INTO page_list (page_title, page_content, status) VALUES ('Strona Główna', '<main>
    <section class="hero">
        <div class="hero-content">
            <h1>Sięgając Nieba</h1>
            <p>Odkryj geniusz inżynierii i architektoniczne cuda, które definiują współczesny świat.</p>
            <a href="#ranking" id="ranking-btn" class="btn btn-primary">Zobacz Ranking</a>
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
    
    // Animacja przycisku Ranking
    $("#ranking-btn").on({
        "mouseenter": function () {
            $(this).stop().animate({
                fontSize: "1.2em",
                paddingLeft: "30px",
                paddingRight: "30px"
            }, 200);
        },
        "mouseleave": function () {
            $(this).stop().animate({
                fontSize: "1em",
                paddingLeft: "20px",
                paddingRight: "20px"
            }, 200);
        }
    });
</script>', 1);
