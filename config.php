<?php
/**
 * Config - Defines stuff!..
 */

$config = [
    // plinker configuration
    'plinker' => [
        // self should point to this instance
        'peer' => 'http://phive.free.lxd.systems',
        
        // tracker which seeds peers
        'tracker' => 'http://phive.free.lxd.systems',
        
        // network keys
        'public_key'  => 'xxxxxxxxxxxxxxxxxxxxxx',
        // required to add nodes
        'private_key' => 'xxxxxxxxxxxxxxxxxxxxxx',
        
        'enabled' => true,
        'encrypted' => true
    ],
    
    // database connection
    // default: sqlite:'.__DIR__.'/database.db
    'database' => [
        'dsn' => 'sqlite:/var/www/html/plinkerui/database.db',
        'username' => 'x',
        'password' => 'x',
        'freeze' => true,
        'debug' => false
    ],

    // displays output to consoles
    'debug' => true,

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