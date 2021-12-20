<?php

use Illuminate\Support\Env;

return [
    'users' => collect([
        [env('BASE_AUTH_USERNAME'), env('BASE_AUTH_PASSWORD')],
    ]),
];