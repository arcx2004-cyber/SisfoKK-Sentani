import { renderers } from './renderers.mjs';
import { c as createExports, s as serverEntrypointModule } from './chunks/_@astrojs-ssr-adapter_CpGF4P9O.mjs';
import { manifest } from './manifest_Cbr-Mb6G.mjs';

const serverIslandMap = new Map();;

const _page0 = () => import('./pages/_image.astro.mjs');
const _page1 = () => import('./pages/berita.astro.mjs');
const _page2 = () => import('./pages/galeri.astro.mjs');
const _page3 = () => import('./pages/kegiatan.astro.mjs');
const _page4 = () => import('./pages/kontak.astro.mjs');
const _page5 = () => import('./pages/ppdb.astro.mjs');
const _page6 = () => import('./pages/profile.astro.mjs');
const _page7 = () => import('./pages/unit/_kode_.astro.mjs');
const _page8 = () => import('./pages/unit.astro.mjs');
const _page9 = () => import('./pages/visi-misi.astro.mjs');
const _page10 = () => import('./pages/index.astro.mjs');
const pageMap = new Map([
    ["node_modules/astro/dist/assets/endpoint/generic.js", _page0],
    ["src/pages/berita.astro", _page1],
    ["src/pages/galeri.astro", _page2],
    ["src/pages/kegiatan.astro", _page3],
    ["src/pages/kontak.astro", _page4],
    ["src/pages/ppdb/index.astro", _page5],
    ["src/pages/profile.astro", _page6],
    ["src/pages/unit/[kode].astro", _page7],
    ["src/pages/unit.astro", _page8],
    ["src/pages/visi-misi.astro", _page9],
    ["src/pages/index.astro", _page10]
]);

const _manifest = Object.assign(manifest, {
    pageMap,
    serverIslandMap,
    renderers,
    actions: () => import('./noop-entrypoint.mjs'),
    middleware: () => import('./_noop-middleware.mjs')
});
const _args = {
    "middlewareSecret": "42bc255c-61eb-4fa0-ad0c-5385c788f090",
    "skewProtection": false
};
const _exports = createExports(_manifest, _args);
const __astrojsSsrVirtualEntry = _exports.default;
const _start = 'start';
if (Object.prototype.hasOwnProperty.call(serverEntrypointModule, _start)) ;

export { __astrojsSsrVirtualEntry as default, pageMap };
