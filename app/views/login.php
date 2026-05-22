<?php
$pageTitle = 'Iniciar sesión — CallDashboard';
$bodyClass = 'login-page';
include __DIR__ . '/layout/header.php';
?>

<div class="login-wrapper">
    <div class="login-card">

        <div class="login-brand">
            <div class="brand-icon">
                <i class="bi bi-telephone-fill"></i>
            </div>
            <h1>CallDashboard</h1>
            <p>Panel de gestión de llamadas</p>
        </div>

        <?php if (!empty($error)): ?>
        <div class="alert-error">
            <i class="bi bi-exclamation-circle"></i>
            <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="<?= route('auth', 'login') ?>" novalidate>
            <div class="field-group">
                <label for="username">Usuario</label>
                <div class="input-wrap">
                    <i class="bi bi-person"></i>
                    <input type="text" id="username" name="username"
                           placeholder="Ingresa tu usuario"
                           autocomplete="username" required autofocus>
                </div>
            </div>
            <div class="field-group">
                <label for="password">Contraseña</label>
                <div class="input-wrap">
                    <i class="bi bi-lock"></i>
                    <input type="password" id="password" name="password"
                           placeholder="••••••••"
                           autocomplete="current-password" required>
                </div>
            </div>
            <button type="submit" class="btn-login">
                <span>Iniciar sesión</span>
                <i class="bi bi-arrow-right"></i>
            </button>
        </form>

    </div>
</div>

<?php include __DIR__ . '/layout/footer.php'; ?>
