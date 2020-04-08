<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'api/add-product',
        'api/add-productver',
        'api/edit-product',
        'api/edit-productver',
        'api/del-product',
        'api/del-productver',
    ];
}
