import { e as createComponent, k as renderComponent, r as renderTemplate, m as maybeRenderHead } from '../chunks/astro/server_Qfva_YXD.mjs';
import 'piccolore';
import { $ as $$Layout } from '../chunks/Layout_Dp5cvDj6.mjs';
export { renderers } from '../renderers.mjs';

const $$VisiMisi = createComponent(($$result, $$props, $$slots) => {
  return renderTemplate`${renderComponent($$result, "Layout", $$Layout, { "title": "Visi & Misi" }, { "default": ($$result2) => renderTemplate` ${maybeRenderHead()}<section class="hero" style="min-height: 300px;"> <div class="hero-content"> <span class="hero-badge">🎯 Arah Pendidikan</span> <h1 class="hero-title">Visi & Misi</h1> <p class="hero-subtitle">Pedoman dalam mendidik generasi penerus bangsa</p> </div> </section>  <section class="section"> <div class="container"> <div style="max-width: 800px; margin: 0 auto; text-align: center;"> <span class="section-badge">Visi</span> <h2 class="section-title">Visi Sekolah</h2> <div class="card" style="margin-top: 2rem; padding: 3rem;"> <p style="font-size: 1.5rem; color: var(--primary-700); font-weight: 600; line-height: 1.6;">
"Menjadi sekolah Kristen yang unggul dalam iman, ilmu, dan karakter, 
            menghasilkan generasi yang takut akan Tuhan dan berdampak bagi masyarakat."
</p> </div> </div> </div> </section>  <section class="section" style="background: var(--gray-100);"> <div class="container"> <div class="section-header"> <span class="section-badge">Misi</span> <h2 class="section-title">Misi Sekolah</h2> </div> <div class="grid grid-2" style="max-width: 1000px; margin: 0 auto;"> <div class="card"> <div class="card-icon">1</div> <h3 class="card-title">Pendidikan Berbasis Iman</h3> <p class="card-desc">Menyelenggarakan pendidikan yang mengintegrasikan iman Kristiani dalam setiap aspek pembelajaran.</p> </div> <div class="card"> <div class="card-icon">2</div> <h3 class="card-title">Pengembangan Akademik</h3> <p class="card-desc">Melaksanakan pembelajaran berkualitas tinggi sesuai kurikulum nasional dan standar internasional.</p> </div> <div class="card"> <div class="card-icon">3</div> <h3 class="card-title">Pembentukan Karakter</h3> <p class="card-desc">Membentuk siswa yang berkarakter mulia, jujur, disiplin, dan bertanggung jawab.</p> </div> <div class="card"> <div class="card-icon">4</div> <h3 class="card-title">Pengembangan Bakat</h3> <p class="card-desc">Mengembangkan potensi dan bakat siswa melalui berbagai kegiatan ekstrakurikuler.</p> </div> <div class="card"> <div class="card-icon">5</div> <h3 class="card-title">Tenaga Pendidik Profesional</h3> <p class="card-desc">Menyediakan tenaga pendidik yang profesional, kompeten, dan berdedikasi.</p> </div> <div class="card"> <div class="card-icon">6</div> <h3 class="card-title">Fasilitas Pembelajaran</h3> <p class="card-desc">Menyediakan fasilitas dan lingkungan pembelajaran yang kondusif dan modern.</p> </div> </div> </div> </section>  <section class="cta"> <div class="container"> <span class="hero-badge" style="background: rgba(255,255,255,0.2);">Motto</span> <h2 class="cta-title" style="margin-top: 1rem;">"Dengan Kasih & Disiplin Meningkatkan Prestasi"</h2> <p class="cta-desc">Kasih yang tulus dan disiplin yang membangun adalah kunci untuk mencapai prestasi gemilang</p> </div> </section> ` })}`;
}, "/home/arcx/SisfoKK Sentani/frontend/src/pages/visi-misi.astro", void 0);

const $$file = "/home/arcx/SisfoKK Sentani/frontend/src/pages/visi-misi.astro";
const $$url = "/visi-misi";

const _page = /*#__PURE__*/Object.freeze(/*#__PURE__*/Object.defineProperty({
  __proto__: null,
  default: $$VisiMisi,
  file: $$file,
  url: $$url
}, Symbol.toStringTag, { value: 'Module' }));

const page = () => _page;

export { page };
