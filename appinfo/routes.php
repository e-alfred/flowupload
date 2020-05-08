<?php
return [
    /*'resources' => [
        'note' => ['url' => '/notes']
    ],*/
    'routes' => [
        ['name' => 'page#index', 'url' => '/', 'verb' => 'GET'],
        ['name' => 'upload#upload', 'url' => '/upload', 'verb' => 'GET']
    ]
];