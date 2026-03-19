<?php require __DIR__ . '/../partials/header.php'; ?>

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
</section>

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
