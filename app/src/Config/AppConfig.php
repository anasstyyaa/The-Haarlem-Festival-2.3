<?php

namespace App\Config;

class AppConfig 
{
    public static function getBaseUrl(): string 
    {
        // checks if we are on HTTPS
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
        
        // gets the host name, defaults to 'localhost' if not set
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        
        return "{$protocol}://{$host}";
    }
}