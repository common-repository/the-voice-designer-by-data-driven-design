<?php

namespace DataDrivenDesign\VoiceDesigner;

use DataDrivenDesign\VoiceDesigner\Options\AppJson;
use DataDrivenDesign\VoiceDesigner\Options\SiteGuid;

class Plugin
{
    public static function activate()
    {
        if (!empty(SiteGuid::get())) {
            return;
        }

        $guid = BackendApi::getInstance()->createApp();
        SiteGuid::set($guid);
    }

    public static function deactivate()
    {
        AppJson::delete();
    }
}
