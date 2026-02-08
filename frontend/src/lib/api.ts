const API_BASE = import.meta.env.PUBLIC_API_URL || 'https://skkksentani.web.id/api/v1';

interface ApiResponse<T> {
    success: boolean;
    data: T;
    message?: string;
}

async function fetchApi<T>(endpoint: string): Promise<T | null> {
    try {
        const response = await fetch(API_BASE + endpoint);
        if (!response.ok) {
            console.error('API Error: ' + response.status + ' for ' + endpoint);
            return null;
        }
        const result: ApiResponse<T> = await response.json();
        return result.success ? result.data : null;
    } catch (error) {
        console.error('Failed to fetch ' + endpoint + ':', error);
        return null;
    }
}

export interface SchoolSettings { [key: string]: string; }
export interface MenuItem { id: number; title: string; url: string; order: number; children?: MenuItem[]; }
export interface Slider { id: number; title: string; subtitle?: string; image: string; button_text?: string; button_url?: string; is_active: boolean; }
export interface News { id: number; title: string; slug: string; excerpt?: string; content: string; image?: string; published_at: string; views: number; }
export interface Gallery { id: number; title: string; description?: string; cover_image?: string; photos: GalleryPhoto[]; }
export interface GalleryPhoto { id: number; image: string; caption?: string; }
export interface Kegiatan { id: number; title: string; slug: string; description?: string; content: string; image?: string; tanggal_mulai: string; tanggal_selesai?: string; lokasi?: string; }
export interface Unit { id: number; nama: string; kode: string; deskripsi?: string; sekilas?: string; kepala_sekolah?: string; foto_kepala_sekolah?: string; akreditasi?: string; is_active: boolean; urutan: number; gurus_count?: number; siswas_count?: number; }
export interface Page { id: number; title: string; slug: string; content: string; image?: string; is_active: boolean; }
export interface Document { id: number; title: string; file_path: string; type: string; description?: string; created_at: string; }
export interface Mading { id: number; title: string; content: string; image: string; author_name: string; published_at: string; }
export interface Prestasi { id: number; judul: string; jenis: string; keterangan?: string; tanggal: string; foto?: string; siswa: { nama_lengkap: string; unit?: { nama: string; }; }; }
export interface PaginatedResponse<T> { data: T[]; current_page: number; last_page: number; total: number; }

export async function fetchSettings(): Promise<SchoolSettings> { return await fetchApi<SchoolSettings>('/settings') || {}; }
export async function fetchMenus(): Promise<MenuItem[]> { return await fetchApi<MenuItem[]>('/menus') || []; }
export async function fetchSliders(): Promise<Slider[]> { return await fetchApi<Slider[]>('/sliders') || []; }
export async function fetchNews(limit?: number): Promise<News[]> { const endpoint = limit ? '/news?limit=' + limit : '/news'; return await fetchApi<News[]>(endpoint) || []; }
export async function fetchNewsDetail(slug: string): Promise<News | null> { return await fetchApi<News>('/news/' + slug); }
export async function fetchGalleries(): Promise<Gallery[]> { return await fetchApi<Gallery[]>('/galleries') || []; }
export async function fetchKegiatan(limit?: number): Promise<Kegiatan[]> { const endpoint = limit ? '/kegiatan?limit=' + limit : '/kegiatan'; return await fetchApi<Kegiatan[]>(endpoint) || []; }
export async function fetchKegiatanDetail(slug: string): Promise<Kegiatan | null> { return await fetchApi<Kegiatan>('/kegiatan/' + slug); }
export async function fetchUnits(): Promise<Unit[]> { return await fetchApi<Unit[]>('/units') || []; }
export async function fetchPage(slug: string): Promise<Page | null> { return await fetchApi<Page>('/pages/' + slug); }
export async function fetchUnitDetail(kode: string): Promise<Unit | null> { return await fetchApi<Unit>('/units/' + kode); }
export async function fetchDocuments(type: string = 'all'): Promise<Document[]> { const endpoint = type !== 'all' ? '/documents?type=' + type : '/documents'; return await fetchApi<Document[]>(endpoint) || []; }
export async function fetchMading(limit?: number): Promise<Mading[]> { return await fetchApi<Mading[]>(limit ? '/mading?limit=' + limit : '/mading') || []; }
export async function fetchMadingPaginated(limit?: number): Promise<Mading[]> { const res = await fetchApi<PaginatedResponse<Mading>>(limit ? '/mading?limit=' + limit : '/mading'); return res?.data || []; }
export async function fetchPrestasiPaginated(limit?: number): Promise<Prestasi[]> { const res = await fetchApi<PaginatedResponse<Prestasi>>(limit ? '/prestasi?limit=' + limit : '/prestasi'); return res?.data || []; }

export function formatDate(dateString: string): string { const date = new Date(dateString); return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }); }
export function getUnitIcon(kode: string): string { const icons: Record<string, string> = { 'TK': '????', 'SD': '????', 'SMP': '????' }; return icons[kode] || '????'; }
export function getImageUrl(path: string | undefined | null, fallback: string): string { if (!path) return fallback; if (path.startsWith('http')) return path; const baseUrl = 'https://skkksentani.web.id'; return baseUrl + '/storage/' + path; }

