import { e as createComponent, k as renderComponent, r as renderTemplate, m as maybeRenderHead } from '../chunks/astro/server_Qfva_YXD.mjs';
import 'piccolore';
import { $ as $$Layout } from '../chunks/Layout_Dp5cvDj6.mjs';
export { renderers } from '../renderers.mjs';

const $$Profile = createComponent(($$result, $$props, $$slots) => {
  return renderTemplate`${renderComponent($$result, "Layout", $$Layout, { "title": "Profile Sekolah" }, { "default": ($$result2) => renderTemplate`  ${maybeRenderHead()}<section class="hero" style="min-height: 300px;"> <div class="hero-content"> <span class="hero-badge">🏫 Tentang Kami</span> <h1 class="hero-title">Profile Sekolah</h1> <p class="hero-subtitle">Mengenal lebih dekat Sekolah Kristen Kalam Kudus Sentani</p> </div> </section> <section class="section"> <div class="container"> <div class="grid grid-2" style="gap: 4rem; align-items: center;"> <div> <span class="section-badge">Sejarah</span> <h2 class="section-title" style="text-align: left;">Tentang SKKK Sentani</h2> <p style="color: var(--gray-600); line-height: 1.8; margin-bottom: 1.5rem;">
Sekolah Kristen Kalam Kudus Sentani didirikan dengan visi untuk menyediakan pendidikan berkualitas 
            berbasis iman Kristiani di wilayah Sentani, Papua. Sejak berdiri, sekolah ini telah berkomitmen 
            untuk mendidik generasi muda dengan kasih dan disiplin.
</p> <p style="color: var(--gray-600); line-height: 1.8; margin-bottom: 1.5rem;">
Dengan motto <strong>"Dengan Kasih & Disiplin Meningkatkan Prestasi"</strong>, kami percaya bahwa 
            pendidikan yang baik harus dilandasi oleh kasih sayang sekaligus disiplin yang membangun karakter.
</p> <p style="color: var(--gray-600); line-height: 1.8;">
Saat ini, SKKK Sentani melayani tiga jenjang pendidikan: Taman Kanak-Kanak (TK), 
            Sekolah Dasar (SD), dan Sekolah Menengah Pertama (SMP), dengan ratusan siswa yang 
            aktif belajar setiap harinya.
</p> </div> <div> <img src="https://images.unsplash.com/photo-1580582932707-520aed937b7b?w=600&h=400&fit=crop" alt="Gedung Sekolah" style="width: 100%; border-radius: var(--radius-xl); box-shadow: var(--shadow-xl);"> </div> </div> </div> </section>  <section class="section" style="background: var(--gray-100);"> <div class="container"> <div class="section-header"> <span class="section-badge">Nilai-Nilai</span> <h2 class="section-title">Nilai-Nilai yang Kami Tanamkan</h2> </div> <div class="grid grid-4"> <div class="card" style="text-align: center;"> <div class="card-icon" style="margin: 0 auto 1rem;">✝️</div> <h3 class="card-title">Iman</h3> <p class="card-desc">Berpegang teguh pada iman Kristiani dalam setiap aspek pendidikan</p> </div> <div class="card" style="text-align: center;"> <div class="card-icon" style="margin: 0 auto 1rem;">❤️</div> <h3 class="card-title">Kasih</h3> <p class="card-desc">Mendidik dengan kasih yang tulus seperti kasih Kristus</p> </div> <div class="card" style="text-align: center;"> <div class="card-icon" style="margin: 0 auto 1rem;">📏</div> <h3 class="card-title">Disiplin</h3> <p class="card-desc">Membangun karakter melalui kedisiplinan yang konsisten</p> </div> <div class="card" style="text-align: center;"> <div class="card-icon" style="margin: 0 auto 1rem;">🏆</div> <h3 class="card-title">Prestasi</h3> <p class="card-desc">Mendorong siswa mencapai prestasi terbaik mereka</p> </div> </div> </div> </section> ` })}`;
}, "/home/arcx/SisfoKK Sentani/frontend/src/pages/profile.astro", void 0);

const $$file = "/home/arcx/SisfoKK Sentani/frontend/src/pages/profile.astro";
const $$url = "/profile";

const _page = /*#__PURE__*/Object.freeze(/*#__PURE__*/Object.defineProperty({
  __proto__: null,
  default: $$Profile,
  file: $$file,
  url: $$url
}, Symbol.toStringTag, { value: 'Module' }));

const page = () => _page;

export { page };
