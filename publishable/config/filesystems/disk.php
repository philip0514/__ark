<?php

return [
    'upload'    =>  [
        'driver' => 'local',
        'root'   => public_path() . '/upload',
    ],

    'media'    =>  [
        'driver' => 'local',
        'root'   => public_path() . config('ark.media.root'),
    ],
];