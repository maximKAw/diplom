<?php
// functions.php

if (!function_exists('esc')) {
    /**
     * Экранирует вывод для предотвращения XSS
     * @param string $str
     * @return string
     */
    function esc($str) {
        return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
    }
}