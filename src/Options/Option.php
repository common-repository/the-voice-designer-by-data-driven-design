<?php

namespace DataDrivenDesign\VoiceDesigner\Options;

abstract class Option
{
    abstract public static function getName();

    public static function get()
    {
        return get_option(static::getName());
    }

    public static function set($value)
    {
        return update_option(static::getName(), $value);
    }

    public static function delete()
    {
        return delete_option(static::getName());
    }

    public static function onUpdate($callable)
    {
        $optionName = static::getName();

        add_action('updated_option', function ($option, $_, $value) use ($callable, $optionName) {
            if ($option === $optionName) {
                call_user_func_array($callable, [$value]);
            }
        }, 999, 3);
    }
}
