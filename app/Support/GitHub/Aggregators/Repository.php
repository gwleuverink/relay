<?php

namespace App\Support\GitHub\Aggregators;

use App\Support\GitHub\GitHub;

class Repository
{
    public static function aggregate()
    {
        return cache()->remember(
            'repositories',
            now()->addMinutes(10),
            fn () => resolve(GitHub::class)->repos()
        );
    }
}
