<?php
/**
 * Lusso shared <head> assets
 * Include inside <head> on every frontend page.
 */

static $lussoHeadLoaded = false;
if ($lussoHeadLoaded) {
    return;
}
$lussoHeadLoaded = true;
?>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com" rel="preconnect"/>
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400&amp;family=Plus+Jakarta+Sans:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet"/>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<script id="tailwind-config">
  tailwind.config = {
    darkMode: "class",
    theme: {
      extend: {
        colors: {
          "primary": "#411d13",
          "primary-light": "#5a2a1f",
          "background-light": "#efe8d6",
          "background-dark": "#1a1210",
          "champagne": "#f5ede0",
          "text-main": "#363636",
          "text-muted": "#5c5c5c",
          "surface-light": "#ffffff",
          "surface-dark": "#2a1f1c",
          "surface-ink": "#2a1814",
          "sand-darker": "#e3dcc8",
        },
        fontFamily: {
          "display": ["Plus Jakarta Sans", "sans-serif"],
          "serif": ["Playfair Display", "serif"],
          "body": ["Noto Sans", "sans-serif"],
        },
        borderRadius: {
          "DEFAULT": "0.25rem",
          "lg": "0.5rem",
          "xl": "0.75rem",
          "2xl": "1rem",
          "full": "9999px",
        },
        boxShadow: {
          "elevation": "0 20px 40px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01)",
        },
        backgroundImage: {
          "texture-pattern": "url(\"data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23411d13' fill-opacity='0.06'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E\")",
          "fabric-pattern": "url(\"data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23363636' fill-opacity='0.08'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E\")",
          "architectural-pattern": "url(\"data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23363636' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E\")",
          "subtle-pattern": "url(\"data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23411d13' fill-opacity='0.06'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E\")",
        }
      },
    },
  }
</script>
<style>
  html { scroll-behavior: smooth; }
  .no-scrollbar::-webkit-scrollbar { display: none; }
  .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
  .text-cinematic { text-shadow: 0 2px 10px rgba(0,0,0,0.3); }

  /* Homepage booking bridge: tames common embeds (e.g. StayEazi-style #booking-widget / #booking-form) */
  #booking-lusso {
    width: 100% !important;
  }
  #booking-lusso * {
    box-sizing: border-box;
  }
  #booking-lusso #booking-widget {
    margin: 0 !important;
    padding: 0 !important;
    border: 0 !important;
    box-shadow: none !important;
    border-radius: 0 !important;
    background: transparent !important;
    max-width: none !important;
    width: 100% !important;
  }
  #booking-lusso #booking-form {
    display: flex !important;
    flex-wrap: wrap !important;
    gap: 8px !important;
    align-items: flex-end !important;
    justify-content: space-between !important;
    padding: 0 !important;
    margin: 0 !important;
    width: 100% !important;
  }
  #booking-lusso #booking-form > div {
    width: auto !important;
    min-width: 160px !important;
    flex: 1 1 160px !important;
    margin: 0 !important;
  }
  #booking-lusso #booking-form label {
    font-size: 11px !important;
    font-weight: 700 !important;
    letter-spacing: 0.08em !important;
    text-transform: uppercase !important;
    margin-bottom: 6px !important;
    color: #363636 !important;
  }
  #booking-lusso #booking-form input,
  #booking-lusso #booking-form select {
    width: 100% !important;
    height: 44px !important;
    padding: 10px 12px !important;
    border: 1px solid #d8d0bc !important;
    border-radius: 10px !important;
    background: #fff !important;
    color: #363636 !important;
  }
  #booking-lusso #booking-form button {
    width: 100% !important;
    height: 44px !important;
    margin-top: 0 !important;
    border: 0 !important;
    border-radius: 10px !important;
    background: #411d13 !important;
    color: #fff !important;
    font-weight: 700 !important;
    cursor: pointer !important;
  }
  #booking-lusso #booking-form button:hover {
    background: #5a2a1f !important;
  }
  @media (max-width: 1024px) {
    #booking-lusso #booking-form {
      flex-direction: column !important;
      align-items: stretch !important;
    }
    #booking-lusso #booking-form > div {
      min-width: 100% !important;
      flex: 1 1 100% !important;
    }
  }
</style>
<?php
// Optional: site-wide injected header scripts (analytics etc)
if (function_exists('getSiteSetting')) {
    $headerScripts = getSiteSetting('header_scripts', '');
    if (!empty($headerScripts)) {
        echo "\n<!-- Custom Header Scripts -->\n";
        echo $headerScripts . "\n";
    }
}
?>

