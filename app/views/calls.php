<?php
$bodyClass = 'dashboard-page';
$scripts   = [BASE_URL . '/public/js/flexjs/calls-main.js'];
include __DIR__ . '/layout/header.php';
?>

<script>window.FLEX_API_URL = '<?= route('api', 'calls') ?>?limit=100';</script>

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
                <i class="bi bi-grid-1x2-fill"></i><span>Dashboard</span>
            </a>
            <a href="<?= route('dashboard', 'calls') ?>" class="nav-link active">
                <i class="bi bi-table"></i><span>Detalle de llamadas</span>
            </a>
            <?php if (($_SESSION['role_id'] ?? 2) == 1): ?>
            <div class="nav-section-label mt-2">Administración</div>
            <a href="<?= route('user') ?>" class="nav-link"><i class="bi bi-people"></i><span>Usuarios</span></a>
            <?php endif; ?>
            <div class="nav-section-label mt-2">Sesión</div>
            <a href="<?= route('auth', 'logout') ?>" class="nav-link nav-link-danger">
                <i class="bi bi-box-arrow-left"></i><span>Cerrar sesión</span>
            </a>
        </nav>
        <div class="sidebar-footer">SynthFlow API · v1.0</div>
    </aside>

    <!-- ── Main ────────────────────────────────────────────────────── -->
    <div class="main-content">
        <header class="top-navbar">
            <h5 class="page-title">
                <i class="bi bi-table me-2 text-muted" style="font-size:.9rem;"></i>
                Detalle de llamadas
            </h5>
            <div class="user-menu">
                <div class="user-avatar"><?= strtoupper(substr($username ?? 'U', 0, 1)) ?></div>
                <div class="user-info">
                    <div class="user-name"><?= htmlspecialchars($username ?? 'Usuario') ?></div>
                    <div class="user-role"><?= ($role_id ?? 2) == 1 ? 'Administrador' : 'Operador' ?></div>
                </div>
                <a href="<?= route('auth', 'logout') ?>" class="btn-icon-logout" title="Cerrar sesión">
                    <i class="bi bi-box-arrow-right"></i>
                </a>
            </div>
        </header>

        <main class="page-content">
            <div class="page-header">
                <h1>Detalle de llamadas</h1>
                <p>Historial completo con análisis de IA, transcripción y métricas de cada llamada.</p>
            </div>

            <!-- Filtro de fechas -->
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
                    <i class="bi bi-funnel-fill"></i> Filtrar
                </button>
                <button class="btn-filter-clear" id="btn-clear" onclick="clearDateFilter()" style="display:none">
                    <i class="bi bi-x-lg"></i> Limpiar
                </button>
                <span class="filter-result-count" id="filter-count"></span>
            </div>

            <!-- Tabla FlexJS -->
            <div id="calls-table-container">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="text-muted mt-3 mb-0" style="font-size:.85rem;">Cargando llamadas…</p>
                </div>
            </div>

        </main>
    </div>
</div>

<!-- ── Modal detalle de llamada ──────────────────────────────────── -->
<div class="modal fade" id="callModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title mb-0" id="modal-call-title">Llamada</h5>
                    <div class="modal-call-meta" id="modal-call-meta"></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <!-- Tabs -->
                <ul class="nav nav-tabs modal-tabs" id="callTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-info">
                            <i class="bi bi-info-circle me-1"></i> Información
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-score">
                            <i class="bi bi-star me-1"></i> Evaluación IA
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-transcript">
                            <i class="bi bi-chat-text me-1"></i> Transcripción
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-actions">
                            <i class="bi bi-lightning me-1"></i> Acciones
                        </button>
                    </li>
                </ul>
                <div class="tab-content modal-tab-content">

                    <!-- Tab: Información -->
                    <div class="tab-pane fade show active" id="tab-info">
                        <div class="row g-0">
                            <div class="col-md-6 modal-info-col">
                                <div class="info-section-title">Datos de la llamada</div>
                                <div id="modal-call-info"></div>
                            </div>
                            <div class="col-md-6 modal-info-col border-start">
                                <div class="info-section-title">Resumen del juez</div>
                                <div id="modal-call-summary"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab: Evaluación IA -->
                    <div class="tab-pane fade" id="tab-score">
                        <div class="p-4">
                            <div class="scorecard-grid" id="modal-scorecard"></div>
                            <div class="mt-4" id="modal-all-feedback"></div>
                        </div>
                    </div>

                    <!-- Tab: Transcripción -->
                    <div class="tab-pane fade" id="tab-transcript">
                        <div class="transcript-box" id="modal-transcript"></div>
                    </div>

                    <!-- Tab: Acciones ejecutadas -->
                    <div class="tab-pane fade" id="tab-actions">
                        <div class="p-4" id="modal-actions"></div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const today = new Date().toISOString().slice(0, 10);
    document.getElementById('filter-start').max = today;
    document.getElementById('filter-end').max   = today;

    function getApiUrl(start, end) {
        const url = new URL(window.FLEX_API_URL, window.location.href);
        url.searchParams.delete('page');
        start ? url.searchParams.set('start_date', start) : url.searchParams.delete('start_date');
        end   ? url.searchParams.set('end_date', end)     : url.searchParams.delete('end_date');
        return url.toString();
    }

    window.applyDateFilter = function () {
        const s = document.getElementById('filter-start').value;
        const e = document.getElementById('filter-end').value;
        if (!s && !e) return;
        document.getElementById('btn-clear').style.display = '';
        const fn = () => window.SetUrl ? window.SetUrl(getApiUrl(s, e)) : setTimeout(fn, 300);
        fn();
    };

    window.clearDateFilter = function () {
        document.getElementById('filter-start').value = '';
        document.getElementById('filter-end').value   = '';
        document.getElementById('btn-clear').style.display = 'none';
        document.getElementById('filter-count').textContent = '';
        if (window.SetUrl) window.SetUrl(window.FLEX_API_URL);
    };

    ['filter-start', 'filter-end'].forEach(id =>
        document.getElementById(id)?.addEventListener('keydown', e => { if (e.key === 'Enter') window.applyDateFilter(); })
    );

    // ── Modal ──────────────────────────────────────────────────────
    window.openCallModal = function (idx) {
        const call = (window.__callsData || [])[idx];
        if (!call) return;
        const jr = call.judge_results || {};

        // Header
        const phone = call.lead_phone_number || call.phone_number_from || 'N/A';
        document.getElementById('modal-call-title').textContent = phone;
        document.getElementById('modal-call-meta').innerHTML =
            `<span class="badge-type type-${call.type_of_call}">${call.type_of_call === 'inbound' ? 'Entrante' : 'Saliente'}</span>
             <span class="ms-2 text-muted" style="font-size:.75rem">${call.call_id || ''}</span>`;

        // ── Tab 1: Info ──────────────────────────────────────────
        document.getElementById('modal-call-info').innerHTML = infoRows([
            ['Inicio',      fmtDatetime(call.telephony_start)],
            ['Fin',         fmtDatetime(call.telephony_end)],
            ['Duración',    fmtMs(call.telephony_duration)],
            ['Timbre',      fmtMs(call.telephony_ringing_duration)],
            ['Teléfono',    phone],
            ['Agente',      call.agent_phone_number || '—'],
            ['Fin llamada', humanReason(call.end_call_reason)],
            ['Colgó',       call.telephony_hangup === 'caller' ? 'Cliente' : 'Agente'],
            ['Estado',      call.call_status || '—'],
            ['Zona horaria',call.timezone || '—'],
        ]);

        document.getElementById('modal-call-summary').innerHTML = jr.call_summary_feedback
            ? `<p class="summary-text">${jr.call_summary_feedback}</p>`
            : '<p class="text-muted" style="font-size:.82rem">Sin resumen disponible.</p>';

        // ── Tab 2: Scorecard ──────────────────────────────────────
        const scores = [
            { key: 'goal',          label: 'Meta',            icon: 'bi-bullseye' },
            { key: 'appointment',   label: 'Cita',            icon: 'bi-calendar-check' },
            { key: 'call_completion', label: 'Completada',    icon: 'bi-check-circle' },
            { key: 'steps',         label: 'Pasos',           icon: 'bi-list-check' },
            { key: 'style',         label: 'Estilo',          icon: 'bi-chat-quote' },
            { key: 'persona',       label: 'Persona',         icon: 'bi-person-check' },
            { key: 'no_repetition', label: 'Sin repetición',  icon: 'bi-arrow-repeat' },
            { key: 'no_opt_out',    label: 'No opt-out',      icon: 'bi-shield-check' },
            { key: 'answered_by_human', label: 'Por humano',  icon: 'bi-person-lines-fill' },
        ];
        document.getElementById('modal-scorecard').innerHTML = scores.map(s => scoreCard(jr[s.key], s.label, s.icon)).join('');

        const sentRow = `
            <div class="sentiment-row">
                <div><span class="smeta">Sentimiento cliente</span>
                     <span class="sentiment sentiment-${jr.user_sentiment}">${sentimentLabel(jr.user_sentiment)}</span></div>
                <div><span class="smeta">Sentimiento agente</span>
                     <span class="sentiment sentiment-${jr.agent_sentiment}">${sentimentLabel(jr.agent_sentiment)}</span></div>
            </div>`;

        const feedbacks = [
            ['Meta', jr.goal_feedback],
            ['Cita', jr.appointment_feedback],
            ['Pasos', jr.steps_feedback],
            ['Estilo', jr.style_feedback],
            ['Persona', jr.persona_feedback],
            ['Sin repetición', jr.no_repetition_feedback],
            ['Objeciones', jr.objections_feedback],
            ['Sentimiento cliente', jr.user_sentiment_feedback],
            ['Sentimiento agente', jr.agent_sentiment_feedback],
            ['Opt-out', jr.no_opt_out_feedback],
        ].filter(([,v]) => v);

        document.getElementById('modal-all-feedback').innerHTML = sentRow + feedbacks.map(([label, text]) =>
            `<div class="feedback-item"><div class="feedback-label">${label}</div><div class="feedback-text">${text}</div></div>`
        ).join('');

        // ── Tab 3: Transcript ─────────────────────────────────────
        document.getElementById('modal-transcript').innerHTML = formatTranscript(call.transcript);

        // ── Tab 4: Actions ────────────────────────────────────────
        document.getElementById('modal-actions').innerHTML = formatActions(call.executed_actions);

        // Show modal & reset to first tab
        document.querySelector('#callTabs .nav-link.active')?.classList.remove('active');
        document.querySelector('#callTabs .nav-link')?.classList.add('active');
        document.querySelectorAll('.tab-pane').forEach((p, i) => {
            p.classList.toggle('show', i === 0);
            p.classList.toggle('active', i === 0);
        });
        bootstrap.Modal.getOrCreateInstance(document.getElementById('callModal')).show();
    };

    // ── Helpers ────────────────────────────────────────────────────
    function infoRows(pairs) {
        return pairs.map(([k, v]) =>
            `<div class="info-row"><span class="info-label">${k}</span><span class="info-value">${v || '—'}</span></div>`
        ).join('');
    }

    function scoreCard(val, label, icon) {
        const cls = val === 'true' ? 'score-yes' : val === 'false' ? 'score-no' : val === 'partial' ? 'score-partial' : 'score-na';
        const ico = val === 'true' ? 'bi-check-lg' : val === 'false' ? 'bi-x-lg' : val === 'partial' ? 'bi-dash-lg' : 'bi-dash';
        return `<div class="score-card ${cls}">
                    <i class="bi ${icon} score-icon-main"></i>
                    <div class="score-label">${label}</div>
                    <div class="score-result"><i class="bi ${ico}"></i></div>
                </div>`;
    }

    function formatTranscript(raw) {
        if (!raw) return '<div class="text-muted p-4">Sin transcripción disponible.</div>';
        const lines = raw.split('\n').map(l => l.trim()).filter(Boolean);
        return lines.map(line => {
            if (line.startsWith('bot:')) {
                return `<div class="msg msg-bot"><div class="msg-bubble">${line.slice(4).trim()}</div></div>`;
            } else if (line.startsWith('human:')) {
                return `<div class="msg msg-human"><div class="msg-bubble">${line.slice(6).trim()}</div></div>`;
            }
            return `<div class="msg-system">${line}</div>`;
        }).join('');
    }

    function formatActions(actions) {
        if (!actions || !Object.keys(actions).length)
            return '<p class="text-muted">No se ejecutaron acciones en esta llamada.</p>';
        return Object.entries(actions).map(([key, action]) => {
            const statusOk = !action.error_message || action.error_message === '';
            let retVal = '';
            try { retVal = JSON.stringify(JSON.parse(action.return_value), null, 2); } catch { retVal = action.return_value || '—'; }
            return `
                <div class="action-card ${statusOk ? 'action-ok' : 'action-fail'}">
                    <div class="action-header">
                        <span class="action-name"><i class="bi bi-lightning-charge me-1"></i>${action.name || key}</span>
                        <span class="action-type">${action.action_type || ''}</span>
                        <span class="action-status">${statusOk ? '✓ OK' : '✗ Error'}</span>
                    </div>
                    <div class="action-desc">${action.description || ''}</div>
                    ${action.error_message ? `<div class="action-error">${action.error_message}</div>` : ''}
                    <pre class="action-return">${retVal.slice(0, 400)}${retVal.length > 400 ? '…' : ''}</pre>
                </div>`;
        }).join('');
    }

    function fmtDatetime(dt) {
        if (!dt) return '—';
        try { return new Date(dt).toLocaleString('es-GT', { weekday:'short', month:'short', day:'numeric', hour:'2-digit', minute:'2-digit' }); }
        catch { return dt; }
    }

    function fmtMs(ms) {
        if (!ms) return '—';
        const s = Math.floor(ms / 1000);
        const m = Math.floor(s / 60);
        const r = s % 60;
        return m > 0 ? `${m}m ${r}s` : `${r}s`;
    }

    function humanReason(r) {
        const map = { human_goodbye:'Cliente se despidió', agent_goodbye:'Agente se despidió', voicemail_reached:'Buzón de voz', no_answer:'Sin respuesta', call_transfer:'Transferencia', max_duration:'Duración máxima' };
        return map[r] || r?.replace(/_/g,' ') || '—';
    }

    function sentimentLabel(s) {
        const map = { positive:'Positivo', partial:'Parcial', negative:'Negativo', not_applicable:'N/A' };
        return map[s] || s || '—';
    }
})();
</script>

<?php include __DIR__ . '/layout/footer.php'; ?>
