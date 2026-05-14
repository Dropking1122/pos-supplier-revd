<?php
namespace App\Http\Controllers;

use App\Models\Setting;

class PwaController extends Controller
{
    public function manifest()
    {
        $setting  = Setting::getSettings();
        $name     = $setting->company_name ?? config('app.name');

        $manifest = [
            'name'             => $name . ' — POS',
            'short_name'       => $name,
            'description'      => 'Sistem Point of Sale untuk ' . $name,
            'start_url'        => '/',
            'scope'            => '/',
            'display'          => 'standalone',
            'orientation'      => 'portrait-primary',
            'background_color' => '#0f172a',
            'theme_color'      => '#4f46e5',
            'lang'             => 'id',
            'categories'       => ['business', 'productivity'],
            'icons'            => [
                [
                    'src'     => '/pwa/icon/192',
                    'sizes'   => '192x192',
                    'type'    => 'image/png',
                    'purpose' => 'any maskable',
                ],
                [
                    'src'     => '/pwa/icon/512',
                    'sizes'   => '512x512',
                    'type'    => 'image/png',
                    'purpose' => 'any maskable',
                ],
            ],
            'shortcuts' => [
                [
                    'name'        => 'Transaksi Baru',
                    'short_name'  => 'Transaksi',
                    'description' => 'Buat transaksi penjualan baru',
                    'url'         => '/sales/create',
                    'icons'       => [['src' => '/pwa/icon/192', 'sizes' => '192x192']],
                ],
                [
                    'name'        => 'Riwayat Penjualan',
                    'short_name'  => 'Riwayat',
                    'description' => 'Lihat riwayat penjualan',
                    'url'         => '/sales',
                    'icons'       => [['src' => '/pwa/icon/192', 'sizes' => '192x192']],
                ],
                [
                    'name'        => 'Dashboard',
                    'short_name'  => 'Dashboard',
                    'description' => 'Lihat statistik penjualan',
                    'url'         => '/dashboard',
                    'icons'       => [['src' => '/pwa/icon/192', 'sizes' => '192x192']],
                ],
            ],
        ];

        return response()->json($manifest, 200, [
            'Content-Type' => 'application/manifest+json',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    public function icon(int $size)
    {
        if (!in_array($size, [192, 512], true)) {
            abort(404);
        }

        $cacheFile = storage_path("app/pwa/icon_{$size}.png");

        if (!file_exists($cacheFile)) {
            $this->generateIcon($size, $cacheFile);
        }

        return response()->file($cacheFile, [
            'Content-Type'  => 'image/png',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    private function generateIcon(int $size, string $path): void
    {
        $dir = dirname($path);
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        $img = imagecreatetruecolor($size, $size);
        imagealphablending($img, true);
        imagesavealpha($img, true);

        // Indigo background #4f46e5
        $bg = imagecolorallocate($img, 79, 70, 229);
        imagefill($img, 0, 0, $bg);

        // Rounded corners — paint transparent arcs over the four corners
        $r = (int)($size * 0.20);
        $transparent = imagecolorallocatealpha($img, 0, 0, 0, 127);
        imagealphablending($img, false);
        // top-left
        imagefilledrectangle($img, 0, 0, $r, $r, $transparent);
        imagefilledellipse($img, $r, $r, $r * 2, $r * 2, $bg);
        // top-right
        imagefilledrectangle($img, $size - $r, 0, $size, $r, $transparent);
        imagefilledellipse($img, $size - $r, $r, $r * 2, $r * 2, $bg);
        // bottom-left
        imagefilledrectangle($img, 0, $size - $r, $r, $size, $transparent);
        imagefilledellipse($img, $r, $size - $r, $r * 2, $r * 2, $bg);
        // bottom-right
        imagefilledrectangle($img, $size - $r, $size - $r, $size, $size, $transparent);
        imagefilledellipse($img, $size - $r, $size - $r, $r * 2, $r * 2, $bg);
        imagealphablending($img, true);

        // Lightning bolt ⚡ in white, centered
        $white = imagecolorallocate($img, 255, 255, 255);
        $s  = $size / 192.0;
        $cx = $size / 2;
        $cy = $size / 2;

        $points = [
            (int)($cx + 16 * $s),  (int)($cy - 50 * $s),
            (int)($cx - 4  * $s),  (int)($cy +  2 * $s),
            (int)($cx + 14 * $s),  (int)($cy +  2 * $s),
            (int)($cx - 16 * $s),  (int)($cy + 50 * $s),
            (int)($cx + 4  * $s),  (int)($cy -  2 * $s),
            (int)($cx - 14 * $s),  (int)($cy -  2 * $s),
        ];

        imagefilledpolygon($img, $points, $white);

        imagepng($img, $path);
        imagedestroy($img);
    }

    public function sw()
    {
        $version = 'v2-' . substr(md5(config('app.url', 'pos')), 0, 8);
        $js      = $this->buildServiceWorker($version);

        return response($js, 200, [
            'Content-Type'           => 'application/javascript',
            'Service-Worker-Allowed' => '/',
            'Cache-Control'          => 'no-cache, no-store, must-revalidate',
        ]);
    }

    private function buildServiceWorker(string $version): string
    {
        return <<<JS
const STATIC_CACHE = 'pos-static-{$version}';
const PAGE_CACHE   = 'pos-pages-{$version}';
const OFFLINE_URL  = '/offline';

// ── Install ────────────────────────────────────────────────
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(STATIC_CACHE)
            .then(c => c.add(OFFLINE_URL))
            .then(() => self.skipWaiting())
    );
});

// ── Activate — remove old caches ───────────────────────────
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys =>
            Promise.all(
                keys.filter(k => k !== STATIC_CACHE && k !== PAGE_CACHE)
                    .map(k => caches.delete(k))
            )
        ).then(() => self.clients.claim())
    );
});

// ── Fetch ──────────────────────────────────────────────────
self.addEventListener('fetch', event => {
    const req = event.request;
    const url = new URL(req.url);

    // Skip: non-GET, cross-origin, Livewire AJAX, mutations
    if (req.method !== 'GET')                   return;
    if (url.origin !== self.location.origin)    return;
    if (url.pathname.startsWith('/livewire/'))  return;
    if (url.pathname === '/logout')             return;
    if (url.pathname.startsWith('/backup/'))    return;
    if (url.pathname.startsWith('/reports/export')) return;
    if (url.pathname.startsWith('/reports/pdf'))    return;

    // Build assets & uploaded images — Cache First (hashed filenames)
    if (url.pathname.startsWith('/build/')  ||
        url.pathname.startsWith('/logos/')  ||
        url.pathname.startsWith('/pwa/')    ||
        url.pathname === '/manifest.json'   ||
        url.pathname === '/favicon.ico') {
        event.respondWith(cacheFirst(req, STATIC_CACHE));
        return;
    }

    // HTML navigation — Network First with offline fallback
    if (req.mode === 'navigate') {
        event.respondWith(networkFirstPage(req));
        return;
    }
});

// ── Strategies ─────────────────────────────────────────────
async function cacheFirst(req, cacheName) {
    const cached = await caches.match(req);
    if (cached) return cached;
    try {
        const res = await fetch(req);
        if (res.ok) (await caches.open(cacheName)).put(req, res.clone());
        return res;
    } catch {
        return new Response('Not found', { status: 404 });
    }
}

async function networkFirstPage(req) {
    try {
        const res = await fetch(req);
        if (res.ok) (await caches.open(PAGE_CACHE)).put(req, res.clone());
        return res;
    } catch {
        const cached = await caches.match(req);
        return cached || caches.match(OFFLINE_URL);
    }
}
JS;
    }

    public function offline()
    {
        return view('pwa.offline');
    }
}
