<?php
/**
 * Config - Defines stuff!..
 */

$config = [
    // plinker configuration
    'plinker' => [
        // self should point to this instance
        'peer'  => 'http://'.$_SERVER['HTTP_HOST'],
        
        // tracker which seeds peers
        'tracker'     => 'http://phive.free.lxd.systems',
        
        // network keys
        'public_key'  => '86a4a2ade62e48461835d7faf7cf969f94ddca7a4ac5daf654aa',
        // required to add nodes
        'private_key' => '2c26dc4c7e108b77a453d5f0b874daf734ac20cfc68bdbf35311',
        
        'enabled' => true,
        'encrypted' => true
    ],

    // displays output to consoles
    'debug' => true,
    
    // database connection
    'database' => [
        'dsn' => 'sqlite:'.__DIR__.'/database.db',
        'username' => '',
        'password' => '',
        'freeze' => false,
        'debug' => false
    ],
    
    // daemon sleep time
    'sleep_time' => 5,

    // webui login
    'webui' => [
        'user' => 'admin',
        'pass' => 'admin'
    ]
];

// define debug error reporting/output
if ($config['debug'] === true) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
}
