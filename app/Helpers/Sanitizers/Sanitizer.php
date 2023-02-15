<?php

namespace App\Helpers\Sanitizers;

interface Sanitizer
{
    /**
     * Sanitize an input and return it.
     *
     * @param $input
     *
     * @return mixed
     */
    public function sanitize($input);
}
