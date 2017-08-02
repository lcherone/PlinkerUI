<?php

$node =  $vars['db']->findOne('node', 'id = ?', [(int) $vars['route']['id']]);

if (empty($node)) {
    alert('danger', 'Node not found.');
    redirect('/nodes');
}

$error = [];
$tasks = new Plinker\Core\Client(
    $node->peer,
    'Tasks\Manager',
    hash('sha256', gmdate('h').$node->public_key),
    hash('sha256', gmdate('h').$node->private_key),
    json_decode($node->config, true),
    $node->encrypted // enable encryption [default: true]
);

$path = [];

unset($vars['route']['view']);
unset($vars['route']['action']);
unset($vars['route']['id']);
unset($vars['route'][0][0]);
unset($vars['route'][0][1]);
unset($vars['route'][0][2]);

$path = implode('/', $vars['route'][0]);

header('Content-Type: text/plain; charset=utf-8');

if (isset($_GET['del'])) {
    exit(base64_decode($tasks->deleteFile('/var/www/html/'.$path)));
} elseif(isset($_GET['save'])) {
    exit(base64_decode($tasks->saveFile('/var/www/html/'.$path, $_POST['data'])));
} else {
    exit(base64_decode($tasks->getFile('/var/www/html/'.$path)));
}
