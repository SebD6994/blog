<?php 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérification si l'utilisateur est connecté et son rôle
if (isset($_SESSION['patient']['role'])) {
    if ($_SESSION['patient']['role'] === 'admin') {
        // Redirection vers la page admin
        header("Location: index.php?page=admin_services");
        exit();
    } elseif ($_SESSION['patient']['role'] === 'user') {
        // Redirection vers la page utilisateur
        header("Location: index.php?page=services");
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
                <li class="service-item <?php echo $index % 2 === 0 ? 'service-even' : 'service-odd'; ?>">
                    <!-- Conteneur pour l'image -->
                    <div class="service-image">
                        <img src="<?php echo htmlspecialchars($service['image']); ?>" alt="Image de <?php echo htmlspecialchars($service['name']); ?>" class="service-image-img">
                    </div>
                    
                    <!-- Conteneur pour le titre et la description -->
                    <div class="service-details">
                        <h3><?php echo htmlspecialchars($service['name']); ?></h3>
                        <p><?php echo htmlspecialchars($service['description']); ?></p>
                    </div>
                </li>
            <?php endforeach; ?>

            <p>Pour prendre rendez-vous pour l'un de ces services, veuillez vous rendre sur la page 
                <a href="index.php?page=appointments" class="cta-button">Rendez-vous</a>.
            </p>
        </main>

    <?php include 'footer.php'; ?>
    
</body>
</html>