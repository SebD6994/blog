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
    <title>Accueil</title>
    <link rel="stylesheet" href="../assets/style.css"> <!-- Chemin vers votre fichier CSS -->
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
        <p>Lundi - Vendredi : 9h - 17h</p>

        <h3>Nos Services</h3>
        <ul>
            <li>Consultations générales</li>
            <li>Suivi médical personnalisé</li>
            <li>Examen de santé</li>
            <li>Vaccinations</li>
        </ul>

        <h3>Visitez notre clinique</h3>
        <p>Voici quelques images de notre clinique :</p>
        <img src="../assets/clinic_image.jpg" alt="Clinique du Dr. Dupont" width="600"> <!-- Remplacez par l'image appropriée -->

        <p>
            <a href="index.php?page=appointments" class="cta-button">Prendre rendez-vous</a>
        </p>
    </main>

    <footer>
        <p>© 2024 Cabinet du Dr. Dupont. Tous droits réservés.</p>
    </footer>
</body>
</html>
