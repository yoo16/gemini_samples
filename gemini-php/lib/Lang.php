<?php
class Lang
{
    public static $languages = [
        ['code' => 'ja-JP', 'name' => 'Japanese'],
        ['code' => 'en-US', 'name' => 'English'],
        ['code' => 'fr-FR', 'name' => 'French'],
        ['code' => 'es-ES', 'name' => 'Spanish'],
        ['code' => 'de-DE', 'name' => 'German'],
        ['code' => 'zh-CN', 'name' => 'Chinese'],
        ['code' => 'vi-VN', 'name' => 'Vietnamese'],
        ['code' => 'ko-KR', 'name' => 'Korian'],
    ];

    public static function getByCode($code)
    {
        foreach (self::$languages as $language) {
            if ($language['code'] === $code) {
                return $language['name'];
            }
        }
        return null;
    }
}
