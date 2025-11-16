<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * Les URI pour lesquelles la vérification CSRF est désactivée.
     *
     * @var array<int, string>
     */
    protected $except = [
        // Tu peux ajouter ici les routes API que tu veux EXCLURE du CSRF
        'api/auth/*',
    ];
}
