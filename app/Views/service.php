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
    <title>Nos services</title>
    <link rel="stylesheet" href="../assets/css/style.css"> <!-- Chemin vers votre fichier CSS -->
</head>
<body>

    <?php include 'header.php'; ?>

    <main>
    <h2>Services Proposés par le Dr. Dupont</h2>

        <p>Le Dr. Dupont offre une large gamme de services dentaires pour répondre à vos besoins.</p>
        <ul>
            <?php foreach ($services as $index => $service): ?>
                <li class="service-item <?php echo $index % 2 === 0 ? 'even' : 'odd'; ?>">
                    <!-- Conteneur pour l'image -->
                    <?php if (!empty($service['image'])): ?>
                        <div class="service-image">
                            <img src="<?php echo htmlspecialchars($service['image']); ?>" alt="Image de <?php echo htmlspecialchars($service['name']); ?>">
                        </div>
                    <?php endif; ?>

                    <!-- Conteneur pour le titre et la description -->
                    <div class="service-details">
                        <h3><?php echo htmlspecialchars($service['name']); ?></h3>
                        <p><?php echo htmlspecialchars($service['description']); ?></p>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>

        <p>Pour prendre rendez-vous pour l'un de ces services, veuillez vous rendre sur la page 
            <a href="index.php?page=appointments" class="cta-button">Rendez-vous</a>.
        </p>

    </main>

    <footer>
        <p>&copy; 2024 Cabinet du Dr. Dupont. Tous droits réservés.</p>
    </footer>
</body>
</html>