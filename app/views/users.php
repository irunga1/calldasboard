<?php
$bodyClass = 'dashboard-page';
$users  = $users  ?? [];
$roles  = $roles  ?? [];
$flash  = $flash  ?? null;
include __DIR__ . '/layout/header.php';
?>

<div class="dashboard-wrapper">

    <!-- ── Sidebar ─────────────────────────────────────────────────── -->
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="brand-icon"><i class="bi bi-telephone-fill"></i></div>
            <span>CallDashboard</span>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section-label">Principal</div>
            <a href="<?= route('dashboard') ?>" class="nav-link">
                <i class="bi bi-grid-1x2-fill"></i>
                <span>Dashboard</span>
            </a>
            <a href="<?= route('dashboard', 'calls') ?>" class="nav-link">
                <i class="bi bi-table"></i>
                <span>Detalle de llamadas</span>
            </a>

            <div class="nav-section-label mt-2">Administración</div>
            <a href="<?= route('user') ?>" class="nav-link active">
                <i class="bi bi-people"></i>
                <span>Usuarios</span>
            </a>

            <div class="nav-section-label mt-2">Sesión</div>
            <a href="<?= route('auth', 'logout') ?>" class="nav-link nav-link-danger">
                <i class="bi bi-box-arrow-left"></i>
                <span>Cerrar sesión</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            SynthFlow API · v1.0
        </div>
    </aside>

    <!-- ── Main ─────────────────────────────────────────────────── -->
    <div class="main-content">

        <!-- Top navbar -->
        <header class="top-navbar">
            <h5 class="page-title">
                <i class="bi bi-people me-2 text-muted" style="font-size:.9rem;"></i>
                Usuarios
            </h5>
            <div class="user-menu">
                <div class="user-avatar">
                    <?= strtoupper(substr(htmlspecialchars($username ?? 'U'), 0, 1)) ?>
                </div>
                <div class="user-info">
                    <div class="user-name"><?= htmlspecialchars($username ?? 'Usuario') ?></div>
                    <div class="user-role">Administrador</div>
                </div>
                <a href="<?= route('auth', 'logout') ?>" class="btn-icon-logout" title="Cerrar sesión">
                    <i class="bi bi-box-arrow-right"></i>
                </a>
            </div>
        </header>

        <!-- Page body -->
        <main class="page-content">

            <div class="page-header">
                <h1>Gestión de usuarios</h1>
                <p>Administra los usuarios y sus roles en el sistema.</p>
            </div>

            <div class="page-header-row">
                <span style="font-size:.82rem;color:var(--text-muted);">
                    <?= count($users) ?> usuario<?= count($users) !== 1 ? 's' : '' ?> registrado<?= count($users) !== 1 ? 's' : '' ?>
                </span>
                <button class="btn-add-user" data-bs-toggle="modal" data-bs-target="#modalUser" onclick="openCreateModal()">
                    <i class="bi bi-person-plus-fill"></i>
                    Nuevo usuario
                </button>
            </div>

            <!-- Flash message -->
            <?php if (!empty($flash)): ?>
            <div class="flash-alert flash-<?= $flash['type'] ?>">
                <i class="bi bi-<?= $flash['type'] === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill' ?>"></i>
                <?= htmlspecialchars($flash['msg']) ?>
            </div>
            <?php endif; ?>

            <!-- Users table -->
            <div class="users-card">
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Usuario</th>
                            <th>Rol</th>
                            <th>Creado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No hay usuarios registrados.</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($users as $u): ?>
                        <tr>
                            <td class="user-id"><?= (int)$u['id'] ?></td>
                            <td>
                                <div class="user-cell">
                                    <div class="user-avatar-sm">
                                        <?= strtoupper(substr(htmlspecialchars($u['username']), 0, 1)) ?>
                                    </div>
                                    <span><?= htmlspecialchars($u['username']) ?></span>
                                    <?php if ((int)$u['id'] === (int)($_SESSION['user_id'] ?? 0)): ?>
                                    <span class="badge-you">Tú</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <span class="role-badge role-<?= $u['role_id'] == 1 ? 'admin' : 'operator' ?>">
                                    <i class="bi bi-<?= $u['role_id'] == 1 ? 'shield-fill' : 'person-fill' ?>"></i>
                                    <?= htmlspecialchars($u['role_name']) ?>
                                </span>
                            </td>
                            <td class="text-muted" style="font-size:.82rem;">
                                <?= date('d/m/Y H:i', strtotime($u['created_at'])) ?>
                            </td>
                            <td>
                                <div class="action-btns">
                                    <button class="btn-action btn-edit"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalUser"
                                        onclick="openEditModal(<?= htmlspecialchars(json_encode([
                                            'id'       => (int)$u['id'],
                                            'username' => $u['username'],
                                            'role_id'  => (int)$u['role_id'],
                                        ])) ?>)">
                                        <i class="bi bi-pencil-fill"></i>
                                        Editar
                                    </button>
                                    <?php if ((int)$u['id'] !== (int)($_SESSION['user_id'] ?? 0)): ?>
                                    <button class="btn-action btn-delete"
                                        onclick="confirmDelete(<?= (int)$u['id'] ?>, '<?= htmlspecialchars($u['username'], ENT_QUOTES) ?>')">
                                        <i class="bi bi-trash3-fill"></i>
                                        Eliminar
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </main>
    </div>
</div>

<!-- ── Modal: crear / editar usuario ──────────────────────────────── -->
<div class="modal fade" id="modalUser" tabindex="-1" aria-labelledby="modalUserLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content user-modal">
            <div class="modal-header">
                <h5 class="modal-title" id="modalUserLabel">Nuevo usuario</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="userForm" method="POST" action="<?= route('user', 'store') ?>">
                <input type="hidden" name="id" id="userId" value="">
                <div class="modal-body">

                    <div class="field-group">
                        <label for="inputUsername">Nombre de usuario</label>
                        <div class="input-wrap">
                            <i class="bi bi-person"></i>
                            <input type="text" id="inputUsername" name="username" required
                                   placeholder="ej. juan.perez" autocomplete="off">
                        </div>
                    </div>

                    <div class="field-group">
                        <label for="inputPassword" id="passwordLabel">Contraseña</label>
                        <div class="input-wrap">
                            <i class="bi bi-lock"></i>
                            <input type="password" id="inputPassword" name="password"
                                   placeholder="Mínimo 6 caracteres" autocomplete="new-password">
                        </div>
                        <p class="field-hint" id="passwordHint" style="display:none;">
                            Deja en blanco para mantener la contraseña actual.
                        </p>
                    </div>

                    <div class="field-group mb-0">
                        <label for="inputRole">Rol</label>
                        <select id="inputRole" name="role_id" class="input-select">
                            <?php foreach ($roles as $r): ?>
                            <option value="<?= (int)$r['id'] ?>">
                                <?= htmlspecialchars($r['role_name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn-modal-save">
                        <i class="bi bi-check-lg"></i>
                        <span id="modalSaveLabel">Crear usuario</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ── Modal: confirmar eliminación ──────────────────────────────── -->
<div class="modal fade" id="modalDelete" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content user-modal">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar eliminación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-3">
                <i class="bi bi-exclamation-triangle-fill text-warning" style="font-size:2rem;display:block;margin-bottom:.75rem;"></i>
                <p style="font-size:.9rem;color:var(--text);">
                    ¿Eliminar al usuario <strong id="deleteUsername"></strong>?<br>
                    <span style="font-size:.8rem;color:var(--text-muted);">Esta acción no se puede deshacer.</span>
                </p>
            </div>
            <div class="modal-footer justify-content-center gap-2">
                <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">Cancelar</button>
                <form id="deleteForm" method="POST" action="<?= route('user', 'destroy') ?>" style="display:inline;">
                    <input type="hidden" name="id" id="deleteUserId">
                    <button type="submit" class="btn-modal-danger">
                        <i class="bi bi-trash3-fill"></i>
                        Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function openCreateModal() {
    document.getElementById('userForm').action = '<?= route('user', 'store') ?>';
    document.getElementById('modalUserLabel').textContent = 'Nuevo usuario';
    document.getElementById('modalSaveLabel').textContent = 'Crear usuario';
    document.getElementById('userId').value = '';
    document.getElementById('inputUsername').value = '';
    document.getElementById('inputPassword').value = '';
    document.getElementById('inputPassword').required = true;
    document.getElementById('passwordLabel').textContent = 'Contraseña';
    document.getElementById('passwordHint').style.display = 'none';
    document.getElementById('inputRole').value = '<?= (int)($roles[1]['id'] ?? 2) ?>';
}

function openEditModal(data) {
    document.getElementById('userForm').action = '<?= route('user', 'update') ?>';
    document.getElementById('modalUserLabel').textContent = 'Editar usuario';
    document.getElementById('modalSaveLabel').textContent = 'Guardar cambios';
    document.getElementById('userId').value = data.id;
    document.getElementById('inputUsername').value = data.username;
    document.getElementById('inputPassword').value = '';
    document.getElementById('inputPassword').required = false;
    document.getElementById('passwordLabel').textContent = 'Nueva contraseña';
    document.getElementById('passwordHint').style.display = '';
    document.getElementById('inputRole').value = data.role_id;
}

function confirmDelete(id, username) {
    document.getElementById('deleteUserId').value = id;
    document.getElementById('deleteUsername').textContent = username;
    new bootstrap.Modal(document.getElementById('modalDelete')).show();
}
</script>

<?php include __DIR__ . '/layout/footer.php'; ?>
