<?php
/**
 * Public site URLs without .php (requires Apache mod_rewrite — see /.htaccess).
 */

if (!function_exists('lusso_url')) {
    /**
     * Build a root-relative path: /rooms, /dining, /room-details?slug=...
     */
    function lusso_url(string $page, array $query = []): string {
        $page = preg_replace('/\.php$/i', '', trim($page, '/'));
        if ($page === '' || strcasecmp($page, 'index') === 0) {
            $path = '/';
        } else {
            $path = '/' . $page;
        }
        if (!empty($query)) {
            $path .= '?' . http_build_query($query);
        }
        return $path;
    }
}

if (!function_exists('lusso_href')) {
    /**
     * Normalize hrefs from CMS/settings: rooms.php → /rooms, index.php → /
     * Leaves http(s), mailto:, tel:, #, and already-clean /paths unchanged.
     */
    function lusso_href(string $href): string {
        $href = trim($href);
        if ($href === '' || $href === '#') {
            return $href;
        }
        if (preg_match('#^(https?:)?//#i', $href)) {
            return $href;
        }
        if (preg_match('#^(mailto:|tel:)#i', $href)) {
            return $href;
        }
        if ($href[0] === '/') {
            return $href;
        }
        if (preg_match('#^(?:\./)?index\.php(\?[^#]*)?(#.*)?$#i', $href, $m)) {
            $rest = ($m[1] ?? '') . ($m[2] ?? '');
            return $rest === '' ? '/' : '/' . ltrim($rest, '/');
        }
        if (preg_match('#^(?:\./)?([a-z0-9_-]+)\.php(\?[^#]*)?(#.*)?$#i', $href, $m)) {
            $base = $m[1];
            $tail = ($m[2] ?? '') . ($m[3] ?? '');
            if (strcasecmp($base, 'index') === 0) {
                return $tail === '' ? '/' : '/' . ltrim($tail, '/');
            }
            return '/' . $base . $tail;
        }
        return $href;
    }
}

if (!function_exists('lusso_site_href')) {
    /**
     * Absolute URL for admin previews / emails (uses SITE_URL if defined).
     */
    function lusso_site_href(string $pathOrHref): string {
        $path = lusso_href($pathOrHref);
        if (preg_match('#^https?://#i', $path)) {
            return $path;
        }
        $base = defined('SITE_URL') ? rtrim((string)SITE_URL, '/') : '';
        if ($path === '/' || $path === '') {
            return $base . '/';
        }
        return $base . $path;
    }
}
