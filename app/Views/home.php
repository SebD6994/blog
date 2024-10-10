<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <header>
        <h1>Bienvenue au Cabinet du Dr. Dupont</h1>
        <nav>
            <ul>
                <li><a href="index.php?page=home">Accueil</a></li>
                <li><a href="index.php?page=patients">Patients</a></li>
                <li><a href="index.php?page=appointments">Rendez-vous</a></li>
                <li><a href="index.php?page=services">Services</a></li>
                <li><a href="index.php?page=news">Actualités</a></li>
                <?php if (isset($_SESSION['patient'])): ?>
                    <li>
                        <a href="index.php?page=patients&action=logout">Se déconnecter</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main>
        <h2>À propos du Cabinet</h2>
        <p>Le Cabinet du Dr. Dupont propose divers services adaptés à vos besoins de santé. Nous nous engageons à vous fournir des soins de qualité dans un environnement convivial et professionnel.</p>
        
        <h3>Horaires d'ouverture</h3>
        <ul>
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

                // Créer un tableau pour stocker les horaires d'ouverture triés
                $openingHoursSorted = [];
                foreach ($openingHours as $hour) {
                    $openingHoursSorted[$hour['day_of_week']] = $hour;
                }

                // Ordre des jours avec Lundi en premier
                $orderedDays = [1, 2, 3, 4, 5, 6, 0]; 

                foreach ($orderedDays as $day): ?>
                    <li>
                        <?php echo htmlspecialchars($daysOfWeek[$day]); ?> : 
                        <?php 
                        // Vérification des horaires d'ouverture
                        if (isset($openingHoursSorted[$day]) && 
                            $openingHoursSorted[$day]['start_time'] !== '00:00:00' && 
                            $openingHoursSorted[$day]['end_time'] !== '00:00:00'): 
                            // Formatage des heures pour enlever les secondes
                            $startTime = date('H:i', strtotime($openingHoursSorted[$day]['start_time']));
                            $endTime = date('H:i', strtotime($openingHoursSorted[$day]['end_time']));
                            echo htmlspecialchars($startTime) . ' - ' . htmlspecialchars($endTime);
                        else: ?>
                            Fermé
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
        </ul>


        <h3>Nos Services</h3>
        <ul>
            <?php if (!empty($services)): ?>
                <?php foreach ($services as $service): ?>
                    <li><?php echo htmlspecialchars($service['name']); ?></li>
                <?php endforeach; ?>
            <?php else: ?>
                <li>Aucun service disponible pour le moment.</li>
            <?php endif; ?>
        </ul>

        <h3>Visitez notre clinique</h3>
        <p>Voici quelques images de notre clinique :</p>
        <img src="../assets/clinic_image.jpg" alt="Clinique du Dr. Dupont" width="600">

        <p>
            <a href="index.php?page=appointments" class="cta-button">Prendre rendez-vous</a>
        </p>
    </main>

    <footer>
        <p>&copy; 2024 Cabinet du Dr. Dupont. Tous droits réservés.</p>
    </footer>
</body>
</html>