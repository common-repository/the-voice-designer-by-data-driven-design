<?php

namespace DataDrivenDesign\VoiceDesigner;

class AdminPage
{
    public static function setup()
    {
        add_action('admin_menu', [self::class, 'menu']);
    }

    public static function menu()
    {
        add_menu_page(
            'The Voice Designer by Data Driven Design',
            'Voice Designer',
            'edit_posts',
            'ddd-voice',
            [self::class, 'render'],
            Asset::url('logo-icon.png'),
            58
        );
    }

    public static function render()
    {
        $scriptUrl = getenv('DDD_VOICE_JS_CLIENT_LIB') ?: 'https://cdn.thevoicedesigner.com/js/app.js';
        $stylesUrl = getenv('DDD_VOICE_JS_CLIENT_CSS') ?: 'https://cdn.thevoicedesigner.com/css/app.css';

        wp_enqueue_script('ddd-voice-vue-app', $scriptUrl, [], false, true);
        wp_enqueue_style('ddd-voice-vue-styles', $stylesUrl);
        ?>
        <div style="margin: 20px; margin-left: 0;">
            <h3>
                <img
                    src="<?php echo Asset::url('logo.png'); ?>"
                    alt="The Voice Designer"
                    height="100"
                >
            </h3>
            <div id="ddd-voice-vue-app"
                data-wp-rest-nonce="<?php echo wp_create_nonce('wp_rest'); ?>"
            >
                <div v-if="false">Loading...</div>
            </div>
        </div>
        <?php
    }
}
