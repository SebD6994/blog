<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['success'])): ?>
    <div class="success-message">
        <?= htmlspecialchars($_SESSION['success']); ?>
        <?php unset($_SESSION['success']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="error-message">
        <?= htmlspecialchars($_SESSION['error']); ?>
        <?php unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<?php 
if (isset($_SESSION['patient']['role'])) {
    if ($_SESSION['patient']['role'] === 'admin') {
        header("Location: index.php?page=admin_services");
        exit();
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
            <h2>Services proposés par le cabinet</h2>            
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