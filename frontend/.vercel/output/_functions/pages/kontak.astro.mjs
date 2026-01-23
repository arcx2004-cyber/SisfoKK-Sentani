import { e as createComponent, k as renderComponent, r as renderTemplate, m as maybeRenderHead, u as unescapeHTML, h as addAttribute, l as Fragment } from '../chunks/astro/server_Qfva_YXD.mjs';
import 'piccolore';
import { $ as $$Layout } from '../chunks/Layout_Dp5cvDj6.mjs';
export { renderers } from '../renderers.mjs';

const prerender = false;
const $$Kontak = createComponent(async ($$result, $$props, $$slots) => {
  let contactData = {
    address: "Jl. Raya Sentani, Sentani, Jayapura, Papua 99352",
    phone: "(0967) 123456",
    whatsapp: "62967123456",
    email1: "info@sisfokk.sch.id",
    email2: "admin@sisfokk.sch.id",
    map_lat: "-2.5697",
    map_lng: "140.5047",
    operational_hours: "Senin - Jumat: 07:00 - 15:00 WIT, Sabtu: 07:00 - 12:00 WIT"
  };
  try {
    const response = await fetch("http://localhost:8001/api/v1/settings");
    const result = await response.json();
    if (result.success && result.data) {
      const s = result.data;
      contactData = {
        address: s.contact_address || contactData.address,
        phone: s.contact_phone || contactData.phone,
        whatsapp: s.contact_whatsapp || contactData.whatsapp,
        email1: s.contact_email1 || contactData.email1,
        email2: s.contact_email2 || contactData.email2,
        map_lat: s.contact_map_lat || contactData.map_lat,
        map_lng: s.contact_map_lng || contactData.map_lng,
        operational_hours: s.contact_operational_hours || contactData.operational_hours
      };
    }
  } catch (e) {
    console.error("Failed to fetch contact settings:", e);
  }
  const mapEmbedUrl = `https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3986.28!2d${contactData.map_lng}!3d${contactData.map_lat}!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zLocation!5e0!3m2!1sen!2sid!4v1704100000000!5m2!1sen!2sid`;
  const mapSearchUrl = `https://www.google.com/maps/search/${contactData.map_lat},${contactData.map_lng}`;
  return renderTemplate`${renderComponent($$result, "Layout", $$Layout, { "title": "Kontak" }, { "default": async ($$result2) => renderTemplate` ${maybeRenderHead()}<section class="hero" style="min-height: 300px;"> <div class="hero-content"> <span class="hero-badge">📞 Hubungi Kami</span> <h1 class="hero-title">Kontak</h1> <p class="hero-subtitle">Kami siap membantu menjawab pertanyaan Anda</p> </div> </section> <section class="section"> <div class="container"> <div class="grid grid-2" style="gap: 4rem;"> <div> <h2 class="section-title" style="text-align: left; margin-bottom: 2rem;">Informasi Kontak</h2> <div class="card" style="margin-bottom: 1.5rem;"> <h3 class="card-title">📍 Alamat</h3> <p class="card-desc">${unescapeHTML(contactData.address.replace(/,/g, "<br>"))}</p> </div> <div class="card" style="margin-bottom: 1.5rem;"> <h3 class="card-title">📞 Telepon</h3> <p class="card-desc"> <a${addAttribute(`tel:${contactData.phone}`, "href")} style="color: var(--primary-600);">${contactData.phone}</a> </p> </div> <div class="card" style="margin-bottom: 1.5rem;"> <h3 class="card-title">💬 WhatsApp</h3> <p class="card-desc"> <a${addAttribute(`https://wa.me/${contactData.whatsapp}`, "href")} target="_blank" rel="noopener" style="color: var(--success);">
+${contactData.whatsapp} </a> </p> </div> <div class="card" style="margin-bottom: 1.5rem;"> <h3 class="card-title">✉️ Email</h3> <p class="card-desc"> <a${addAttribute(`mailto:${contactData.email1}`, "href")} style="color: var(--primary-600);">${contactData.email1}</a> ${contactData.email2 && renderTemplate`${renderComponent($$result2, "Fragment", Fragment, {}, { "default": async ($$result3) => renderTemplate` <br> <a${addAttribute(`mailto:${contactData.email2}`, "href")} style="color: var(--primary-600);">${contactData.email2}</a> ` })}`} </p> </div> <div class="card"> <h3 class="card-title">⏰ Jam Operasional</h3> <p class="card-desc">${unescapeHTML(contactData.operational_hours.replace(/,/g, "<br>"))}</p> </div> </div> <div> <h2 class="section-title" style="text-align: left; margin-bottom: 2rem;">Kirim Pesan</h2> <form class="card" style="padding: 2rem;"> <div style="margin-bottom: 1.5rem;"> <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: var(--gray-700);">Nama Lengkap</label> <input type="text" placeholder="Masukkan nama lengkap" style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--gray-300); border-radius: var(--radius); font-size: 1rem;"> </div> <div style="margin-bottom: 1.5rem;"> <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: var(--gray-700);">Email</label> <input type="email" placeholder="email@example.com" style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--gray-300); border-radius: var(--radius); font-size: 1rem;"> </div> <div style="margin-bottom: 1.5rem;"> <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: var(--gray-700);">Subjek</label> <input type="text" placeholder="Tentang apa?" style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--gray-300); border-radius: var(--radius); font-size: 1rem;"> </div> <div style="margin-bottom: 1.5rem;"> <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: var(--gray-700);">Pesan</label> <textarea rows="5" placeholder="Tulis pesan Anda..." style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--gray-300); border-radius: var(--radius); font-size: 1rem; resize: vertical;"></textarea> </div> <button type="submit" class="btn btn-primary" style="width: 100%;">Kirim Pesan</button> </form> </div> </div> </div> </section>  <section class="section" style="padding-top: 0;"> <div class="container"> <div class="section-header"> <span class="section-badge">🗺️ Lokasi Kami</span> <h2 class="section-title">Temukan Kami di Peta</h2> <p style="color: var(--gray-600); max-width: 600px; margin: 0 auto;">
Sekolah Kristen Kalam Kudus Sentani berlokasi di pusat Kota Sentani, mudah dijangkau dari berbagai arah.
</p> </div> <div class="map-container" style="margin-top: 2rem;"> <iframe${addAttribute(mapEmbedUrl, "src")} width="100%" height="450" style="border:0; border-radius: var(--radius-lg); box-shadow: var(--shadow-lg);" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
        </iframe> </div> <div style="text-align: center; margin-top: 1.5rem;"> <a${addAttribute(mapSearchUrl, "href")} target="_blank" rel="noopener noreferrer" class="btn btn-outline" style="display: inline-flex; align-items: center; gap: 0.5rem;"> <span>📍</span> Buka di Google Maps
</a> </div> </div> </section> ` })}`;
}, "/home/arcx/SisfoKK Sentani/frontend/src/pages/kontak.astro", void 0);

const $$file = "/home/arcx/SisfoKK Sentani/frontend/src/pages/kontak.astro";
const $$url = "/kontak";

const _page = /*#__PURE__*/Object.freeze(/*#__PURE__*/Object.defineProperty({
  __proto__: null,
  default: $$Kontak,
  file: $$file,
  prerender,
  url: $$url
}, Symbol.toStringTag, { value: 'Module' }));

const page = () => _page;

export { page };
