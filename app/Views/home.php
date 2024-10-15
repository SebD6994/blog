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
        <h2>À propos du Cabinet</h2>
        <p>Le Cabinet du Dr. Dupont propose divers services adaptés à vos besoins de santé. Nous nous engageons à vous fournir des soins de qualité dans un environnement convivial et professionnel.</p>

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
        <img src="../assets/clinic_image.jpg" alt="Clinique du Dr. Dupont" class="clinic-image" width="600">
        
        <p>
            <a href="index.php?page=appointments" class="cta-button">Prendre rendez-vous</a>
        </p>
    </main>

    <?php include 'footer.php'; ?>

</body>
</html>
