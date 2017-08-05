<?php
if (!file_exists('../config.php')) {
    file_put_contents('../config.php', "<?php
/**
 * Config - Defines stuff!..
 */

\$config = [
    // plinker configuration
    'plinker' => [
        // self should point to this instance
        'peer' => 'http://{$_SERVER['HTTP_HOST']}',

        // tracker which seeds peers
        'tracker' => 'http://{$_SERVER['HTTP_HOST']}',

        // network keys
        'public_key'  => '".hash('sha256', uniqid(true))."',
        // required to add nodes
        'private_key' => '".hash('sha256', uniqid(true))."',

        'enabled' => true,
        'encrypted' => true
    ],

    // database connection
    // default: sqlite:'.__DIR__.'/database.db
    'database' => [
        'dsn' => 'sqlite:".realpath(__DIR__."/../")."/database.db',
        'username' => '',
        'password' => '',
        'freeze' => false,
        'debug' => false
    ],

    // displays output to consoles
    'debug' => true,

    // daemon sleep time
    'sleep_time' => 1,

    // webui login
    'webui' => [
        'user' => 'admin',
        'pass' => 'admin'
    ]
];

// define debug error reporting/output
if (\$config['debug'] === true) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
}");
}

require '../config.php';
require '../vendor/autoload.php';

/**
 * Check ./tmp folder is there
 */
if (!file_exists('../tmp')) {
    mkdir('../tmp');
}

/**
 * Plinker Server listener
 */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    /** 
     * Plinker server listener
     */
    if (isset($_POST['data']) && 
        isset($_POST['token']) && 
        isset($_POST['public_key'])
    ) {
        if ($config['plinker']['enabled'] === false) {
            exit('Node not enabled.');
        }

        // test its encrypted
        file_put_contents('../tmp/encryption-proof.txt', print_r($_POST, true));

        //
        $server = new Plinker\Core\Server(
            $_POST,
            hash('sha256', gmdate('h').$config['plinker']['public_key']),
            hash('sha256', gmdate('h').$config['plinker']['private_key'])
        );
        exit($server->execute());
    }
}

/**
 * Go App!
 */
session_start();

// config webui credentials set - go to sign in
if (!empty($config['webui']['user']) && !empty($config['webui']['pass'])) {
    if (empty($_SESSION['user'])) {
        $_GET['route'] = 'sign-in';
    }
}

// Functions

function view($view = '', $data = null) {
    global $vars;
    
    if (file_exists($view) === false) {
        return 'Partial view not Found';
    }
    
    if (!empty($data)) {
        $vars = $data+$vars;
    }

    if ($vars !== null) {
        extract(array('vars' => $vars));
    }

    ob_start();
    require($view);
    return ob_get_clean();
}

function redirect($url = null) {
    if (isset($_GET['return'])) {
        exit(header('Location: '.$_GET['return']));
    }
    
    exit(header('Location: '.$url));
}

function alert($type = 'default', $body = '') {
    $_SESSION['alert'] = [$type, $body];
}

// Working vars

$route = explode('/', (!empty($_GET['route']) ? $_GET['route'] : 'index'));

if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    $template = './template/ajax.php';
} else {
    $template = './template/main.php';
}

$vars = [
    'js' => '',
    'css' => '',
    'template' => $template,
    'route' => ['view' => $route[0], 'action' => @$route[1], 'id' => @$route[2], $route],
    'db' => new \Plinker\Tasks\Model($config['database'])
]+$config;

// Run route/pages and render into template

echo file_exists('./pages/'.$vars['route']['view'].'.php') ? view(
    $vars['template'],
    $vars+[
        'body' => view('./pages/'.$vars['route']['view'].'.php', $vars)
    ]
) : view(
    $vars['template'],
    $vars+[
        'body' => view('./pages/not_found.php')
    ]
);
