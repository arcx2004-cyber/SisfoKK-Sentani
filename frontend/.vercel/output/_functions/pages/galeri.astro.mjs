import { e as createComponent, k as renderComponent, r as renderTemplate, m as maybeRenderHead } from '../chunks/astro/server_Qfva_YXD.mjs';
import 'piccolore';
import { $ as $$Layout } from '../chunks/Layout_Dp5cvDj6.mjs';
export { renderers } from '../renderers.mjs';

const $$Galeri = createComponent(($$result, $$props, $$slots) => {
  return renderTemplate`${renderComponent($$result, "Layout", $$Layout, { "title": "Galeri" }, { "default": ($$result2) => renderTemplate` ${maybeRenderHead()}<section class="hero" style="min-height: 300px;"> <div class="hero-content"> <span class="hero-badge">📷 Dokumentasi</span> <h1 class="hero-title">Galeri Foto</h1> <p class="hero-subtitle">Dokumentasi kegiatan dan momen berharga di Sekolah Kalam Kudus</p> </div> </section> <section class="section"> <div class="container"> <div class="section-header"> <span class="section-badge">Koleksi Foto</span> <h2 class="section-title">Album Galeri</h2> </div> <div class="grid grid-3"> <div class="card" style="padding: 0; overflow: hidden;"> <img src="https://images.unsplash.com/photo-1580582932707-520aed937b7b?w=400&h=250&fit=crop" alt="Kegiatan Belajar" style="width: 100%; height: 200px; object-fit: cover;"> <div style="padding: 1.5rem;"> <h3 class="card-title">Kegiatan Belajar Mengajar</h3> <p class="card-desc">Suasana pembelajaran di kelas dengan metode aktif dan interaktif</p> </div> </div> <div class="card" style="padding: 0; overflow: hidden;"> <img src="https://images.unsplash.com/photo-1577896851231-70ef18881754?w=400&h=250&fit=crop" alt="Upacara" style="width: 100%; height: 200px; object-fit: cover;"> <div style="padding: 1.5rem;"> <h3 class="card-title">Upacara Bendera</h3> <p class="card-desc">Pembinaan karakter kebangsaan melalui upacara rutin</p> </div> </div> <div class="card" style="padding: 0; overflow: hidden;"> <img src="https://images.unsplash.com/photo-1509062522246-3755977927d7?w=400&h=250&fit=crop" alt="Perayaan Natal" style="width: 100%; height: 200px; object-fit: cover;"> <div style="padding: 1.5rem;"> <h3 class="card-title">Perayaan Natal</h3> <p class="card-desc">Sukacita perayaan Natal bersama seluruh civitas akademika</p> </div> </div> <div class="card" style="padding: 0; overflow: hidden;"> <img src="https://images.unsplash.com/photo-1571260899304-425eee4c7efc?w=400&h=250&fit=crop" alt="Perpustakaan" style="width: 100%; height: 200px; object-fit: cover;"> <div style="padding: 1.5rem;"> <h3 class="card-title">Perpustakaan</h3> <p class="card-desc">Fasilitas perpustakaan dengan koleksi buku yang lengkap</p> </div> </div> <div class="card" style="padding: 0; overflow: hidden;"> <img src="https://images.unsplash.com/photo-1594608661623-aa0bd3a69d98?w=400&h=250&fit=crop" alt="Ekstrakurikuler" style="width: 100%; height: 200px; object-fit: cover;"> <div style="padding: 1.5rem;"> <h3 class="card-title">Kegiatan Ekstrakurikuler</h3> <p class="card-desc">Pengembangan bakat dan minat siswa melalui ekskul</p> </div> </div> <div class="card" style="padding: 0; overflow: hidden;"> <img src="https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=400&h=250&fit=crop" alt="Wisuda" style="width: 100%; height: 200px; object-fit: cover;"> <div style="padding: 1.5rem;"> <h3 class="card-title">Wisuda & Kelulusan</h3> <p class="card-desc">Momen kelulusan siswa-siswi berprestasi</p> </div> </div> </div> </div> </section> ` })}`;
}, "/home/arcx/SisfoKK Sentani/frontend/src/pages/galeri.astro", void 0);

const $$file = "/home/arcx/SisfoKK Sentani/frontend/src/pages/galeri.astro";
const $$url = "/galeri";

const _page = /*#__PURE__*/Object.freeze(/*#__PURE__*/Object.defineProperty({
  __proto__: null,
  default: $$Galeri,
  file: $$file,
  url: $$url
}, Symbol.toStringTag, { value: 'Module' }));

const page = () => _page;

export { page };
