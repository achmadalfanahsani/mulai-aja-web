<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Error Config
    |--------------------------------------------------------------------------
    */
    'default' => [
        'title'   => 'Oops.. You just found an error page..',
        'color'   => 'text-dark',
        'icon'    => 'fa fa-exclamation-circle',
        'message' => 'Something went wrong.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Error Pages Configuration
    |--------------------------------------------------------------------------
    */
    400 => [
        'title'   => 'Oops.. You just found an error page..',
        'color'   => 'text-warning',
        'icon'    => null,
        'message' => 'Your request contains bad syntax and cannot be fulfilled.',
    ],

    401 => [
        'title'   => 'Oops.. You just found an error page..',
        'color'   => 'text-info',
        'icon'    => 'fa fa-lock',
        'message' => 'You are not authorized to access this page.',
    ],

    403 => [
        'title'   => 'Oops.. You just found an error page..',
        'color'   => 'text-corporate',
        'icon'    => 'fa fa-ban',
        'message' => 'You do not have permission to access this page.',
    ],

    404 => [
        'title'   => 'Oops.. You just found an error page..',
        'color'   => 'text-danger',
        'icon'    => 'fa fa-exclamation-triangle',
        'message' => 'The page you are looking for was not found.',
    ],

    500 => [
        'title'   => 'Oops.. You just found an error page..',
        'color'   => 'text-flat',
        'icon'    => 'far fa-times-circle',
        'message' => 'Something went wrong on our server.',
    ],

    503 => [
        'title'   => 'Oops.. You just found an error page..',
        'color'   => 'text-elegance',
        'icon'    => 'fa fa-database',
        'message' => 'Our service is currently not available.',
    ],

];