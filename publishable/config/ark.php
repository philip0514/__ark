<?php

$config = [
    'prefix'    =>  'admin',
    'title'     =>  'Ark',
    'permission'    =>  'admin_permission',
    'session_url'   =>  'url',
    'text'      =>  [
        'account'   =>  '帳號',
        'password'  =>  '密碼',
    ],
    'error' =>  [
        'login'     =>  '帳號密碼錯誤，請確認後重新輸入',
    ],

    'media' =>  [
        's3'            =>  [
            'active'        =>  false,
            'root'          =>  env('AWS_S3_URL'),
        ],
        'root'          =>  '/storage',
        'upload'        =>  '/upload',
        'dimensions'    =>  [
            'large'         =>  [
                'folder'            =>  'large',
                'custom-crop'       =>  0,
                'width'             =>  1200,
                'height'            =>  0,
                'shape'				=>	'isometric',
                'cropper'			=>	[
                    'aspectRatio'		=>	0,
                ],
            ],
            'medium'        =>  [
                'folder'            =>  'medium',
                'custom-crop'       =>  1,
                'width'             =>  600,
                'height'            =>  0,
                'shape'				=>	'isometric',
                'cropper'			=>	[
                    'aspectRatio'		=>	0,
                ],
            ],
            'small'         =>  [
                'folder'            =>  'small',
                'custom-crop'       =>  1,
                'width'             =>  300,
                'height'            =>  0,
                'shape'				=>	'isometric',
                'cropper'			=>	[
                    'aspectRatio'		=>	0,
                ],
            ],
            'square'        =>  [
                'folder'            =>  'square',
                'custom-crop'       =>  1,
                'width'             =>  600,
                'height'            =>  600,
                'shape'				=>	'square',
                'cropper'			=>	[
                    'aspectRatio'       =>  1,
                ],
            ],
            'facebook'      =>  [
                'folder'            =>  'facebook',
                'custom-crop'       =>  0,
                'width'             =>  1200,
                'height'            =>  630,
                'shape'				=>	'landscape',
                /*
                'width'             =>  630,
                'height'            =>  1200,
                'shape'             =>  'portrait',
                */
                'cropper'			=>	[
                    'aspectRatio'		=>	1200/630,
                ],
            ]
        ]
    ],
    'url_allow_chars'   =>  '/[^a-zA-Z0-9~%:_\- \x{4e00}-\x{9fa5} \x{0800}-\x{4e00} \x{3130}-\x{318F} \x{AC00}-\x{D7A3}]+/isu',
    'tinymce'   =>  [
        'key'       =>  'pat6gvrra1ufopdrc2gorucnzjhu2ng6uwtw4tdqyhib956j'
    ],
    'route'     =>  [
        'request.toggle_sidebar',
        'request.zip',
        'tag.search',
        'tag.insert',
        'media.manager',
        'media.upload',
        'media.data',
        'media.editor',
        'user.search',
    ],
];

if($config['media']['s3']['active']){
    $config['media']['root'] = $config['media']['s3']['root'];
}

return $config;