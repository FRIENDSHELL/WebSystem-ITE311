<?php

if (!function_exists('formatBytes')) {
    /**
     * Format bytes to human readable format
     *
     * @param int|float $size File size in bytes
     * @param int $precision Number of decimal places
     * @return string Formatted file size
     */
    function formatBytes($size, $precision = 2)
    {
        if (empty($size) || $size == 0 || $size == null || !is_numeric($size)) {
            return '0 B';
        }
        
        $size = (float)$size;
        if ($size < 0) {
            return '0 B';
        }
        
        $base = log($size, 1024);
        $suffixes = array('B', 'KB', 'MB', 'GB', 'TB');
        $index = floor($base);
        
        if ($index < 0) {
            $index = 0;
        }
        if ($index >= count($suffixes)) {
            $index = count($suffixes) - 1;
        }
        
        return round(pow(1024, $base - $index), $precision) . ' ' . $suffixes[$index];
    }
}

