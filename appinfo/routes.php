<?php
return [
    'resources' => [
        'directory' => ['url' => '/directories']
    ],
    'routes' => [
        ['name' => 'page#index', 'url' => '/', 'verb' => 'GET'],
        ['name' => 'upload#checkChunk', 'url' => '/upload', 'verb' => 'GET'],
        ['name' => 'upload#upload', 'url' => '/upload', 'verb' => 'POST']
    ]
];