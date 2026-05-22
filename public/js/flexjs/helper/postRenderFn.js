export const runPostRender = () => {
    const cards = document.querySelectorAll('.call-card');

    // Actualiza contadores de stats
    let completed = 0, active = 0, failed = 0;
    cards.forEach(card => {
        const badge = card.querySelector('.status-badge');
        if (!badge) return;
        const cls = badge.className;
        if (cls.includes('status-completada') || cls.includes('status-completed')) completed++;
        else if (cls.includes('status-failed')   || cls.includes('status-sin_respuesta')) failed++;
        else if (cls.includes('status-en_progreso') || cls.includes('status-ringing') || cls.includes('status-in_progress')) active++;
    });

    const set = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };
    set('stat-total',     cards.length);
    set('stat-completed', completed);
    set('stat-active',    active);
    set('stat-failed',    failed);

    // Muestra conteo en la barra de filtros si hay fechas activas
    const start = document.getElementById('filter-start')?.value;
    const end   = document.getElementById('filter-end')?.value;
    const countEl = document.getElementById('filter-count');
    if (countEl) {
        if (start || end) {
            countEl.textContent = `${cards.length} resultado${cards.length !== 1 ? 's' : ''}`;
            countEl.style.display = '';
        } else {
            countEl.textContent = '';
        }
    }
};
