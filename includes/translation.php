<?php
/**
 * TRANSLATION WIDGET
 * 
 * GTranslate widget for multi-language support
 * Translates all content on the site
 * 
 * Note:
 * - Public/marketing pages keep their own `.gtranslate_wrapper` (do not change those layouts).
 * - App pages (logged-in) and admin pages mount the widget ONLY inside `#settingsGTranslateMount` on
 *   `/profile/settings`, and keep it hidden elsewhere to prevent floating / duplicates.
 * - GTranslate keeps the chosen language in localStorage `__GT_TRANSLATE_LANGS` (per origin).
 *   The `site_lang` cookie mirrors it on the parent domain so the choice survives across
 *   subdomains (www / app / root); it is the source of truth and re-seeds localStorage on load.
 */

// Prevent duplicate widget/script injection when multiple layouts include this file.
if (defined('GTRANSLATE_WIDGET_INCLUDED')) {
    return;
}
define('GTRANSLATE_WIDGET_INCLUDED', true);

// GTranslate widget scripts
?>
<style>
/* Floating widget for marketing/public + auth pages (the ONLY floating widget).
   Keyed off the widget itself (NOT the body class) so it always shows on marketing
   pages whether the visitor is logged in or not. The settings-page mount is excluded. */
.gtranslate_wrapper:not(#settingsGTranslateMount) {
    position: fixed !important;
    bottom: 20px !important;
    left: 20px !important;
    z-index: 9998 !important;
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
    background: rgba(255, 255, 255, 0.95) !important;
    backdrop-filter: blur(10px) !important;
    border-radius: 8px !important;
    padding: 8px !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
    border: 1px solid rgba(0, 0, 0, 0.1) !important;
    min-width: 120px !important;
}

/* Settings page mount: inline (NOT floating), nudged down a little. */
#settingsGTranslateMount.gtranslate_wrapper {
    position: static !important;
    display: inline-block !important;
    visibility: visible !important;
    opacity: 1 !important;
    pointer-events: auto !important;
    bottom: auto !important;
    left: auto !important;
    right: auto !important;
    top: auto !important;
    transform: none !important;
    margin-top: 12px !important;
    background: rgba(255, 255, 255, 0.95) !important;
    backdrop-filter: blur(10px) !important;
    border-radius: 8px !important;
    padding: 8px !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12) !important;
    border: 1px solid rgba(0, 0, 0, 0.08) !important;
    min-width: 120px !important;
}

/* Ensure GTranslate dropdown is visible (both public + app mount) */
.gtranslate_wrapper select,
.gtranslate_wrapper .gt_container,
.gtranslate_wrapper .gt_select,
.gtranslate_wrapper .gt_current,
.gtranslate_wrapper .gt_flag {
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
}

/* Mobile responsive adjustments */
@media (max-width: 768px) {
    .gtranslate_wrapper:not(#settingsGTranslateMount) {
        bottom: 15px !important;
        left: 15px !important;
        padding: 6px !important;
        min-width: 100px !important;
        font-size: 14px !important;
    }
    #settingsGTranslateMount.gtranslate_wrapper {
        padding: 6px !important;
        min-width: 100px !important;
        font-size: 14px !important;
    }
}
</style>
<script>
window.gtranslateSettings = {
    "default_language": "en",
    // Disabled: this was auto-translating the site (e.g. to Khmer) without user action.
    "detect_browser_language": false,
    // Expanded language list with additional Asian languages.
    "languages": ["en", "ko", "zh-CN", "ja", "th", "id", "bn", "ur", "km", "es", "pt", "it", "tl", "ms", "vi", "ru", "fr", "de", "ar", "hi"],
    // GTranslate will mount into the first matching wrapper on the page.
    // - Marketing/public pages: `.gtranslate_wrapper` exists in their layout.
    // - App pages: only `#settingsGTranslateMount` should be visible.
    "wrapper_selector": ".gtranslate_wrapper",
    "flag_size": 24,
    "flag_style": "2d",
    "switcher_text_color": "#333333",
    "switcher_background_color": "#ffffff",
    "switcher_hover_background_color": "#f5f5f5",
    "switcher_open_direction": "down"
};

// Must run before dwf.js loads: it reads `__GT_TRANSLATE_LANGS` once at startup to pick the page language.
(function() {
    var GT_STORAGE_KEY = '__GT_TRANSLATE_LANGS';
    var LANG_COOKIE = 'site_lang';
    var DEFAULT_LANG = window.gtranslateSettings.default_language;
    var ALLOWED = window.gtranslateSettings.languages;

    function isAllowed(lang) {
        return typeof lang === 'string' && ALLOWED.indexOf(lang) !== -1;
    }

    function readCookie(name) {
        var match = document.cookie.match(new RegExp('(?:^|; )' + name + '=([^;]*)'));
        return match ? decodeURIComponent(match[1]) : '';
    }

    function cookieDomains() {
        var host = location.hostname;
        if (!host || /^[\d.]+$/.test(host) || host.indexOf('.') === -1) {
            return [];
        }
        var parts = host.split('.');
        var domains = [];
        for (var i = parts.length - 2; i >= 0; i--) {
            domains.push(parts.slice(i).join('.'));
        }
        return domains;
    }

    function expireCookie(name) {
        document.cookie = name + '=; path=/; max-age=0';
        cookieDomains().forEach(function(domain) {
            document.cookie = name + '=; path=/; max-age=0; domain=.' + domain;
        });
    }

    // Shortest parent domain the browser accepts (public suffixes like co.uk are rejected).
    function writeLangCookie(lang) {
        var attrs = '; path=/; max-age=' + (365 * 24 * 60 * 60) + '; SameSite=Lax';
        document.cookie = LANG_COOKIE + '=; path=/; max-age=0';
        var domains = cookieDomains();
        for (var i = 0; i < domains.length; i++) {
            document.cookie = LANG_COOKIE + '=' + lang + attrs + '; domain=.' + domains[i];
            if (readCookie(LANG_COOKIE) === lang) {
                return;
            }
        }
        document.cookie = LANG_COOKIE + '=' + lang + attrs;
    }

    function readStoredLang() {
        try {
            var stored = JSON.parse(localStorage.getItem(GT_STORAGE_KEY));
            if (stored && isAllowed(stored.tgtLang)) {
                return stored.tgtLang;
            }
        } catch (e) {}
        return DEFAULT_LANG;
    }

    function writeStoredLang(lang) {
        try {
            if (lang === DEFAULT_LANG) {
                localStorage.removeItem(GT_STORAGE_KEY);
            } else {
                localStorage.setItem(GT_STORAGE_KEY, JSON.stringify({ srcLang: DEFAULT_LANG, tgtLang: lang }));
            }
        } catch (e) {}
    }

    // Leftovers from the old Google-Translate-cookie integration; they re-applied stale languages.
    try { localStorage.removeItem('gt_selected_lang'); } catch (e) {}
    expireCookie('googtrans');

    var cookieLang = readCookie(LANG_COOKIE);
    if (isAllowed(cookieLang)) {
        writeStoredLang(cookieLang);
    } else {
        writeLangCookie(readStoredLang());
    }

    window.__siteLangRemember = function(lang) {
        if (isAllowed(lang)) {
            writeLangCookie(lang);
        }
    };
})();

(function(init) {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})(function() {
    // App pages render the sidebar wrapper (.dashboard-container); admin pages mark the body.
    // On those pages we must NOT create a floating widget; the only allowed widget there is
    // the settings mount. Translation still applies from the saved language.
    function isAppPage() {
        return !!document.querySelector('.dashboard-container') || document.body.classList.contains('admin-page');
    }

    function removeStrayFloats() {
        // Remove GTranslate's own auto-injected float UI and any floating wrapper
        // that is not the settings mount (prevents a stray widget on app pages).
        document.querySelectorAll('.gt_float_switcher, .gt_float_wrapper').forEach(function(node) {
            node.remove();
        });
        document.querySelectorAll('.gtranslate_wrapper').forEach(function(node) {
            if (node.id !== 'settingsGTranslateMount') {
                node.remove();
            }
        });
    }

    const hideWidget = isAppPage() && !document.getElementById('settingsGTranslateMount');
    if (hideWidget) {
        removeStrayFloats();
    } else if (!isAppPage() && !document.querySelector('.gtranslate_wrapper')) {
        const wrapper = document.createElement('div');
        wrapper.className = 'gtranslate_wrapper';
        document.body.appendChild(wrapper);
    }

    // The switcher calls the global doGTranslate at click time, so wrapping it after dwf.js
    // loads captures every language change.
    function wrapDoGTranslate() {
        if (typeof window.doGTranslate !== 'function' || window.doGTranslate.__siteLangWrapped) {
            return;
        }
        const originalDoGTranslate = window.doGTranslate;
        const wrapped = function(langPair) {
            const pair = langPair && langPair.value !== undefined ? langPair.value : langPair;
            if (typeof pair === 'string' && pair.indexOf('|') !== -1) {
                window.__siteLangRemember(pair.split('|')[1]);
            }
            try {
                return originalDoGTranslate.apply(this, arguments);
            } catch (e) {
                console.error('[GTranslate] Error during translation:', e);
            }
        };
        wrapped.__siteLangWrapped = true;
        window.doGTranslate = wrapped;
    }

    // dwf.js fills existing wrappers only once, when it runs, so load it after the wrapper exists.
    const script = document.createElement('script');
    script.src = 'https://cdn.gtranslate.net/widgets/latest/dwf.js';
    script.addEventListener('load', function() {
        wrapDoGTranslate();
        if (hideWidget) {
            removeStrayFloats();
            setTimeout(removeStrayFloats, 400);
            setTimeout(removeStrayFloats, 1500);
        }
    });
    document.body.appendChild(script);
});
</script>
