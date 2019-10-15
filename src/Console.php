<?php


namespace TranslationMergeTool;


class Console
{
    /**
     * @var App
     */
    static $appInstance;

    public static function setAppInstance(App $instance)
    {
        self::$appInstance = $instance;
    }

    public static function debug(string $text)
    {
        self::$appInstance->debug($text);
    }
}