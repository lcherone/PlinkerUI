<?php
require '../config.php';
require '../vendor/autoload.php';

/**
 * Initialize plinker .
 */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    /** 
     * Plinker server listener
     */
    if (isset($_POST['data']) && 
        isset($_POST['token']) && 
        isset($_POST['public_key'])) {

        // test its encrypted
        file_put_contents('./encryption-proof.txt', print_r($_POST, true));

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
 * Public site section - very basic router and view loader. 
 */
// start session
session_start();

$route = explode('/', (!empty($_GET['route']) ? $_GET['route'] : 'index'));

// view loader
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

// handle template type
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    $template = './template/ajax.php';
} else {
    $template = './template/main.php';
}

/* Working vars */
$vars = [
    'js' => '',
    'css' => '',
    'template' => $template,
    'route' => ['view' => $route[0], 'action' => @$route[1], 'id' => @$route[2], $route],
    'db' => new \Plinker\Tasks\Model($config['database'])
]+$config;

// basic router, which loads views and injects the result into template
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
