<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Rendez-vous</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <header>
        <h1>Liste des Rendez-vous</h1>
        <nav>
            <ul>
                <li><a href="index.php?page=home">Accueil</a></li>
                <li><a href="index.php?page=patients">Patients</a></li>
                <li><a href="index.php?page=appointments">Rendez-vous</a></li>
                <li><a href="index.php?page=services">Services</a></li>
                <li><a href="index.php?page=news">Actualités</a></li>
                <?php if (isset($_SESSION['patient'])): ?>
                    <li><a href="index.php?page=patients&action=logout">Se déconnecter</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main>
        <?php if ($patientData): ?>
            <h2>Mes Rendez-vous</h2>
            <ul>
                <?php if (!empty($appointments)): ?>
                    <?php foreach ($appointments as $appointment): ?>
                        <?php 
                            // Extraction de la date et de l'heure
                            $dateTime = new DateTime($appointment['appointment_date']); // Crée un objet DateTime
                            $date = $dateTime->format('d/m/Y'); // Formate la date
                            $heure = $dateTime->format('H:i'); // Formate l'heure
                        ?>
                        <li>
                            <strong>Date :</strong> <?= htmlspecialchars($date); ?> - 
                            <strong>Heure :</strong> <?= htmlspecialchars($heure); ?> - 
                            <strong>Service :</strong> <?= htmlspecialchars($appointment['service_name'] ?? 'Service non spécifié'); ?>

                            <!-- Bouton pour rediriger vers la page appointments -->
                            <a href="index.php?page=appointments" class="button">Modifier ce rendez-vous</a>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li>Aucun rendez-vous trouvé.</li>
                <?php endif; ?>
            </ul>
            
            <h2>Ajouter un Rendez-vous</h2>
            <form action="index.php?page=appointments&action=create" method="post">
                <input type="hidden" name="patient_id" value="<?php echo htmlspecialchars($patientData['id']); ?>">
                
                <label for="appointment_date">Date :</label>
                <input type="date" name="appointment_date" required>

                <label for="appointment_time">Heure :</label>
                <input type="time" name="appointment_time" required>

                <label for="service_id">Service :</label>
                <select name="service_id" required>
                    <?php foreach ($services as $service): ?>
                        <option value="<?php echo htmlspecialchars($service['id']); ?>">
                            <?php echo htmlspecialchars($service['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <button type="submit">Ajouter</button>
            </form>

        <?php else: ?>
            <p>Connectez-vous pour prendre rendez-vous.</p>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; 2024 Cabinet du Dr. Dupont. Tous droits réservés.</p>
    </footer>
</body>
</html>