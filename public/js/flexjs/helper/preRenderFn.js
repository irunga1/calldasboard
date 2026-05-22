export const runBeforeRender = () => {
    const container = document.getElementById('calls-container');
    if (container) {
        container.innerHTML = `
            <div class="col-12 text-center py-5">
                <div class="spinner-border text-primary" role="status" style="width:2rem;height:2rem;">
                    <span class="visually-hidden">Cargando…</span>
                </div>
                <p class="text-muted mt-3 mb-0" style="font-size:.85rem;">Conectando con SynthFlow…</p>
            </div>`;
    }
};
