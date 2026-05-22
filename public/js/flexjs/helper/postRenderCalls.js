export const runPostRenderCalls = () => {
    const rows  = document.querySelectorAll('.ctr');
    const total = rows.length;

    // Actualiza conteo en barra de filtro si hay fechas activas
    const start   = document.getElementById('filter-start')?.value;
    const end     = document.getElementById('filter-end')?.value;
    const countEl = document.getElementById('filter-count');
    if (countEl && (start || end)) {
        countEl.textContent = `${total} resultado${total !== 1 ? 's' : ''}`;
    }
};
