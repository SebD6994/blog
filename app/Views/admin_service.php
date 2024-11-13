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
if (!isset($_SESSION['patient']['role']) || $_SESSION['patient']['role'] !== 'admin') {
    header("Location: index.php?page=patients");
    exit();
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
        <section class="admin-page">
            <h2>Services</h2>
            <div class="sections-container">
                <table>
                    <thead>
                        <tr>
                            <th>Nom du Service</th>
                            <th>Description</th>
                            <th>Image</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($services)): ?>
                            <?php foreach ($services as $service): ?>
                                <tr id="row-<?= $service['id']; ?>">
                                    <td><h3><?= htmlspecialchars($service['name']); ?></h3></td>
                                    <td><?= htmlspecialchars($service['description']); ?></td>
                                    <td>
                                        <?php if (!empty($service['image'])): ?>
                                            <div class="service-image">
                                                <img src="<?= htmlspecialchars($service['image']); ?>" alt="Image du service">
                                            </div>
                                        <?php else: ?>
                                            <p>Aucune image</p>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <button type="button" class="button" onclick="showEditForm('service', <?= $service['id']; ?>)">Modifier</button>
                                        <form action="index.php?page=admin_services&action=delete" method="post" style="display:inline;" onsubmit="return confirmDelete('Êtes-vous sûr de vouloir supprimer ce service ?');">
                                            <input type="hidden" name="id" value="<?= $service['id']; ?>">
                                            <button type="submit" class="delete-button">Supprimer</button>
                                        </form>
                                    </td>
                                </tr>

                                <!-- Formulaire de modification -->
                                <tr id="edit-form-service-<?= $service['id']; ?>" class="edit-form" style="display:none;">
                                    <td colspan="4">
                                        <form action="index.php?page=admin_services&action=update" method="post" enctype="multipart/form-data">
                                            <input type="hidden" name="id" value="<?= $service['id']; ?>">
                                            <input type="text" name="name" value="<?= htmlspecialchars($service['name']); ?>" class="form-input" required>
                                            <textarea name="description" class="form-textarea" required><?= htmlspecialchars($service['description']); ?></textarea>
                                            <input type="file" name="image" class="form-input">
                                            <?php if (!empty($service['image'])): ?>
                                                <div class="service-image">
                                                    <img src="<?= htmlspecialchars($service['image']); ?>" alt="Image du service">
                                                </div>
                                                <input type="hidden" name="existing_image" value="<?= htmlspecialchars($service['image']); ?>">
                                            <?php endif; ?>
                                            <button type="submit" class="button">Enregistrer</button>
                                            <button type="button" class="button" onclick="hideEditForm('service', <?= $service['id']; ?>)">Annuler</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4">Aucun service trouvé.</td></tr>
                        <?php endif; ?>
                        
                        <!-- Formulaire pour ajouter un nouveau service -->
                        <tr id="create-service-row">
                            <td colspan="4">
                                <form action="index.php?page=admin_services&action=create" method="POST" enctype="multipart/form-data" class="edit-form">
                                    <input type="hidden" name="id" value="">
                                    <input type="text" name="name" placeholder="Nom du Service" class="form-input" required>
                                    <textarea name="description" placeholder="Description" class="form-textarea" required></textarea>
                                    <input type="file" name="image" class="form-input">
                                    <div class="button-container">
                                        <button type="submit" class="cta-button">Ajouter le Service</button>
                                    </div>
                                </form>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <?php include 'footer.php'; ?>

    <script src="../assets/js/admin_service.js"></script>
    <script src="https://cdn.tiny.cloud/1/8pmzn31l5fg9pakc6iubiw3hvsowgx2qq1116dbvyo8sfn8b/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
</body>

</html>
