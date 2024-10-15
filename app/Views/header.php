<header>
    <div class="header-container">
        <h2>Bienvenue au Cabinet du Dr. Dupont</h2>
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
    </div>
</header>