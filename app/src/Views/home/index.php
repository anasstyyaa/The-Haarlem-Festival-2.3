<?php use App\ViewModels\PageElementViewModel;
/** @var PageElementViewModel $vm */ ?>
<?php require __DIR__ . '/../partials/header.php'; ?>
<style>


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
    text-align: center;
}

.location-grid {
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

</style>
 <?php foreach ($vm->getSections() as $section => $elements): ?>

    <?php
        $viewFile = __DIR__ . "/sections/section{$section}.php";

        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            require __DIR__ . "/sections/default.php";
        }
    ?>

<?php endforeach; ?>


<?php require __DIR__ . '/../partials/footer.php'; ?> 