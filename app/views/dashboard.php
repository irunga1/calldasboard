<?php
$bodyClass = 'dashboard-page';
$scripts   = [route('api', 'calls')]; // solo para referencia; el JS usa window.FLEX_API_URL
$scripts   = [BASE_URL . '/public/js/flexjs/main.js'];
include __DIR__ . '/layout/header.php';
?>

<script>window.FLEX_API_URL = '<?= route('api', 'calls') ?>';</script>

<div class="dashboard-wrapper">

    <!-- ── Sidebar ───────────────────────────────────────────── -->
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="brand-icon">
                <i class="bi bi-telephone-fill"></i>
            </div>
            <span>CallDashboard</span>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section-label">Principal</div>

            <a href="<?= route('dashboard') ?>" class="nav-link active">
                <i class="bi bi-grid-1x2-fill"></i>
                <span>Dashboard</span>
            </a>
            <a href="<?= route('dashboard', 'calls') ?>" class="nav-link">
                <i class="bi bi-table"></i>
                <span>Detalle de llamadas</span>
            </a>

            <?php if (($_SESSION['role_id'] ?? 2) == 1): ?>
            <div class="nav-section-label mt-2">Administración</div>
            <a href="<?= route('user') ?>" class="nav-link">
                <i class="bi bi-people"></i>
                <span>Usuarios</span>
            </a>
            <?php endif; ?>

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

    <!-- ── Main ─────────────────────────────────────────────── -->
    <div class="main-content">

        <!-- Top navbar -->
        <header class="top-navbar">
            <h5 class="page-title">
                <i class="bi bi-grid-1x2 me-2 text-muted" style="font-size:.9rem;"></i>
                Dashboard
            </h5>
            <div class="user-menu">
                <div class="user-avatar">
                    <?= strtoupper(substr(htmlspecialchars($username ?? 'U'), 0, 1)) ?>
                </div>
                <div class="user-info">
                    <div class="user-name"><?= htmlspecialchars($username ?? 'Usuario') ?></div>
                    <div class="user-role">
                        <?= ($role_id ?? 2) == 1 ? 'Administrador' : 'Operador' ?>
                    </div>
                </div>
                <a href="<?= route('auth', 'logout') ?>"
                   class="btn-icon-logout"
                   title="Cerrar sesión">
                    <i class="bi bi-box-arrow-right"></i>
                </a>
            </div>
        </header>

        <!-- Page body -->
        <main class="page-content">

            <div class="page-header">
                <h1>Resumen de llamadas</h1>
                <p>Monitorea en tiempo real las llamadas gestionadas por SynthFlow.</p>
            </div>

            <!-- ── Stats (PHP renderiza estructura, FlexJS actualiza valores) -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon stat-icon-blue">
                        <i class="bi bi-telephone-inbound-fill"></i>
                    </div>
                    <div class="stat-body">
                        <div class="stat-label">Total llamadas</div>
                        <div class="stat-value" id="stat-total">—</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon stat-icon-green">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div class="stat-body">
                        <div class="stat-label">Completadas</div>
                        <div class="stat-value" id="stat-completed">—</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon stat-icon-amber">
                        <i class="bi bi-arrow-repeat"></i>
                    </div>
                    <div class="stat-body">
                        <div class="stat-label">En progreso</div>
                        <div class="stat-value" id="stat-active">—</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon stat-icon-red">
                        <i class="bi bi-x-circle-fill"></i>
                    </div>
                    <div class="stat-body">
                        <div class="stat-label">Fallidas</div>
                        <div class="stat-value" id="stat-failed">—</div>
                    </div>
                </div>
            </div>

            <!-- ── Calls (FlexJS renderiza aquí) ── -->
            <div class="section-header">
                <h2 class="section-title">Llamadas recientes</h2>
                <button class="btn-refresh" onclick="location.reload()">
                    <i class="bi bi-arrow-clockwise"></i>
                    Actualizar
                </button>
            </div>

            <!-- Filtro de fechas (PHP renderiza, FlexJS recarga los datos) -->
            <div class="date-filter-bar">
                <div class="date-filter-group">
                    <label for="filter-start">Desde</label>
                    <input type="date" id="filter-start" class="date-input">
                </div>
                <div class="date-filter-sep">→</div>
                <div class="date-filter-group">
                    <label for="filter-end">Hasta</label>
                    <input type="date" id="filter-end" class="date-input">
                </div>
                <button class="btn-filter-apply" onclick="applyDateFilter()">
                    <i class="bi bi-funnel-fill"></i>
                    Filtrar
                </button>
                <button class="btn-filter-clear" id="btn-clear" onclick="clearDateFilter()" style="display:none">
                    <i class="bi bi-x-lg"></i>
                    Limpiar
                </button>
                <span class="filter-result-count" id="filter-count"></span>
            </div>

            <div class="row g-3" id="calls-container">
                <div class="col-12 text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando…</span>
                    </div>
                </div>
            </div>

        </main>
    </div>
</div>

<script>
(function () {
    // Establece max=hoy en ambos inputs
    const today = new Date().toISOString().slice(0, 10);
    document.getElementById('filter-start').max = today;
    document.getElementById('filter-end').max   = today;

    function getApiUrl(start, end) {
        const url = new URL(window.FLEX_API_URL, window.location.href);
        url.searchParams.delete('page');
        if (start) url.searchParams.set('start_date', start);
        else        url.searchParams.delete('start_date');
        if (end)   url.searchParams.set('end_date', end);
        else        url.searchParams.delete('end_date');
        return url.toString();
    }

    window.applyDateFilter = function () {
        const start = document.getElementById('filter-start').value;
        const end   = document.getElementById('filter-end').value;
        if (!start && !end) return;

        const clearBtn = document.getElementById('btn-clear');
        if (clearBtn) clearBtn.style.display = '';

        if (window.SetUrl) {
            window.SetUrl(getApiUrl(start, end));
        } else {
            // FlexJS aún no cargó: espera y reintenta
            setTimeout(window.applyDateFilter, 400);
        }
    };

    window.clearDateFilter = function () {
        document.getElementById('filter-start').value = '';
        document.getElementById('filter-end').value   = '';
        document.getElementById('btn-clear').style.display = 'none';
        document.getElementById('filter-count').textContent = '';
        if (window.SetUrl) window.SetUrl(window.FLEX_API_URL);
    };

    // Permite aplicar con Enter en cualquier input de fecha
    ['filter-start', 'filter-end'].forEach(id => {
        document.getElementById(id)?.addEventListener('keydown', e => {
            if (e.key === 'Enter') window.applyDateFilter();
        });
    });
})();
</script>

<?php include __DIR__ . '/layout/footer.php'; ?>
