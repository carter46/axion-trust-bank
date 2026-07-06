<?php
/**
 * Security Headers Helper
 * Prevents various web attacks by setting appropriate HTTP headers
 */

function setSecurityHeaders() {
    // Prevent clickjacking
    header('X-Frame-Options: DENY');
    
    // Prevent MIME type sniffing
    header('X-Content-Type-Options: nosniff');
    
    // Enable XSS filter (legacy browsers)
    header('X-XSS-Protection: 1; mode=block');
    
    // Content Security Policy
    // Adjust these policies based on your site's needs
    $csp = "default-src 'self'; ";
    $csp .= "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://www.google.com https://www.gstatic.com https://translate.google.com https://translate.googleapis.com https://*.googleapis.com https://*.smartsuppchat.com https://*.smartsuppcdn.com https://cdn.gtranslate.net; ";
    $csp .= "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://www.gstatic.com https://cdnjs.cloudflare.com https://*.smartsuppcdn.com; ";
    $csp .= "font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com https://*.smartsuppcdn.com data:; ";
    $csp .= "img-src 'self' data: https: blob: https://*.smartsuppcdn.com; ";
    $csp .= "media-src 'self' https://assets.mixkit.co https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://*.smartsuppcdn.com blob:; ";
    $csp .= "connect-src 'self' https://api.exchangerate-api.com https://cdn.jsdelivr.net https://*.smartsupp.com https://*.smartsuppchat.com https://*.smartsuppcdn.com https://cdn77.org wss://*.smartsupp.com wss://*.smartsuppchat.com https://translate.google.com https://translate.googleapis.com https://*.googleapis.com; ";
    $csp .= "frame-src 'self' https://*.smartsupp.com https://*.smartsuppchat.com https://translate.google.com; ";
    $csp .= "frame-ancestors 'none'; ";
    $csp .= "base-uri 'self'; ";
    $csp .= "form-action 'self'; ";
    $csp .= "upgrade-insecure-requests;";
    
    header("Content-Security-Policy: $csp");
    
    // HTTP Strict Transport Security (HSTS) - only if HTTPS
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
    }
    
    // Referrer Policy
    header('Referrer-Policy: strict-origin-when-cross-origin');
    
    // Permissions Policy (formerly Feature Policy)
    header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
    
    // Remove server information
    header_remove('X-Powered-By');
    header_remove('Server');
}

// Automatically set headers when file is included
if (!headers_sent()) {
    setSecurityHeaders();
}
