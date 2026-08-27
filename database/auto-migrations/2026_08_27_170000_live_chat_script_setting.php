<?php
/**
 * Admin-managed live chat widget script (System Settings → Other).
 *
 * Default is empty on purpose: each site pastes its own embed in admin.
 * includes/livechat.php only loads whatever is stored in this setting —
 * it must never ship a site-specific widget key.
 */

return [
    'id' => '2026_08_27_live_chat_script_setting',
    'description' => 'Add live_chat_script system setting for injectable chat widgets',
    'up' => function ($db) {
        DatabaseAutoMigrate::ensureSetting(
            $db,
            'live_chat_script',
            '',
            'string',
            'Live chat widget script (paste full Tawk.to, Crisp, Smartsupp, etc. embed code). Shown on home, contact, help center, and customer dashboard/transfer pages.'
        );
    },
];
