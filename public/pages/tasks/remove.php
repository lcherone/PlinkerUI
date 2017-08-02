<?php

// type - task/queue
$type = @$vars['route']['id'];
// id
$id = @$vars['route'][0][3];

if (empty($type) || empty($id)) {
    redirect('/tasks');
} elseif (!is_numeric($id)) {
    alert('danger', 'Invalid task id.');
    redirect('/tasks');
} elseif (!in_array($type, ['task', 'queue'])) {
    alert('danger', 'Invalid task type.');
    redirect('/tasks');
}

// handle remove task
if ($type == 'task') {
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
    
        $tasks->removeSource((int) $id);
        alert('success', 'Task removed.');
        redirect('/tasks');
    } else {
        $task = $vars['db']->load('tasksource', $id);
        $vars['db']->trash($task);
        alert('success', 'Task removed.');
        redirect('/tasks');
    }
}

// handle remove queue item
elseif ($type == 'queue') {
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
        
        $task = $tasks->getTasks((int) $id);
        $taskId = $task->tasksource_id;
        $tasks->removeTasksLog((int) $id);

        alert('success', 'Task removed.');
        redirect('/tasks/view/'.(int) $taskId);
    } else {
        $task = $vars['db']->load('tasks', $id);
        $tasksourceId = $task->tasksource_id;
        $vars['db']->trash($task);
        alert('success', 'Task queue item removed.');
        redirect('/tasks/view/'.(int) $tasksourceId);
    }
}

alert('danger', 'Invalid or unknown task.');
redirect('/tasks/view/'.(int) $tasksourceId);
