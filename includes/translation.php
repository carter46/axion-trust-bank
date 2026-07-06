<?php
/**
 * TRANSLATION WIDGET
 * 
 * GTranslate widget for multi-language support
 * Translates all content on the site
 * 
 * Note:
 * - Public/marketing pages keep their own `.gtranslate_wrapper` (do not change those layouts).
 * - App pages (logged-in) mount the widget ONLY inside `#settingsGTranslateMount` on
 *   `/profile/settings`, and keep it hidden elsewhere to prevent floating / duplicates.
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

// Ensure GTranslate handles multiple language switches properly
document.addEventListener('DOMContentLoaded', function() {
    const STORAGE_KEY = 'gt_selected_lang';
    const COOKIE_KEY = 'googtrans';

    // App pages render the sidebar wrapper (.dashboard-container). On those pages we must
    // NOT create a floating widget; the only allowed widget there is the settings mount.
    function isAppPage() {
        return !!document.querySelector('.dashboard-container');
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

    function ensureWrapperExists() {
        const settingsMount = document.getElementById('settingsGTranslateMount');
        if (settingsMount) {
            return settingsMount;
        }

        // On app pages with no settings mount, do not create any widget (avoid float).
        if (isAppPage()) {
            return null;
        }

        // Marketing/public + auth pages: ensure the floating wrapper exists.
        let wrapper = document.querySelector('.gtranslate_wrapper');
        if (!wrapper) {
            wrapper = document.createElement('div');
            wrapper.className = 'gtranslate_wrapper';
            document.body.appendChild(wrapper);
        }
        return wrapper;
    }

    function setCookie(name, value, days) {
        const maxAge = days * 24 * 60 * 60;
        document.cookie = `${name}=${value};path=/;max-age=${maxAge}`;
    }

    function getCookie(name) {
        const match = document.cookie.match(new RegExp('(?:^|; )' + name.replace(/[.$?*|{}()[\]\\/+^]/g, '\\$&') + '=([^;]*)'));
        return match ? decodeURIComponent(match[1]) : '';
    }

    function persistLanguage(langCode) {
        if (!langCode || langCode === 'en') {
            localStorage.setItem(STORAGE_KEY, 'en');
            setCookie(COOKIE_KEY, '/en/en', 365);
            return;
        }
        localStorage.setItem(STORAGE_KEY, langCode);
        setCookie(COOKIE_KEY, `/en/${langCode}`, 365);
    }

    function getPreferredLanguage() {
        const stored = localStorage.getItem(STORAGE_KEY);
        if (stored) {
            return stored;
        }

        const cookieValue = getCookie(COOKIE_KEY);
        if (cookieValue && cookieValue.indexOf('/en/') === 0) {
            return cookieValue.replace('/en/', '') || 'en';
        }
        return 'en';
    }

    function applySavedLanguage(langCode) {
        if (!langCode || langCode === 'en' || typeof window.doGTranslate !== 'function') {
            return;
        }
        try {
            window.doGTranslate(`en|${langCode}`);
        } catch (error) {
            console.error('[GTranslate] Failed to apply saved language:', error);
        }
    }

    const ensuredWrapper = ensureWrapperExists();
    // App pages without a settings mount: strip any stray floating widget GTranslate adds,
    // but still allow translation to apply via the saved-language cookie below.
    if (isAppPage() && !document.getElementById('settingsGTranslateMount')) {
        removeStrayFloats();
        setTimeout(removeStrayFloats, 400);
        setTimeout(removeStrayFloats, 1500);
    }

    // Wait for GTranslate to load
    function waitForGTranslate(callback, maxAttempts = 50) {
        let attempts = 0;
        const checkInterval = setInterval(function() {
            attempts++;
            if (window.gtranslate || window.doGTranslate) {
                clearInterval(checkInterval);
                if (callback) callback();
            } else if (attempts >= maxAttempts) {
                clearInterval(checkInterval);
                console.warn('[GTranslate] Widget did not load within expected time');
            }
        }, 100);
    }
    
    // Ensure widget is properly initialized
    waitForGTranslate(function() {
        const preferredLanguage = getPreferredLanguage();

        // Force widget refresh only when a visible wrapper exists
        if (window.gtranslate && typeof window.gtranslate.install === 'function') {
            try {
                const wrapper = document.querySelector('.gtranslate_wrapper');
                const onAppPage = isAppPage();
                const isSettingsMount = wrapper && wrapper.id === 'settingsGTranslateMount';
                if (wrapper && (!onAppPage || isSettingsMount)) {
                    if (!wrapper.querySelector('select')) {
                        window.gtranslate.install();
                    }
                }
            } catch(e) {
                console.log('[GTranslate] Reinstall check:', e);
            }
        }

        // Apply previously selected language across pages.
        applySavedLanguage(preferredLanguage);
        
        // Monitor for language changes and ensure widget stays functional
        let lastLanguage = preferredLanguage || 'en';
        
        // Check for language changes periodically
        setInterval(function() {
            const cookieValue = getCookie(COOKIE_KEY);
            let currentLanguage = 'en';
            if (cookieValue && cookieValue.indexOf('/en/') === 0) {
                currentLanguage = cookieValue.replace('/en/', '') || 'en';
            } else {
                currentLanguage = localStorage.getItem(STORAGE_KEY) || 'en';
            }

            if (currentLanguage !== lastLanguage) {
                lastLanguage = currentLanguage;
                persistLanguage(currentLanguage);
                // Language changed, ensure widget is still responsive
                const wrapper = document.querySelector('.gtranslate_wrapper');
                if (wrapper) {
                    const select = wrapper.querySelector('select');
                    if (select && select.disabled) {
                        select.disabled = false;
                    }
                }
            }
        }, 500);

        // Save language whenever the switcher changes.
        const wrapper = document.querySelector('.gtranslate_wrapper');
        const select = wrapper ? wrapper.querySelector('select') : null;
        if (select) {
            select.addEventListener('change', function() {
                persistLanguage(this.value || 'en');
            });
        }
    });
    
    // Prevent conflicts with other scripts
    if (window.doGTranslate) {
        const originalDoGTranslate = window.doGTranslate;
        window.doGTranslate = function() {
            try {
                return originalDoGTranslate.apply(this, arguments);
            } catch(e) {
                console.error('[GTranslate] Error during translation:', e);
                // Try to reinitialize
                if (window.gtranslate && typeof window.gtranslate.install === 'function') {
                    setTimeout(function() {
                        window.gtranslate.install();
                    }, 1000);
                }
            }
        };
    }
});
</script>
<script src="https://cdn.gtranslate.net/widgets/latest/dwf.js" defer></script>
