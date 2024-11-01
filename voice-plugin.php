<?php

/*
Plugin Name: The Voice Designer by Data Driven Design
Plugin URI: https://thevoicedesigner.com
Description: Create your voice-based web site.
Version: 0.3.1
Author: Data Driven Design
Author URI: https://datadriven.design
License: MIT
*/

require_once __DIR__ . '/vendor/autoload.php';

use DataDrivenDesign\VoiceDesigner\AdminPage;
use DataDrivenDesign\VoiceDesigner\BackendApi;
use DataDrivenDesign\VoiceDesigner\Options\AppJson;
use DataDrivenDesign\VoiceDesigner\Options\SiteGuid;
use DataDrivenDesign\VoiceDesigner\Plugin;
use DataDrivenDesign\VoiceDesigner\RestRoutes;
use DataDrivenDesign\VoiceDesigner\VoiceContentMeta;

require_once __DIR__ . '/wp/editor-sidebar.php';

function ddd_voice_logger($log)
{
    if (!is_string($log)) {
        $log = print_r($log, true);
    }

    file_put_contents(__DIR__ . '/test.log', $log . PHP_EOL . PHP_EOL, FILE_APPEND);
}

register_activation_hook(__FILE__, [Plugin::class, 'activate']);
register_deactivation_hook(__FILE__, [Plugin::class, 'deactivate']);

add_action('init', [VoiceContentMeta::class, 'register']);
add_action('admin_enqueue_scripts', 'wp_enqueue_media');

register_post_meta('', 'ddd_voice_plugin_voice_content', [
    'show_in_rest' => true,
    'single' => true,
    'type' => 'string',
]);

AdminPage::setup();

AppJson::onUpdate(function ($app) {
    $logoUrl = $app['logo'];
    $logoFile = basename($logoUrl);
    [$logoFileNoHash] = explode('#', $logoFile);
    [$logoFileNoHashOrQuery] = explode('?', $logoFileNoHash);

    BackendApi::getInstance()->saveApp(
        SiteGuid::get(),
        [
            'app_name' => $app['app_name'],
            'invocation' => $app['invocation'],
            'license_key' => $app['license_key'],
            'logo' => [
                'name' => $logoFileNoHashOrQuery,
                'base64' => base64_encode(file_get_contents($logoUrl)),
            ],
            'nodes' => array_map(function ($node) {
                if ($node['type'] !== 'page') {
                    return $node;
                }

                return array_merge($node, ['type' => 'basic']);
            }, $app['nodes']),
        ]
    );
});

RestRoutes::register();
