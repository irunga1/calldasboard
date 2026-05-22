export class ApiModule {
    constructor(apiUrl = '') {
        this.url = apiUrl;
    }

    async getData() {
        const response = await fetch(this.url);
        return response.json();
    }

    async showData(renderView, afterRenderCallback, containerId = 'container', beforeRenderCallback = () => {}, dataKey = 'results') {
        const responseData = await this.getData();
        beforeRenderCallback();

        if (dataKey && Object.prototype.hasOwnProperty.call(responseData, dataKey)) {
            responseData.results = responseData[dataKey];
        } else if (typeof responseData.results === 'undefined') {
            responseData.results = responseData;
        }

        renderView(responseData.results, containerId);
        afterRenderCallback();
    }
}
