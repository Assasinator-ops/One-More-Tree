<?php
/**
 * Plugin Name: One More Tree — Cache Headers
 * Description: Sends explicit Cache-Control headers for front-end HTML so any reverse proxy / host page cache / CDN in front of WordPress (e.g. a hosting platform's anonymous-page cache) follows our freshness policy instead of applying its own long default. Logged-in users always bypass caches; anonymous visitors get a short, well-defined freshness window so published edits reach them within a couple of minutes, not an hour.
 * Version: 1.0.0
 * Author: One More Tree
 *
 * Loaded as a must-use plugin so it cannot be deactivated by accident.
 *
 * Why this exists
 * ---------------
 * WordPress ships no Cache-Control header for anonymous front-end HTML. When such
 * a site lands behind a host-level page cache (TransIP hosting cache, Cloudflare
 * "Cache Everything", an office proxy, …), that layer can cache the page for its
 * own default lifetime and only refresh it after expiry. Because every full-page
 * cache bypasses visitors that carry a WordPress login cookie, editors see their
 * changes instantly (an authenticated browser) while incognito visitors see the
 * stale copy until the TTL passes — and the TTL may be an hour.
 *
 * The fix: always declare the policy explicitly.
 *  - Logged-in requests:  no-store (editors must never see a cached page).
 *  - Anonymous requests:  s-maxage=120 + stale-while-revalidate=300. Shared
 *    caches keep a copy for two minutes and may serve it stale while
 *    revalidating for five more; browsers must revalidate at once (max-age=0).
 *
 * @package OneMoreTree
 */

defined( 'ABSPATH' ) || exit;

const OMT_CACHE_SMAXAGE              = 120; // Shared/proxy cache lifetime for anonymous HTML.
const OMT_CACHE_STALE_WHILE_REVALIDATE = 300; // Grace period to serve a stale copy while revalidating.

if ( ! function_exists( 'omt_cache_headers_is_admin_request' ) ) {
    /**
     * True for wp-admin, WP-CLI, cron and REST — none of these should get HTML
     * page-cache hints from us (WordPress manages those responses itself).
     *
     * @return bool
     */
    function omt_cache_headers_is_admin_request() {
        return is_admin()
            || ( defined( 'WP_CLI' ) && WP_CLI )
            || ( defined( 'DOING_CRON' ) && DOING_CRON )
            || ( defined( 'REST_REQUEST' ) && REST_REQUEST )
            || ( defined( 'DOING_AJAX' ) && DOING_AJAX );
    }
}

if ( ! function_exists( 'omt_cache_headers_has_login_cookie' ) ) {
    /**
     * Does the request carry any WordPress auth/settings cookie? Full-page
     * caches typically bypass on the mere presence of the login cookie name,
     * so we treat it the same way — before it is even technically verified.
     *
     * @return bool
     */
    function omt_cache_headers_has_login_cookie() {
        foreach ( array_keys( $_COOKIE ) as $name ) {
            if ( 0 === strpos( (string) $name, 'wordpress_logged_in' ) || 0 === strpos( (string) $name, 'wp-settings' ) ) {
                return true;
            }
        }

        return false;
    }
}

if ( ! function_exists( 'omt_cache_headers_send' ) ) {
    /**
     * Send the cache policy for a front-end HTML response.
     *
     * @return void
     */
    function omt_cache_headers_send() {
        if ( omt_cache_headers_is_admin_request() ) {
            return;
        }

        if ( headers_sent() ) {
            return;
        }

        if ( is_user_logged_in() || omt_cache_headers_has_login_cookie() ) {
            // Editors and anyone with a login cookie: never, ever a cached copy.
            header( 'Cache-Control: no-store, no-cache, must-revalidate' );
            header( 'Pragma: no-cache' );
            return;
        }

        if ( is_feed() || is_embed() ) {
            // Feeds/embeds have their own conservative WordPress defaults.
            return;
        }

        if ( defined( 'REST_REQUEST' ) && REST_REQUEST || false !== strpos( $_SERVER['REQUEST_URI'] ?? '', '/wp-json/' ) ) {
            return; // JSON endpoints are not the tester-facing problem; leave untouched.
        }

        $age    = OMT_CACHE_SMAXAGE;
        $stale  = OMT_CACHE_STALE_WHILE_REVALIDATE;

        header( "Cache-Control: max-age=0, must-revalidate, s-maxage={$age}, stale-while-revalidate={$stale}" );
    }
}
add_action( 'send_headers', 'omt_cache_headers_send', 1 );
