<?php

namespace DataDrivenDesign\VoiceDesigner;

class Asset
{
    public static function url($asset)
    {
        return plugins_url("wp/$asset}", __DIR__ . '/../voice-plugin.php');
    }
}
