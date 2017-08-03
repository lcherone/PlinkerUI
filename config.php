<?php
/**
 * Config - Defines stuff!..
 */

$config = [
    // plinker configuration
    'plinker' => [
        // self should point to this instance
        'peer' => '',
        
        // tracker which seeds peers
        'tracker' => '',
        
        // network keys
        'public_key'  => '',
        // required to add nodes
        'private_key' => '',
        
        'enabled' => true,
        'encrypted' => true
    ],
    
    // database connection
    'database' => [
        'dsn' => 'sqlite:'.__DIR__.'/database.db',
        'username' => '',
        'password' => '',
        'freeze' => false,
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
