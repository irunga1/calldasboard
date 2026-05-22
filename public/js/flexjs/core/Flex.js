import { Utilery } from '../libs/Utilery.js';
import { SetUrl } from '../libs/SetURL.js';

window.SetUrl = SetUrl;

export class FlexJS {
    constructor(baseUrl = '', css = '', pageApi = 1) {
        const routeSuffix = this.getRouteSuffix();
        this.pageApi = pageApi;
        this.url = routeSuffix.length > 0 ? `${baseUrl}${routeSuffix}` : baseUrl;
        window.flexApp = this;
        if (css !== '') this.applyStylesheet(css);
    }

    getRouteSuffix = () => {
        const utility = new Utilery();
        return utility.getParamsSP();
    };

    init(renderView, afterRenderCallback, containerId = 'content', ModelClass, beforeRenderCallback, verMas = true) {
        this.viewConfig = { renderView, afterRenderCallback, containerId, ModelClass, beforeRenderCallback, verMas };
        document.addEventListener('DOMContentLoaded', async () => {
            this.blackout();
            const modelInstance = new ModelClass(this.url);
            await modelInstance.showData(renderView, afterRenderCallback, containerId, beforeRenderCallback, 'results');
            if (verMas) this.addBtnViewMore(4, containerId);
        });
    }

    init2(renderView, afterRenderCallback, containerId = 'content', ModelClass, beforeRenderCallback, url2, verMas = true) {
        this.viewConfig = { renderView, afterRenderCallback, containerId, ModelClass, beforeRenderCallback, verMas };
        const loadPage = async () => {
            this.url = url2;
            const currentUrl = new URL(url2, window.location.href);
            this.pageApi = Number(currentUrl.searchParams.get('page')) || this.pageApi;
            document.getElementById('flex-view-more')?.remove();
            this.blackout();
            const modelInstance = new ModelClass(url2);
            await modelInstance.showData(renderView, afterRenderCallback, containerId, beforeRenderCallback, 'results');
            if (verMas) this.addBtnViewMore(4, containerId);
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', loadPage);
            return;
        }
        loadPage();
    }

    applyStylesheet = (stylesheetName = 'page.css') => {
        const link = document.createElement('link');
        link.rel  = 'stylesheet';
        link.href = `./css/${stylesheetName}`;
        document.head.appendChild(link);
    };

    blackout(duration = 800) {
        document.getElementById('loading')?.remove();
        document.body.insertAdjacentHTML('beforeend', `
            <div id="loading" style="
                position:fixed;inset:0;background:#1e3a5f;color:rgba(255,255,255,.75);
                display:flex;align-items:center;justify-content:center;
                z-index:9999;font-size:.85rem;letter-spacing:.06em;
                opacity:1;transition:opacity .4s ease;">
                Cargando datos…
            </div>`);
        setTimeout(() => this.remove(), duration);
    }

    remove() {
        const el = document.getElementById('loading');
        if (!el) return;
        el.style.opacity = '0';
        setTimeout(() => el.remove(), 420);
    }

    addBtnViewMore = (intNumber = 0, strContent = 'container') => {
        intNumber = Number(this.pageApi) + 1;
        const url = new URL(this.url, window.location.href);
        url.searchParams.set('page', intNumber);
        document.getElementById('flex-view-more')?.remove();
        document.getElementById(strContent).insertAdjacentHTML(
            'afterend',
            `<button id="flex-view-more" onclick="SetUrl('${url.toString()}')">Cargar más</button>`
        );
    };
}
