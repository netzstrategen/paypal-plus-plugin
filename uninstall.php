<?php

namespace WCPayPalPlus;

use WCPayPalPlus\Utils\NetworkState;
use WCPayPalPlus\Notice\DismissibleNoticeOption;

if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

$autoload = __DIR__ . '/vendor/autoload.php';

if (file_exists($autoload)) {
    /** @noinspection PhpIncludeInspection */
    require $autoload;
}
if (!class_exists(PayPalPlus::class)) {
    return;
}

$isMultisite = function_exists('get_sites') && is_multisite();

switch ($isMultisite) {
    case true:
        global $wpdb;

        $noticePrefix = DismissibleNoticeOption::OPTION_PREFIX;
        $wpdb->query("DELETE FROM {$wpdb->sitemeta} WHERE 'meta_key' LIKE '{$noticePrefix}%'");

        $sites = get_sites(['fields' => 'ids']);

        $networkState = NetworkState::create();
        foreach ($sites as $blogId) {
            switch_to_blog($blogId);
            delete_options();
        }
        $networkState->restore();
        break;
    default:
        delete_options();
        break;
}

wp_cache_flush();
