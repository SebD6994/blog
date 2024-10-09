<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h1>Tableau de bord de l'Admin</h1>

<form action="index.php?page=patients&action=logout" method="GET" style="margin-bottom: 20px;">
    <button type="submit">Déconnexion</button>
</form>

<!-- Section des Patients -->
<h2>Gestion des Patients</h2>
    
    <form action="add_patient.php" method="POST">
        <input type="text" name="first_name" placeholder="Prénom" required>
        <input type="text" name="last_name" placeholder="Nom" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="text" name="phone" placeholder="Téléphone" required>
        <input type="password" name="password" placeholder="Mot de passe" required>

        <label for="role">Rôle :</label>
            <select name="role" required>
                <option value="user">Utilisateur</option>
                <option value="admin">Administrateur</option>
            </select>

        <button type="submit">Ajouter Patient</button>
    </form>

    <h3>Liste des Patients</h3>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Prénom</th>
            <th>Nom</th>
            <th>Email</th>
            <th>Téléphone</th>
            <th>Rôle</th> <!-- Ajout de la colonne Rôle -->
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($patients)): ?>
            <?php foreach ($patients as $patient): ?>
                <tr>
                    <td><?php echo htmlspecialchars($patient['id']); ?></td>
                    <td><?php echo htmlspecialchars($patient['first_name']); ?></td>
                    <td><?php echo htmlspecialchars($patient['last_name']); ?></td>
                    <td><?php echo htmlspecialchars($patient['email']); ?></td>
                    <td><?php echo htmlspecialchars($patient['phone']); ?></td>
                    <td>
                        <form action="index.php?page=admin&action=updateRole" method="POST">
                            <input type="hidden" name="id" value="<?php echo $patient['id']; ?>">
                            <select name="role" onchange="this.form.submit()">
                                <option value="patient" <?php echo $patient['role'] === 'patient' ? 'selected' : ''; ?>>Utilisateur</option>
                                <option value="admin" <?php echo $patient['role'] === 'admin' ? 'selected' : ''; ?>>Administrateur</option>
                            </select>
                        </form>
                    </td>
                    <td>
                        <a href="edit_patient.php?id=<?php echo $patient['id']; ?>">Modifier</a>
                        <a href="delete_patient.php?id=<?php echo $patient['id']; ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce patient ?');">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="7">Aucun patient trouvé.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>


<!-- Section des Rendez-vous -->
<h2>Gestion des Rendez-vous</h2>

<!-- Formulaire d'ajout de rendez-vous -->
<form action="add_appointment.php" method="POST">
    <label for="patient_id">Patient :</label>
    <select name="patient_id" required>
        <?php if (!empty($patients)): ?>
            <?php foreach ($patients as $patient): ?>
                <option value="<?php echo htmlspecialchars($patient['id']); ?>">
                    <?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?>
                </option>
            <?php endforeach; ?>
        <?php else: ?>
            <option value="">Aucun patient disponible</option>
        <?php endif; ?>
    </select>

    <label for="service_id">Service :</label>
    <select name="service_id" required>
        <?php if (!empty($services)): ?>
            <?php foreach ($services as $service): ?>
                <option value="<?php echo htmlspecialchars($service['id']); ?>">
                    <?php echo htmlspecialchars($service['name']); ?>
                </option>
            <?php endforeach; ?>
        <?php else: ?>
            <option value="">Aucun service disponible</option>
        <?php endif; ?>
    </select>

    <label for="appointment_date">Date :</label>
    <input type="date" name="appointment_date" required>

    <label for="appointment_time">Heure :</label>
    <input type="time" name="appointment_time" required>

    <button type="submit">Ajouter Rendez-vous</button>
</form>

<h3>Liste des Rendez-vous</h3>
<table>
    <thead>
        <tr>
            <th>Nom du Patient</th>
            <th>Service</th>
            <th>Date de Rendez-vous</th>
            <th>Statut</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($appointments)): ?>
            <?php foreach ($appointments as $appointment): ?>
                <tr>
                    <td><?php echo htmlspecialchars($appointment['patient_name']); ?></td>
                    <td><?php echo htmlspecialchars($appointment['service_name']); ?></td>
                    <td><?php echo htmlspecialchars($appointment['appointment_date']); ?></td>
                    <td>

                    <form action="index.php?page=admin&action=updateAppointment" method="POST">
                        <input type="hidden" name="id" value="<?php echo $appointment['id']; ?>">">
                            <label for="status">Statut :</label>    
                            <select name="status" required>
                                <option value="pending" <?php echo $appointment['status'] === 'pending' ? 'selected' : ''; ?>>En attente</option>
                                <option value="confirmed" <?php echo $appointment['status'] === 'confirmed' ? 'selected' : ''; ?>>Confirmé</option>
                                <option value="cancelled" <?php echo $appointment['status'] === 'cancelled' ? 'selected' : ''; ?>>Annulé</option>
                            </select>
                            <button type="submit">Mettre à jour</button>
                        </form>
                    </td>
                    <td>
                        <a href="edit_appointment.php?id=<?php echo $appointment['id']; ?>">Modifier</a>
                        <a href="delete_appointment.php?id=<?php echo $appointment['id']; ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce rendez-vous ?');">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="7">Aucun rendez-vous trouvé.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<!-- Section des Services -->
<h2>Services</h2>
<table>
    <tr>
        <th>Nom</th>
        <th>Description</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($services as $service): ?>
    <tr>
            <form method="POST" action="?page=admin&action=updateService">
            <td><input type="text" name="name" value="<?= htmlspecialchars($service['name']); ?>" required></td>
            <td><input type="text" name="description" value="<?= htmlspecialchars($service['description']); ?>"></td>
                <input type="hidden" name="id" value="<?= $service['id']; ?>">
        <td>
            <button type="submit">Mettre à jour</button>
            <a href="?page=admin&action=deleteService&id=<?= $service['id']; ?>">Supprimer</a>
        </td>
        </form>
    </tr>
    <?php endforeach; ?>
</table>


<!-- Section des Actualités -->
<h2>Actualités</h2>
<table>
    <tr>
        <th>Titre</th>
        <th>Contenu</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($news as $new): ?>
    <tr>
        
            <form method="POST" action="?page=admin&action=updateNews">
            <td><input type="text" name="title" value="<?= htmlspecialchars($new['title']); ?>" required></td>
            <td><textarea name="content" required><?= htmlspecialchars($new['content']); ?></textarea></td>
                <input type="hidden" name="id" value="<?= $new['id']; ?>">
            
            <td><button type="submit">Mettre à jour</button>
            <a href="?page=admin&action=deleteNews&id=<?= $new['id']; ?>">Supprimer</a></td>
            </form>
    </tr>
    <?php endforeach; ?>
</table>

<!-- Section des Horaires d'ouverture -->
<h2>Horaires d'Ouverture</h2>
<form method="POST" action="?page=admin&action=updateOpeningHours">
    <?php foreach ($openingHours as $hour): ?>
    <div>
        <strong><?= htmlspecialchars($hour['day']); ?>:</strong>
        <input type="time" name="opening_time[<?= $hour['day']; ?>]" value="<?= htmlspecialchars($hour['opening_time']); ?>" required>
        <input type="time" name="closing_time[<?= $hour['day']; ?>]" value="<?= htmlspecialchars($hour['closing_time']); ?>" required>
    </div>
    <?php endforeach; ?>
    <button type="submit">Mettre à jour les horaires</button>
</form>

<footer>
        <p>&copy; 2024 Cabinet du Dr. Dupont. Tous droits réservés.</p>
    </footer>
</body>
</html>