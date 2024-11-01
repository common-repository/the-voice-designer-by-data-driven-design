<?php

namespace DataDrivenDesign\VoiceDesigner;

class VoiceContentMeta
{
    private const NAME = 'ddd_voice_plugin_voice_content';

    public static function register()
    {
        register_post_meta('', self::NAME, [
            'show_in_rest' => true,
            'single' => true,
            'type' => 'string',
        ]);
    }

    public static function getFor($postId)
    {
        return get_post_meta($postId, self::NAME, true);
    }
}
