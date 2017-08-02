<?php

// AJAX
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    
    header('Content-Type: application/json; charset=utf-8');
    
    // type - task/queue
    $type = @$vars['route']['id'];
    // id
    $id = @$vars['route'][0][3];
    
    if (empty($type) || empty($id)) {
        exit('{"success": false, "msg": "Invalid request."}');
    } elseif (!is_numeric($id)) {
        exit('{"success": false, "msg": "Invalid task id."}');
    } elseif (!in_array($type, ['task', 'queue'])) {
        exit('{"success": false, "msg": "Invalid task type."}');
    }
    
    // handle remove task
    if ($type == 'task') {
    
        $update = [
            'id'    => @$_POST['pk'],
            'name'  => @$_POST['name'],
            'value' => @$_POST['value']
        ];
        
        $task = $vars['db']->load('tasksource', $update['id']);
        
        if (empty($task)) {
            exit('{"success": false, "msg": "Invalid task id."}');
        }
        
        if (!isset($task[$update['name']])){
            exit('{"success": false, "msg": "Invalid task property."}');
        }
        
        // update
        $task[$update['name']] = $update['value'];
        
        // addons
        if ($update['name'] == 'repeats') {
            if ($update['value'] == '1') {
                $task['completed'] = '';
            }
        }
    
        $vars['db']->store($task);
        
        exit('{"success": true, "msg": "Task updated."}');
    }
    
    // handle remove queue item
    elseif ($type == 'queue') {
        
        $update = [
            'id'    => @$_POST['pk'],
            'name'  => @$_POST['name'],
            'value' => @$_POST['value']
        ];
        
        $task = $vars['db']->load('tasks', $update['id']);
        
        if (empty($task)) {
            exit('{"success": false, "msg": "Invalid task id."}');
        }
        
        if (!isset($task[$update['name']])){
            exit('{"success": false, "msg": "Invalid task property."}');
        }
        
        // handle sleep update
        if ($_POST['name'] == 'sleep' && !is_numeric($_POST['value']) && !is_int($_POST['value'])) {
            exit('{"success": false, "msg": "Invalid sleep value, expected integer."}');
        } elseif ($_POST['name'] == 'sleep' && $_POST['value'] < 1) {
            exit('{"success": false, "msg": "Invalid sleep value, must be greater than 0"}');
        } elseif ($_POST['name'] == 'sleep' && $_POST['value'] > 31557600) {
            exit('{"success": false, "msg": "Invalid sleep value, must be less than 31557600"}');
        } elseif ($_POST['name'] == 'sleep') {
            $task->run_next = date_create($task->run_last)->modify("+".(int) $_POST['value']." seconds")->format('Y-m-d h:i:s');
        }
        
        // update
        $task[$update['name']] = $update['value'];
        
        // addons
        if ($update['name'] == 'repeats') {
            if ($update['value'] == '1') {
                $task['completed'] = '';
            }
        }
    
        $vars['db']->store($task);
    
        exit('{"success": true, "msg": "Task queue item updated."}');
    }

    //..
} else {
    alert('danger', 'No direct access, AJAX requests only!');
    redirect('/tasks');
}
