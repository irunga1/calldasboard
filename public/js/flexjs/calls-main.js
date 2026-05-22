import { FlexJS }             from './core/Flex.js';
import { CallsModel }          from './models/CallsModel.js';
import { callsTable }          from './views/callsTable.js';
import { runBeforeRender }     from './helper/preRenderFn.js';
import { runPostRenderCalls }  from './helper/postRenderCalls.js';

// preRenderFn apunta a 'calls-container' pero en esta página el contenedor
// se llama 'calls-table-container', así que override inline:
const beforeRender = () => {
    const c = document.getElementById('calls-table-container');
    if (c) c.innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="text-muted mt-3 mb-0" style="font-size:.85rem;">Conectando con SynthFlow…</p>
        </div>`;
};

const apiUrl  = window.FLEX_API_URL || '/calldashboard/index.php/api/calls?limit=100';
const flexApp = new FlexJS(apiUrl);

flexApp.init(
    callsTable,
    runPostRenderCalls,
    'calls-table-container',
    CallsModel,
    beforeRender,
    false
);
