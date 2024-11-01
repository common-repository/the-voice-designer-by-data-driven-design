<?php

use DataDrivenDesign\VoiceDesigner\Asset;
use DataDrivenDesign\VoiceDesigner\Options\AppJson;
use DataDrivenDesign\VoiceDesigner\VoiceContentMeta;

function ddd_voice_init_editor_sidebar()
{
    wp_register_script(
        'ddd-voice-sidebar-js',
        Asset::url('editor-sidebar.js'),
        [
            'wp-plugins',
            'wp-edit-post',
            'wp-element',
            'wp-components',
            'wp-data',
        ]
    );

    wp_register_style(
        'ddd-voice-sidebar-css',
        Asset::url('editor-sidebar.css')
    );
}
add_action('init', 'ddd_voice_init_editor_sidebar');

function ddd_voice_add_sidebar_js()
{
    wp_enqueue_script('ddd-voice-sidebar-js');
    wp_localize_script('ddd-voice-sidebar-js', 'DddVoiceDesigner', array(
        'logoUrl' => Asset::url('logo-icon-lg.png'),
    ));
}
add_action('enqueue_block_editor_assets', 'ddd_voice_add_sidebar_js');

function ddd_voice_add_sidebar_css()
{
    wp_enqueue_style('ddd-voice-sidebar-css');
}
add_action('enqueue_block_assets', 'ddd_voice_add_sidebar_css');

function ddd_voice_update_voice_app($postId, $field, $fieldValueCallback)
{
    $app = get_option(AppJson::getName()) ?: null;

    if (! $app) {
        return;
    }

    $shouldSave = false;
    foreach (($app['nodes'] ?? []) as $index => $node) {
        if ($node['type'] !== 'page' || ($node['postId'] ?? null) !== $postId) {
            continue;
        }

        $shouldSave = true;
        $app['nodes'][$index][$field] = $fieldValueCallback($postId);
    }

    if ($shouldSave) {
        update_option(AppJson::getName(), $app);
    }
}
function ddd_voice_update_site_option_on_post_saved($postId)
{
    ddd_voice_update_voice_app($postId, 'title', function ($postId) {
        return get_the_title($postId);
    });
}
add_action('save_post', 'ddd_voice_update_site_option_on_post_saved', 999, 1);
function ddd_voice_update_site_option_on_metadata_saved($_, $postId)
{
    ddd_voice_update_voice_app($postId, 'speech', function ($postId) {
        return VoiceContentMeta::getFor($postId);
    });
}
add_action('updated_postmeta', 'ddd_voice_update_site_option_on_metadata_saved', 999, 2);
