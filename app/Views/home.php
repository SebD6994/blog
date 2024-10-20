<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérification si l'utilisateur est connecté et son rôle
if (isset($_SESSION['patient']['role'])) {
    if ($_SESSION['patient']['role'] === 'admin') {
        // Redirection vers la page admin
        header("Location: index.php?page=admin_home");
        exit();
    } elseif ($_SESSION['patient']['role'] === 'user') {
        // Redirection vers la page utilisateur
        header("Location: index.php?page=home");
        exit();
    } else {
        echo "Accès non autorisé.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

    <?php include 'header.php'; ?>

    <main class="home">

    <!-- Bannière -->
    <?php if (isset($bannerImagePath)): ?>
        <div class="banner">
            <img src="<?php echo htmlspecialchars($bannerImagePath); ?>" alt="Bannière de la clinique" class="banner-image">
        </div>
    <?php else: ?>
        <p>Aucune bannière disponible.</p>
    <?php endif; ?>


    <h2>À propos du Cabinet</h2>
        <p>
            <?php 
            if (isset($currentApropos['description'])) {
                echo htmlspecialchars($currentApropos['description']);
            } else {
                echo "Aucune description disponible.";
            }
            ?>
        </p>

        <div class="sections-container">
            <!-- Horaires Section -->
            <section class="horaires">
                <h3>Horaires d'ouverture</h3>
                <ul class="horaires-list">
                    <?php 
                        $daysOfWeek = [
                            1 => 'Lundi',
                            2 => 'Mardi',
                            3 => 'Mercredi',
                            4 => 'Jeudi',
                            5 => 'Vendredi',
                            6 => 'Samedi',
                            0 => 'Dimanche'
                        ];

                        $openingHoursSorted = [];
                        foreach ($openingHours as $hour) {
                            $openingHoursSorted[$hour['day_of_week']] = $hour;
                        }

                        $orderedDays = [1, 2, 3, 4, 5, 6, 0]; 

                        foreach ($orderedDays as $day): ?>
                            <li class="horaires-item">
                                <div class="day"><?php echo htmlspecialchars($daysOfWeek[$day]); ?></div>
                                <div class="time">
                                    <?php 
                                    if (isset($openingHoursSorted[$day]) && 
                                        $openingHoursSorted[$day]['start_time'] !== '00:00:00' && 
                                        $openingHoursSorted[$day]['end_time'] !== '00:00:00'): 
                                        $startTime = date('H:i', strtotime($openingHoursSorted[$day]['start_time']));
                                        $endTime = date('H:i', strtotime($openingHoursSorted[$day]['end_time']));
                                        echo htmlspecialchars($startTime) . ' - ' . htmlspecialchars($endTime);
                                    else: ?>
                                        Fermé
                                    <?php endif; ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                </ul>
            </section>

            <!-- Services Section -->
            <section class="services">
                <h3>Nos Services</h3>
                <ul class="services-list">
                    <?php if (!empty($services)): ?>
                        <?php foreach ($services as $service): ?>
                            <li><?php echo htmlspecialchars($service['name']); ?></li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li>Aucun service disponible pour le moment.</li>
                    <?php endif; ?>
                </ul>
            </section>
        </div>

        <h3>Visitez notre clinique</h3>

        <div id="clinicCarousel" class="carousel">
            <div class="carousel-inner">
                <?php if (!empty($clinicImages)): ?>
                    <?php foreach ($clinicImages as $index => $image): ?>
                        <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                            <img src="<?php echo htmlspecialchars($image['image_path']); ?>" 
                                alt="<?php echo htmlspecialchars($image['description']); ?>">
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="carousel-item active">
                        <p>Aucune image disponible pour le moment.</p>
                    </div>
                <?php endif; ?>
            </div>
            <button class="carousel-control prev" onclick="moveSlide(-1)">&#10094;</button>
            <button class="carousel-control next" onclick="moveSlide(1)">&#10095;</button>
            <div class="carousel-indicators">
                <?php if (!empty($clinicImages)): ?>
                    <?php foreach ($clinicImages as $index => $image): ?>
                        <span class="indicator <?php echo $index === 0 ? 'active' : ''; ?>" 
                            onclick="currentSlide(<?php echo $index; ?>)"></span>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>



        <p>
            <a href="index.php?page=appointments" class="cta-button">Prendre rendez-vous</a>
        </p>
    </main>

    <?php include 'footer.php'; ?>

    <script src="../assets/js/home.js"></script>

</body>
</html>