<?php

$node =  $vars['db']->findOne('node', 'id = ?', [(int) $vars['route']['id']]);

if (empty($node)) {
    alert('danger', 'Node not found.');
    redirect('/nodes');
}

$vars['db']->trash($node);

alert('success', 'Node removed.');
redirect('/nodes');