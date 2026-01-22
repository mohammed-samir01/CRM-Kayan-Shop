<?php

if (!function_exists('fix_arabic')) {
    function fix_arabic($text) {
        if (!$text) return $text;
        
        $arabic = new \ArPHP\I18N\Arabic();
        return $arabic->utf8Glyphs($text);
    }
}
