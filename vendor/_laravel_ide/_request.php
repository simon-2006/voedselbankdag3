<?php

namespace Illuminate\Http;

interface Request
{
    /**
     * @return \App\Models\Gebruiker|null
     */
    public function user($guard = null);
}