<?php

return [
    'database' => [
        'adapter'  => '<database_provider>',
        'host'     => '<hostname>>',
        'username' => '<db_user>',
        'password' => '<db_pass>',
        'dbname'   => '<database_name>',
        'charset'  => 'utf8'
    ],
    
    'module' => [
    		'dataDir'						=> BASE_PATH . '/data/modules/api',
        'cacheDir'					=> BASE_PATH . '/tmp/cache/modules/api',
				'logDir'						=> BASE_PATH . '/log/modules/api',
				
				// Options related to caching
				'cache'							=> array()
    ]
];
