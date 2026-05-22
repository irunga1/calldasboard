export class Utilery {
    constructor(firstProp = 'default') {
        this.firstProp = firstProp;
    }

    getParams = () => {
        const currentUrl = window.location.href;
        let queryParts = currentUrl.split('?');
        if (queryParts.length <= 1) return [];
        queryParts = queryParts[1];
        const keyValueParts = queryParts.split('=');
        return `/${keyValueParts[1]}`;
    };

    getParamsSP = () => {
        const urlSearchParams = new URLSearchParams(window.location.search);
        if ([...urlSearchParams].length === 0) return [];
        return `/${urlSearchParams.values().next().value}`;
    };
}
