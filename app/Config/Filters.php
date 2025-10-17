<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Filters extends BaseConfig
{
    public array $aliases = [
        'csrf'     => \CodeIgniter\Filters\CSRF::class,
        'toolbar'  => \CodeIgniter\Filters\DebugToolbar::class,
        'honeypot' => \CodeIgniter\Filters\Honeypot::class,
        'auth'     => \App\Filters\AuthFilter::class,
        'noauth'   => \App\Filters\NoAuthFilter::class,
        'role'     => \App\Filters\RoleFilter::class,
        'roleauth' => \App\Filters\RoleAuth::class,
    ];

    public array $globals = [
        'before' => [
            // 'csrf', // enable later if needed
        ],
        'after' => [
            'toolbar',
        ],
    ];

    public array $methods = [];

    public array $filters = [];
}
