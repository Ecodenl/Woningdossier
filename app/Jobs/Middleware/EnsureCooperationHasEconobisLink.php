<?php

namespace App\Jobs\Middleware;

use App\Models\Cooperation;

class EnsureCooperationHasEconobisLink
{
    public Cooperation $cooperation;

    public function __construct(Cooperation $cooperation)
    {
        $this->cooperation = $cooperation;
    }

    /**
     * Process the job.
     *
     * @param  mixed  $job
     * @param  callable  $next
     * @return mixed
     */
    public function handle($job, $next)
    {
        $wildcard = $this->cooperation->econobis_wildcard;
        $apiKey = $this->cooperation->econobis_api_key;

        // When one is null, just use the test environment.
        if (app()->environment('production') && (empty($wildcard) || empty($apiKey))) {
            return;
        } else {
            $next($job);
        }
    }
}