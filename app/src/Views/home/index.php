<?php use App\ViewModels\PageElementViewModel;
/** @var PageElementViewModel $vm */ ?>
<?php require __DIR__ . '/../partials/header.php'; ?>
<style>
 /* RESET */
/* body {
  margin: 0;
  font-family: "Segoe UI", sans-serif;
} */

/* ========== SECTION 1 HERO ========== */
/* HERO IMAGE */
.section4 {
  position: relative;
  height: 75vh;
  display: flex;
  flex-direction: column;
  justify-content: center;
  padding: 0 8%;
  color: white;
}

.section4 img {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  z-index: -1; /* behind text */
}

/* overlay */
.section4::after {
  content: "";
  position: absolute;
  inset: 0;
  background: linear-gradient(to right, rgba(60, 15, 0, 0.7), rgba(60, 15, 0, 0.2));
  z-index: 0;
}

/* Hero text & buttons */
.section4 h1, .section1 p, .section1 a {
  position: relative;
  z-index: 1; /* on top of image */
}

.section4 h1 { font-size: 4rem; margin-bottom: 10px; }
.section4 p { max-width: 600px; margin-bottom: 20px; }

.section4 a:first-of-type {
  background: #f7c9bc;
  color: #4b1608;
}
.section4 a:last-of-type {
  border: 1px solid #fff;
  color: #fff;
}

/* ========== SECTION 2 ABOUT ========== */
.section5 {
  background: #4b1608;
  color: white;
  padding: 80px 8%;
  text-align: left;
}

.section5 h3 {
  display: inline-block;
  width: 120px;
  margin: 20px 20px 0 0;
  font-size: 28px;
  color: #ff8a2a;
}


/* ========== SECTION 3 DATES ========== */
.section6 {
  background: #5c1a08;
  color: white;
  text-align: center;
  padding: 80px 20px;
}

.section6 p {
  display: inline-flex;
  justify-content: center;
  align-items: center;

  width: 140px;
  height: 140px;
  margin: 15px;

  border-radius: 50%;
  background: rgba(255, 140, 60, 0.3);

  font-size: 1.5rem;
  font-weight: bold;
}
.locations-flat {
    background: #4b1608;
    color: white;
    padding: 80px 20px;

    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 30px;
}

/* Title */
.locations-title {
    width: 100%;
    text-align: center;
    font-size: 2.2rem;
    margin-bottom: 10px;
}

/* Intro */
.locations-intro {
    width: 100%;
    max-width: 700px;
    text-align: center;
    margin-bottom: 30px;
    line-height: 1.6;
}

/* Cards */
.location-item {
    width: 180px;
    text-align: center;
}

/* Images */
.location-item img {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    object-fit: cover;
    margin-bottom: 10px;
}

/* Titles */
.location-item h3 {
    margin: 6px 0;
    font-size: 1.2rem;
}

/* Text */
.location-item p {
    font-size: 0.9rem;
    line-height: 1.4;
}

/* Responsive */
@media (max-width: 600px) {
    .location-item {
        width: 140px;
    }

    .location-item img {
        width: 120px;
        height: 120px;
    }
}

/* ========== SECTION 4 LOCATIONS ========== */
/* .section4 {
  background: #4b1608;
  color: white;
  padding: 80px 20px;
  text-align: center;
}

/* fake cards */
/* .section4 img {
  width: 140px;
  height: 140px;
  border-radius: 50%;
  object-fit: cover;
  display: block;
  margin: 40px auto 10px;
} */

/* .section4 p {
  max-width: 250px;
  margin: 0 auto 20px;
  font-size: 0.9rem;
}  */


/* ========== SECTION 5 EVENTS ========== */
/* .section5 {
  background: #5c1a08;
  color: white;
  padding: 80px 8%;
} */

/* simulate alternating layout */
/* .section5 img {
  width: 200px;
  height: 200px;
  border-radius: 50%;
  object-fit: cover;
  margin: 30px 0;
} */

/* text spacing */
/* .section5 h3 {
  font-size: 2rem;
  margin-top: 10px;
} */

/* .section5 p {
  max-width: 600px;
} */

/* button */
/* .section5 a {
  display: inline-block;
  margin-top: 10px;
  padding: 10px 18px;
  border-radius: 999px;
  background: #f7c9bc;
  color: #4b1608;
  text-decoration: none;
} */
</style>
 <?php foreach ($vm->getSections() as $section => $elements): 
    if($section==7){
    ?>
    <div class="locations-flat"> 
      <div class="section<?= htmlspecialchars($section) ?>">
        
        <?php foreach ($elements as $element): ?>
            <?= $element->render(); ?>
        <?php endforeach; ?>
 </div>
    </div>
    <?php } ?>

    
    <div class="section<?= htmlspecialchars($section) ?>">
        
        <?php foreach ($elements as $element): ?>
            <?= $element->render(); ?>
        <?php endforeach; ?>

    </div>

<?php endforeach; ?>
<div class="locations-flat">

    <h2 class="locations-title">Event’s Main Locations</h2>


    <div class="location-item">
        <img src="/assets/images/home/Jazz1.png">
        <h3>Jazz</h3>
        <p>Haarlem Jazz is centered at Patronaat, with additional live performances around Grote Markt.</p>
    </div>

    <div class="location-item">
        <img src="/assets/images/home/Yummy1.png">
        <h3>Yummy</h3>
        <p>Yummy is hosted by selected restaurants in the city center.</p>
    </div>

    <div class="location-item">
        <img src="/assets/images/home/Dance1.png">
        <h3>Dance</h3>
        <p>Dance events are spread across multiple venues.</p>
    </div>

    <div class="location-item">
        <img src="/assets/images/home/History1.png">
        <h3>History</h3>
        <p>Tours start near iconic landmarks across Haarlem.</p>
    </div>

    <div class="location-item">
        <img src="/assets/images/home/Tyler1.png">
        <h3>Kids</h3>
        <p>Activities take place in museums and family spaces.</p>
    </div>

</div>
<!-- <div class="section1">
    <img src="/assets/images/home/FirstPic.png">
    <h1>HAARLEM FESTIVAL</h1>
    <p>Discover music, food, culture, history, and family fun.</p>
    <a href="#">Explore events</a>
    <a href="#">Personal Program</a>
</div> -->

<!-- <div class="section5">
    <h2>About Haarlem Festival</h2>
    <p>  Welcome to Haarlem’s most vibrant celebration of culture, music, food, and community.
                    Enjoy soul jazz in historic streets, flavors of Haarlem’s best spots, and local history
                    during guided tours. With family-friendly museum activities and a lively program for all ages,
                    the city comes alive with creativity and discovery all week long.</p>

    <h3>1 festival</h3>
    <h3>4 days</h3>
    <h3>50+ events</h3>
    <h3>100+ memories</h3>
</div> -->
<!-- 
<div class="section6">
    <h2>Festival Dates</h2>
    <p>23 July</p>
    <p>24 July</p>
    <p>25 July</p>
    <p>26 July</p>
</div> -->

<div class="section4">
    <h2>Locations</h2>

    <img src="/assets/images/home/Jazz1.png">
    <h3>Jazz</h3>
    <p>Centered at Patronaat.</p>

    <img src="/assets/images/home/Yummy1.png">
    <h3>Yummy</h3>
    <p>Restaurants in city center.</p>
</div>

<div class="section5">
    <h2>Events</h2>

    <img src="/assets/images/home/Jazz2.png">
    <h3>Jazz</h3>
    <p>Enjoy soulful jazz.</p>

    <img src="/assets/images/home/Dance2.png">
    <h3>Dance</h3>
    <p>Dynamic performances.</p>
</div>
<!-- 
<section class="home-hero">
    <img src="/assets/images/home/FirstPic.png" alt="Haarlem Festival" class="home-hero-image">
    <div class="home-hero-overlay">
        <div class="home-hero-content">
            <h1>HAARLEM FESTIVAL</h1>
            <p>
                Discover music, food, culture, history, and family fun across the city of Haarlem.
            </p>

            <div class="home-hero-buttons">
                <a href="#festival-events" class="home-btn primary">Explore events</a>
                <a href="/personalProgram" class="home-btn secondary">Personal Program</a>
            </div>
        </div>
    </div>
</section>-->

<section class="home-about">
    <div class="home-container">
        <div class="home-about-grid">
            <div class="home-about-text">
                <h2>About Haarlem Festival</h2>
                <p>
                    Welcome to Haarlem’s most vibrant celebration of culture, music, food, and community.
                    Enjoy soul jazz in historic streets, flavors of Haarlem’s best spots, and local history
                    during guided tours. With family-friendly museum activities and a lively program for all ages,
                    the city comes alive with creativity and discovery all week long.
                </p>
            </div>

            <div class="home-about-stats">
                <div class="home-stat">
                    <span class="home-stat-number">1</span>
                    <span class="home-stat-label">festival</span>
                </div>
                <div class="home-stat">
                    <span class="home-stat-number">4</span>
                    <span class="home-stat-label">days</span>
                </div>
                <div class="home-stat">
                    <span class="home-stat-number">50+</span>
                    <span class="home-stat-label">events</span>
                </div>
                <div class="home-stat">
                    <span class="home-stat-number">100+</span>
                    <span class="home-stat-label">memories</span>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="home-dates">
    <div class="home-container">
        <h2>Festival Dates</h2>
        <div class="home-date-circles">
            <div class="date-circle">23 July</div>
            <div class="date-circle">24 July</div>
            <div class="date-circle">25 July</div>
            <div class="date-circle">26 July</div>
        </div>
    </div>
</section>

<section class="home-locations">
    <div class="home-container">
        <h2>Event’s Main Locations</h2>
        <p class="home-section-intro">
            The festival takes place across several locations in Haarlem, with each event hosted at a venue
            that matches its character.
        </p>

        <div class="location-grid">
            <div class="location-card">
                <img src="/assets/images/home/Jazz1.png" alt="Jazz location">
                <h3>Jazz</h3>
                <p>Haarlem Jazz is centered at Patronaat, with additional live performances around Grote Markt.</p>
            </div>

            <div class="location-card">
                <img src="/assets/images/home/Yummy1.png" alt="Yummy location">
                <h3>Yummy</h3>
                <p>Yummy is hosted by selected participating restaurants located mainly in and around the city center.</p>
            </div>

            <div class="location-card">
                <img src="/assets/images/home/Dance1.png" alt="Dance location">
                <h3>Dance</h3>
                <p>Dance events are spread across multiple venues, including performance spaces and city stages.</p>
            </div>

            <div class="location-card">
                <img src="/assets/images/home/History1.png" alt="History location">
                <h3>History</h3>
                <p>History tours begin near iconic city landmarks and guide visitors through Haarlem’s rich past.</p>
            </div>

            <div class="location-card">
                <img src="/assets/images/home/Tyler1.png" alt="Kids location">
                <h3>Kids</h3>
                <p>Kids activities take place in museums and family-friendly cultural spaces around Haarlem.</p>
            </div>
        </div>
    </div>
</section>

<section class="home-events" id="festival-events">
    <div class="home-container">
        <h2>Festival Events</h2>

        <div class="event-feature-list">
            <div class="event-feature-card">
                <img src="/assets/images/home/Jazz2.png" alt="Jazz">
                <div class="event-feature-content">
                    <h3>Jazz</h3>
                    <p>
                        Enjoy soulful jazz moments during the Haarlem Festival, where music flows through historic
                        locations and sets the perfect mood.
                    </p>
                    <a href="/jazz" class="home-btn primary">Explore Jazz events</a>
                </div>
            </div>

            <div class="event-feature-card reverse">
                <img src="/assets/images/home/Dance2.png" alt="Dance">
                <div class="event-feature-content">
                    <h3>Dance</h3>
                    <p>
                        Move to the beat during the Haarlem Festival, where dance takes over the city with dynamic
                        performances and contagious energy.
                    </p>
                    <a href="/dance" class="home-btn primary">Explore Dance events</a>
                </div>
            </div>

            <div class="event-feature-card">
                <img src="/assets/images/home/Yummy2.png" alt="Yummy">
                <div class="event-feature-content">
                    <h3>Yummy</h3>
                    <p>
                        Discover Haarlem’s best restaurants during the festival as local chefs serve up their most
                        delicious dishes and signature flavors.
                    </p>
                    <a href="/yummy" class="home-btn primary">Explore Yummy events</a>
                </div>
            </div>

            <div class="event-feature-card reverse">
                <img src="/assets/images/home/History2.png" alt="History">
                <div class="event-feature-content">
                    <h3>History</h3>
                    <p>
                        Step back in time at the festival and uncover Haarlem’s vibrant past through stories, tours,
                        and hidden gems.
                    </p>
                    <a href="/history" class="home-btn primary">Explore History events</a>
                </div>
            </div>

            <div class="event-feature-card">
                <img src="/assets/images/home/Tyler2.png" alt="Kids">
                <div class="event-feature-content">
                    <h3>Kids</h3>
                    <p>
                        Kids can discover Haarlem’s museums with exciting, interactive experiences made for young
                        explorers.
                    </p>
                    <a href="/kidsEvent" class="home-btn primary">Explore Kids events</a>
                </div>
            </div>
        </div>
    </div>
</section>


<?php require __DIR__ . '/../partials/footer.php'; ?> 