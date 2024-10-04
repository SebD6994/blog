<?php 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Rendez-vous</title>
    <link rel="stylesheet" href="../assets/style.css"> <!-- Chemin vers votre fichier CSS -->
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
                    <li>
                        <a href="index.php?page=patients&action=logout">Se déconnecter</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main>
        <h2>Ajouter un Rendez-vous</h2>
        <form action="index.php?page=appointments" method="post">
            <input type="hidden" name="action" value="add_appointment">
    
            <!-- Champ masqué pour l'ID du patient -->
            <input type="hidden" name="patient_id" value="<?php echo htmlspecialchars($patientData['id'] ?? ''); ?>">
    
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
    </main>

    <footer>
        <p>&copy; 2024 Cabinet du Dr. Dupont. Tous droits réservés.</p>
    </footer>
</body>
</html>