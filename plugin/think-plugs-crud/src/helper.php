<?php

declare (strict_types=1);

if (!function_exists('formatdate')) {
    /**
     * 日期格式过滤
     * @param string|null $value
     * @return string|null
     */
    function formatdate(?string $value): ?string
    {
        return is_string($value) ? str_replace(['年', '月', '日'], ['-', '-', ''], $value) : $value;
    }
}