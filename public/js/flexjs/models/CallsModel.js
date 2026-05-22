import { ApiModule } from './ApiModule.js';

export class CallsModel extends ApiModule {
    constructor(apiUrl = '') {
        super(apiUrl);
    }

    async getData() {
        const response = await fetch(this.url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        if (!response.ok) return { results: [] };
        return response.json();
    }
}
