<?php

namespace App\Mixins;

use Closure;
use GuzzleHttp\Promise\Each;
use Illuminate\Http\Client\Pool;

class HttpMixin
{
    public function concurrent(): Closure
    {
        return function (
            int $concurrency,
            callable $requests,
            ?callable $onFulfilled = null,
            ?callable $onRejected = null
        ): void {
            /**
             * @var $this Factory
             */
            $requests = $requests(...)(new Pool($this));

            Each::ofLimit(
                $requests,
                $concurrency,
                $onFulfilled,
                $onRejected
            )->wait();
        };
    }
}
