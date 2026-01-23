import { e as createComponent, k as renderComponent, r as renderTemplate, m as maybeRenderHead } from '../chunks/astro/server_Qfva_YXD.mjs';
import 'piccolore';
import { $ as $$Layout } from '../chunks/Layout_Dp5cvDj6.mjs';
export { renderers } from '../renderers.mjs';

const $$Berita = createComponent(($$result, $$props, $$slots) => {
  return renderTemplate`${renderComponent($$result, "Layout", $$Layout, { "title": "Berita" }, { "default": ($$result2) => renderTemplate` ${maybeRenderHead()}<section class="hero" style="min-height: 300px;"> <div class="hero-content"> <span class="hero-badge">📰 Informasi</span> <h1 class="hero-title">Berita Sekolah</h1> <p class="hero-subtitle">Kabar terbaru seputar kegiatan dan prestasi Sekolah Kalam Kudus</p> </div> </section> <section class="section"> <div class="container"> <div class="grid grid-3"> <article class="news-card"> <img src="https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=400&h=200&fit=crop" alt="PPDB"> <div class="news-card-content"> <span class="news-card-date">12 Januari 2026</span> <h3 class="news-card-title">PPDB 2026/2027 Resmi Dibuka</h3> <p class="news-card-excerpt">Pendaftaran Peserta Didik Baru untuk tahun ajaran 2026/2027 telah resmi dibuka. Segera daftarkan putra-putri Anda untuk bergabung bersama keluarga besar Kalam Kudus Sentani.</p> </div> </article> <article class="news-card"> <img src="https://images.unsplash.com/photo-1577896851231-70ef18881754?w=400&h=200&fit=crop" alt="Olimpiade"> <div class="news-card-content"> <span class="news-card-date">8 Januari 2026</span> <h3 class="news-card-title">Siswa Raih Medali Olimpiade Sains</h3> <p class="news-card-excerpt">Siswa SMP Kalam Kudus berhasil meraih medali emas dalam Olimpiade Sains Nasional tingkat provinsi Papua. Prestasi gemilang ini membanggakan sekolah.</p> </div> </article> <article class="news-card"> <img src="https://images.unsplash.com/photo-1509062522246-3755977927d7?w=400&h=200&fit=crop" alt="Natal"> <div class="news-card-content"> <span class="news-card-date">25 Desember 2025</span> <h3 class="news-card-title">Perayaan Natal Bersama</h3> <p class="news-card-excerpt">Seluruh civitas akademika merayakan Natal bersama dengan penuh sukacita. Acara diisi dengan ibadah, pentas seni, dan tukar kado.</p> </div> </article> <article class="news-card"> <img src="https://images.unsplash.com/photo-1580582932707-520aed937b7b?w=400&h=200&fit=crop" alt="Guru"> <div class="news-card-content"> <span class="news-card-date">25 November 2025</span> <h3 class="news-card-title">Pelatihan Guru Kurikulum Merdeka</h3> <p class="news-card-excerpt">Para guru mengikuti pelatihan intensif implementasi Kurikulum Merdeka untuk meningkatkan kualitas pembelajaran di sekolah.</p> </div> </article> <article class="news-card"> <img src="https://images.unsplash.com/photo-1594608661623-aa0bd3a69d98?w=400&h=200&fit=crop" alt="Ekstrakurikuler"> <div class="news-card-content"> <span class="news-card-date">15 November 2025</span> <h3 class="news-card-title">Penambahan Ekstrakurikuler Baru</h3> <p class="news-card-excerpt">Sekolah menambah beberapa kegiatan ekstrakurikuler baru seperti robotika, coding, dan musik modern untuk pengembangan bakat siswa.</p> </div> </article> <article class="news-card"> <img src="https://images.unsplash.com/photo-1571260899304-425eee4c7efc?w=400&h=200&fit=crop" alt="Perpustakaan"> <div class="news-card-content"> <span class="news-card-date">1 November 2025</span> <h3 class="news-card-title">Renovasi Perpustakaan Selesai</h3> <p class="news-card-excerpt">Perpustakaan sekolah telah selesai direnovasi dengan fasilitas baru termasuk ruang baca digital dan koleksi buku yang diperbanyak.</p> </div> </article> </div> </div> </section> ` })}`;
}, "/home/arcx/SisfoKK Sentani/frontend/src/pages/berita.astro", void 0);

const $$file = "/home/arcx/SisfoKK Sentani/frontend/src/pages/berita.astro";
const $$url = "/berita";

const _page = /*#__PURE__*/Object.freeze(/*#__PURE__*/Object.defineProperty({
  __proto__: null,
  default: $$Berita,
  file: $$file,
  url: $$url
}, Symbol.toStringTag, { value: 'Module' }));

const page = () => _page;

export { page };
