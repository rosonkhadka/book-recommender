<?php

declare(strict_types=1);

namespace App\Supports;

class HandlePagination
{
    public function __invoke($perPage): int
    {
        return match (true) {
            is_numeric($perPage) && $perPage > 0 => (int)$perPage,
            'all' == $perPage => 0,
            default => 25,
        };
    }
}
