export const callCards = (items = [], containerId) => {
    const container = document.getElementById(containerId);
    if (!container) return;

    if (!Array.isArray(items) || items.length === 0) {
        container.innerHTML = `
            <div class="col-12">
                <div class="empty-state">
                    <i class="bi bi-telephone-x"></i>
                    <p>No se encontraron llamadas registradas.</p>
                </div>
            </div>`;
        return;
    }

    container.innerHTML = items.map(call => {
        const status     = deriveStatus(call);
        const statusCls  = status.toLowerCase().replace(/[\s/]+/g, '_');
        const phone      = call.lead_phone_number || call.phone_number_to || call.phone_number_from || 'N/A';
        const name       = call.lead_name || call.name || null;
        const duration   = formatDuration(call.duration || call.telephony_duration);
        const date       = formatDate(call.start_time || call.telephony_start || call.created_at);
        const shortId    = String(call.call_id || '').slice(-8) || '—';
        const sentiment  = call.judge_results?.user_sentiment || null;
        const summary    = call.judge_results?.call_summary_feedback || null;
        const agendada   = call.judge_results.appointment;
        const reason     = call.end_call_reason
            ? call.end_call_reason.replace(/_/g, ' ')
            : null;
        if(agendada === true || agendada === 'true') {
        return `
        <div class="col-12 col-md-6 col-xl-4">
            <div class="call-card">
                <div class="call-card-header">
                    <span class="status-badge status-${statusCls}">${status}</span>
                    <span class="call-id">${shortId}</span>
                </div>
                <div class="call-card-body">
                    <div class="call-phone">
                        <i class="bi bi-telephone"></i>
                        ${phone}
                    </div>
                    ${name   ? `<div class="call-agent"><i class="bi bi-person me-1"></i>${name}</div>` : ''}
                    ${reason ? `<div class="call-agent">${reason}</div>` : ''}
                    <div class="call-meta">
                        <div class="meta-item">
                            <span class="meta-label">Duración</span>
                            <span class="meta-value">${formatDuration(call.duration)}</span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Fecha</span>
                            <span class="meta-value">${date}</span>
                        </div>
                        ${sentiment ? `
                        <div class="meta-item">
                            <span class="meta-label">Sentimiento</span>
                            <span class="meta-value sentiment-${sentiment}">${sentiment}</span>
                        </div>` : ''}
                        ${call.judge_results?.appointment !== undefined ? `
                        <div class="meta-item">
                            <span class="meta-label">Cita</span>
                            <span class="meta-value">${call.judge_results.appointment === 'true' ? '✓ Agendada' : '✗ No agendada'}</span>
                        </div>` : ''}
                    </div>
                    ${summary ? `<div class="call-summary">${summary.slice(0, 120)}${summary.length > 120 ? '…' : ''}</div>` : ''}
                </div>
            </div>
        </div>`;
                        
        } else {
            return ``;
        }
        

    
    }).join('');
};

function deriveStatus(call) {
    if (call.status) return call.status;
    if (call.end_call_reason === 'human_goodbye' || call.end_call_reason === 'agent_goodbye') return 'Completada';
    if (call.end_call_reason === 'call_transfer') return 'Transferida';
    if (call.end_call_reason === 'voicemail_reached') return 'Buzón';
    if (call.end_call_reason === 'no_answer') return 'Sin respuesta';
    if (call.duration && Number(call.duration) > 0) return 'Completada';
    return 'Desconocido';
}

function formatDuration(seconds) {
    if (!seconds && seconds !== 0) return '—';
    const s = Number(seconds);
    const m = Math.floor(s / 60);
    const r = s % 60;
    return m > 0 ? `${m}m ${r}s` : `${r}s`;
}

function formatDate(dateStr) {
    if (!dateStr) return '—';
    try {
        return new Date(dateStr).toLocaleString('es-GT', {
            month: 'short', day: 'numeric',
            hour: '2-digit', minute: '2-digit'
        });
    } catch { return dateStr; }
}
