<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'http://readingtohabit.develop.jp/add_favorite/*',
        'http://readingtohabit.develop.jp/delete_favorite/*',
    ];
}
