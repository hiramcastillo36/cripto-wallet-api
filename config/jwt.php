<?php

return [
    /*
    |--------------------------------------------------------------------------
    | JWT Authentication Secret
    |--------------------------------------------------------------------------
    |
    | Don't forget to set this, as it will be used to sign your tokens.
    | A great place for this string is environment variables.
    |
    */

    'secret' => env('JWT_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | JWT Authentication Public Key
    |--------------------------------------------------------------------------
    |
    | A path or resource to your public key.
    |
    | Uncomment and set this if you are using Asymmetric Algorithms (RS256, etc.)
    |
    */

    'public' => env('JWT_PUBLIC_KEY'),

    /*
    |--------------------------------------------------------------------------
    | JWT Authentication Private Key
    |--------------------------------------------------------------------------
    |
    | A path or resource to your private key.
    |
    | Uncomment and set this if you are using Asymmetric Algorithms (RS256, etc.)
    |
    */

    'private' => env('JWT_PRIVATE_KEY'),

    /*
    |--------------------------------------------------------------------------
    | JWT Authentication Algorithm
    |--------------------------------------------------------------------------
    |
    | The algorithm you want to use to sign the token. See here
    | to find a list of available algorithms.
    | Defaults to 'HS256'
    |
    */

    'algorithm' => env('JWT_ALGORITHM', 'HS256'),

    /*
    |--------------------------------------------------------------------------
    | JWT Authentication Time to Live
    |--------------------------------------------------------------------------
    |
    | Specify the length of time (in minutes) that the token will be valid for.
    | Defaults to 1 hour.
    |
    */

    'ttl' => env('JWT_TTL', 60),

    /*
    |--------------------------------------------------------------------------
    | JWT Authentication Refresh Time to Live
    |--------------------------------------------------------------------------
    |
    | Specify the length of time (in minutes) that the token can be refreshed
    | within. I.E. The user can refresh their token within this period even
    | if they have not used it. Doesn't make much sense if TTL is unlimited
    | Defaults to 2 weeks.
    |
    */

    'refresh_ttl' => env('JWT_REFRESH_TTL', 20160),

    /*
    |--------------------------------------------------------------------------
    | JWT hashKey Provider
    |--------------------------------------------------------------------------
    |
    | This will be run through hash() so that it generates a proper key.
    | Defaults to 'sha256'
    |
    */

    'hash_key' => env('JWT_HASH_ALGORITHM', 'sha256'),

    /*
    |--------------------------------------------------------------------------
    | Blacklist Storage
    |--------------------------------------------------------------------------
    |
    | options: predis, redis, tokens
    |
    */

    'blacklist_storage' => env('JWT_BLACKLIST_STORAGE', 'tokens'),

    /*
    |--------------------------------------------------------------------------
    | Blacklist Storage key
    |--------------------------------------------------------------------------
    |
    | Token, Db table or Redis key
    |
    */

    'blacklist_key' => env('JWT_BLACKLIST_KEY', 'jwt_blacklist'),

    /*
    |--------------------------------------------------------------------------
    | Cookies encryption
    |--------------------------------------------------------------------------
    |
    | By default, payload extracted from cookies will be encrypted with
    | encryption key. If you disable cookies, you should also disable
    | this for security reasons
    |
    */

    'decrypt_cookies' => false,

    /*
    |--------------------------------------------------------------------------
    | Leeway Time
    |--------------------------------------------------------------------------
    |
    | This property gives the jwt timestamp claims some leeway (in seconds)
    | to account for clock skew between signing and verifying servers.
    | Defaults to 0 seconds.
    |
    */

    'leeway' => env('JWT_LEEWAY', 0),

    /*
    |--------------------------------------------------------------------------
    | Requires Algorithms
    |--------------------------------------------------------------------------
    |
    | Specify the required_algorithms for parsing as a string value.
    | multiple algorithms comma separated: 'HS256,HS512'
    |
    */

    'required_claims' => ['iat', 'nbf', 'exp', 'sub', 'jti'],

    /*
    |--------------------------------------------------------------------------
    | Persistent Claims
    |--------------------------------------------------------------------------
    |
    | Add an array of persistent claims that will be added to the token
    | each time it is refreshed. Pass null to add nothing.
    | By default you don't need to do anything besides add `sub` to the traits.
    |
    */

    'persistent_claims' => [
        // 'foo' => 'bar',
    ],

    /*
    |--------------------------------------------------------------------------
    | Protected Routes
    |--------------------------------------------------------------------------
    |
    | Set a list of the authenticated routes that shall be protected.
    | you can add your routes to this array as you require and Laravel
    | will throw an exception if the route is called without a valid token.
    | this is not a tool to manage route protection middleware, it is just set
    | the request to have access to the payload if a token is present without
    | throwing up errors when it's not witthing this array.
    |
    */

    'protected_routes' => false,
];
