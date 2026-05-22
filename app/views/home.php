<!DOCTYPE html>
<html>
<head><title>Usuarios</title></head>
<body>
    <h1>Lista de Usuarios</h1>
    <ul>
        <?php foreach ($users as $user): ?>
            <li><?= $user['user']; ?> (<?= $user['correo']; ?>)</li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
