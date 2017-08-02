<?php 
if (!empty($_SESSION['node'])) {
    $node =  $vars['db']->findOne('node', 'id = ?', [(int) $_SESSION['node']]);
    
    $tasks = new Plinker\Core\Client(
        $node->peer,
        'Tasks\Manager',
        hash('sha256', gmdate('h').$node->public_key),
        hash('sha256', gmdate('h').$node->private_key),
        $vars,
        $node->encrypted // enable encryption [default: true]
    );

    $tasksource = $tasks->getSource((int) $vars['route']['id']);
} else {

    $tasks = new Plinker\Core\Client(
        $vars['plinker']['tracker'],
        'Tasks\Manager',
        hash('sha256', gmdate('h').$vars['plinker']['public_key']),
        hash('sha256', gmdate('h').$vars['plinker']['private_key']),
        $vars,
        $vars['plinker']['encrypted'] // enable encryption [default: true]
    );

    $tasksource =  $vars['db']->findOne('tasksource', 'id = ?', [(int) $vars['route']['id']]);
}

$tasks->run($tasksource->name, $tasksource->params, $tasksource->sleep);

alert('success', 'Task placed in task queue.');

redirect('/tasks/view/'.(int) $vars['route']['id']);
