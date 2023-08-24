import { ZiggyRouter } from '@/types/ziggy';
import { Config, RouteParam, RouteParamsWithQueryOverload } from 'ziggy-js';
import { config } from "@/utils/config";

export function getAppendableUri() {
    return location.pathname.replace(new RegExp(`^/${config('sharp.custom_url_segment')}/`), '');
}

export function route(
    name?: undefined,
    params?: RouteParamsWithQueryOverload | RouteParam,
    absolute?: boolean,
    config?: Config,
): ZiggyRouter;

export function route(
    name: string,
    params?: RouteParamsWithQueryOverload | RouteParam,
    absolute?: boolean,
    config?: Config,
): string;

export function route(name?: string, params?, absolute?, config?) {
    if(name && params.uri) {
        return (globalThis.route(name, { ...params, uri: 's-list/(uri)' }, absolute, config) as string)
            .replace(encodeURIComponent('s-list/(uri)'), params.uri);
    }
    return globalThis.route();
}

// export route;

