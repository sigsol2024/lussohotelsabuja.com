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

if (!function_exists('lusso_site_root')) {
    function lusso_site_root(): string {
        return defined('BASE_PATH') ? BASE_PATH : dirname(__DIR__);
    }
}

if (!function_exists('lusso_public_page_exists')) {
    /**
     * True when site root has a public entry script named {slug}.php (home = index).
     */
    function lusso_public_page_exists(string $slug): bool {
        $slug = strtolower($slug);
        if (!preg_match('/^[a-z0-9_-]+$/', $slug)) {
            return false;
        }
        $root = lusso_site_root();
        if ($slug === 'index') {
            return is_file($root . '/index.php');
        }
        return is_file($root . '/' . $slug . '.php');
    }
}

if (!function_exists('lusso_internal_nav_slug')) {
    /**
     * First path segment for an internal href, or "index" for home. Null if not applicable.
     */
    function lusso_internal_nav_slug(string $href): ?string {
        $h = lusso_href(trim($href));
        if ($h === '' || $h === '#') {
            return null;
        }
        if (preg_match('#^https?://#i', $h) || preg_match('#^(mailto:|tel:)#i', $h)) {
            return null;
        }
        if ($h[0] !== '/') {
            $h = lusso_href('/' . ltrim($h, '/'));
        }
        $path = parse_url($h, PHP_URL_PATH);
        if (!is_string($path)) {
            return null;
        }
        $path = trim($path, '/');
        if ($path === '') {
            return 'index';
        }
        $first = explode('/', $path)[0] ?? '';
        return $first !== '' ? strtolower($first) : null;
    }
}

if (!function_exists('lusso_is_valid_site_nav_href')) {
    /**
     * Use for header/footer: external http(s) URLs allowed; internal links only if a matching .php exists.
     */
    function lusso_is_valid_site_nav_href(string $href): bool {
        $h = trim($href);
        if ($h === '' || $h === '#') {
            return false;
        }
        if (preg_match('#^https?://#i', lusso_href($h))) {
            return true;
        }
        $slug = lusso_internal_nav_slug($h);
        return $slug !== null && lusso_public_page_exists($slug);
    }
}

if (!function_exists('lusso_media_src')) {
    /**
     * Full URL for a media path from the library (relative to site root) or an absolute http(s) URL.
     */
    function lusso_media_src(string $path): string {
        $path = trim($path);
        if ($path === '') {
            return '';
        }
        if (preg_match('#^https?://#i', $path)) {
            return $path;
        }
        $rel = '/' . ltrim($path, '/');
        if (defined('SITE_URL') && (string)SITE_URL !== '') {
            return rtrim((string)SITE_URL, '/') . $rel;
        }
        return $rel;
    }
}

if (!function_exists('lusso_brand_logo_path')) {
    /**
     * CMS path if set, otherwise use default file under site root when it exists (e.g. assets/images/logo/logo-dark.png).
     */
    function lusso_brand_logo_path(string $cmsPath, string $defaultRelativeFile): string {
        $cms = trim($cmsPath);
        if ($cms !== '') {
            return $cms;
        }
        $rel = ltrim($defaultRelativeFile, '/');
        if ($rel === '') {
            return '';
        }
        $full = lusso_site_root() . '/' . $rel;
        return is_file($full) ? $rel : '';
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
