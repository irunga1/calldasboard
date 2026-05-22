export const callsTable = (items = [], containerId) => {
    const container = document.getElementById(containerId);
    if (!container) return;

    window.__callsData = Array.isArray(items) ? items : [];

    if (!window.__callsData.length) {
        container.innerHTML = `<div class="empty-state"><i class="bi bi-telephone-x"></i><p>No se encontraron llamadas.</p></div>`;
        return;
    }

    const rows = window.__callsData.map((call, i) => {
        const jr       = call.judge_results || {};
        const phone    = call.lead_phone_number || call.phone_number_from || 'N/A';
        const date     = fmtDatetime(call.telephony_start);
        const duration = fmtMs(call.telephony_duration);
        const type     = call.type_of_call === 'inbound' ? 'Entrante' : 'Saliente';
        const typeCls  = call.type_of_call === 'inbound' ? 'inbound' : 'outbound';
        const reason   = humanReason(call.end_call_reason);
        const sentiment = jr.user_sentiment || null;
        const appt     = jr.appointment;
        const goal     = jr.goal;
        const name     = call.lead_name || null;

        return `
        <tr class="ctr" onclick="window.openCallModal(${i})">
            <td>
                <div class="ct-date">${date}</div>
                ${name ? `<div class="ct-name">${name}</div>` : ''}
            </td>
            <td class="ct-phone">${phone}</td>
            <td><span class="badge-type type-${typeCls}">${type}</span></td>
            <td class="ct-dur">${duration}</td>
            <td><span class="reason-pill">${reason}</span></td>
            <td>${sentiment ? `<span class="sentiment sentiment-${sentiment}">${sentLabel(sentiment)}</span>` : '<span class="text-muted">—</span>'}</td>
            <td>${apptBadge(appt)}</td>
            <td>${goalBadge(goal)}</td>
            <td><button class="btn-ver" onclick="event.stopPropagation(); window.openCallModal(${i})"><i class="bi bi-eye"></i></button></td>
        </tr>`;
    }).join('');

    container.innerHTML = `
        <div class="calls-table-wrap">
            <table class="calls-table">
                <thead>
                    <tr>
                        <th>Fecha / Hora</th>
                        <th>Teléfono</th>
                        <th>Tipo</th>
                        <th>Duración</th>
                        <th>Razón de fin</th>
                        <th>Sentimiento</th>
                        <th>Cita</th>
                        <th>Meta</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>${rows}</tbody>
            </table>
        </div>`;
};

// ── Utilities ────────────────────────────────────────────────────────────────

function fmtDatetime(dt) {
    if (!dt) return '—';
    try {
        return new Date(dt).toLocaleString('es-GT', { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
    } catch { return dt; }
}

function fmtMs(ms) {
    if (!ms) return '—';
    const s = Math.floor(ms / 1000);
    const m = Math.floor(s / 60);
    return m > 0 ? `${m}m ${s % 60}s` : `${s}s`;
}

function humanReason(r) {
    const map = {
        human_goodbye: 'Se despidió',
        agent_goodbye: 'Agente colgó',
        voicemail_reached: 'Buzón de voz',
        no_answer: 'Sin respuesta',
        call_transfer: 'Transferencia',
        max_duration: 'Límite alcanzado',
    };
    return map[r] || (r ? r.replace(/_/g, ' ') : '—');
}

function sentLabel(s) {
    return { positive: 'Positivo', partial: 'Parcial', negative: 'Negativo', not_applicable: 'N/A' }[s] || s;
}

function apptBadge(val) {
    if (val === 'true')  return '<span class="bool-badge bool-yes"><i class="bi bi-calendar-check"></i> Sí</span>';
    if (val === 'false') return '<span class="bool-badge bool-no"><i class="bi bi-calendar-x"></i> No</span>';
    return '<span class="text-muted">—</span>';
}

function goalBadge(val) {
    if (val === 'true')    return '<span class="bool-badge bool-yes"><i class="bi bi-check-circle"></i> Lograda</span>';
    if (val === 'partial') return '<span class="bool-badge bool-partial"><i class="bi bi-dash-circle"></i> Parcial</span>';
    if (val === 'false')   return '<span class="bool-badge bool-no"><i class="bi bi-x-circle"></i> No lograda</span>';
    return '<span class="text-muted">—</span>';
}
