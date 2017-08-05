<?php 

// Handle actions

// ./nodes/new
if ($vars['route']['action'] == 'new') {
    echo view('./pages/tasks/new.php', $vars); return;
}
// ./nodes/edit/*
elseif($vars['route']['action'] == 'edit') {
    echo view('./pages/tasks/edit.php', $vars); return;
}
// ./nodes/view/*
elseif($vars['route']['action'] == 'view') {
    echo view('./pages/tasks/view.php', $vars); return;
}
// ./nodes/run/*
elseif($vars['route']['action'] == 'run') {
    echo view('./pages/tasks/run.php', $vars); return;
}
// ./nodes/remove/*
elseif($vars['route']['action'] == 'remove') {
    echo view('./pages/tasks/remove.php', $vars); return;
}
// ./nodes/update/*
elseif($vars['route']['action'] == 'inline_update') {
    echo view('./pages/tasks/inline_update.php', $vars); return;
}

unset($_SESSION['node']);

$taskSources = $vars['db']->findAll('tasksource');

/**
 * Javascript
 */
ob_start() ?>
<script>
    $(document).ready(function() {
        load.script('/js/module/tasks.js', function(){
           nodes.init();
        });
    });
</script>
<?php $vars['js'] .= ob_get_clean() ?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            Tasks <small> - on (<?= $vars['plinker']['peer'] ?>)</small>
        </h1>
        <ol class="breadcrumb">
            <li>
                <a href="/"><i class="fa fa-dashboard"></i> Dashboard</a>
            </li>
            <li class="active">
                <i class="fa fa-cog"></i> Tasks
            </li>
        </ol>
    </div>
</div>

<?php if (!empty($_SESSION['alert'])): ?>
<div class="alert alert-<?= $_SESSION['alert'][0] ?>">
    <a href="#" class="close" data-dismiss="alert">&times;</a>
    <?= $_SESSION['alert'][1] ?>
</div>
<?php unset($_SESSION['alert']); endif ?>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-cog fa-fw"></i> Tasks</h3>
                <div class="panel-buttons text-right">
                    <div class="btn-group-xs">
                        <a href="/tasks/new" class="btn btn-success ajax-link"><i class="fa fa-plus"></i> New Task</a>
                    </div>
                </div>
            </div>
            <div class="panel-body nopadding">
                <div class="table-responsive">
                    <table class="table table-condensed form-table">
                        <thead>
                            <tr>
                                <th style="width:1%">ID</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Source Size (bytes)</th>
                                <th>Created</th>
                                <th>Updated</th>
                                <th>Tasks</th>
                                <th style="width:1%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($taskSources as $row): ?>
                            <tr>
                                <td><?= $row->id ?></td>
                                <td><a href="/tasks/view/<?= $row->id ?>"><?= $row->name ?></a></td>
                                <td><?= $row->type ?></td>
                                <td><?= strlen($row->source) ?></td>
                                <td><?= date_create($row->created)->format('F jS Y, g:ia') ?></td>
                                <td><?= date_create($row->updated)->format('F jS Y, g:ia') ?></td>
                                <td><?= $vars['db']->count('tasks', 'tasksource_id = ?', [$row->id]) ?></td>
                                <td>
                                    <div class="btn-group" style="display:flex">
                                        <a href="/tasks/run/<?= $row->id ?>?return=/tasks" class="btn btn-xs btn-success ajax-link"><i class="fa fa-play"></i></a>
                                        <a href="/tasks/edit/<?= $row->id ?>?return=/tasks" class="btn btn-xs btn-primary ajax-link"><i class="fa fa-pencil"></i></a>
                                        <a href="/tasks/remove/task/<?= $row->id ?>?return=/tasks" class="btn btn-xs btn-danger ajax-link"><i class="fa fa-times"></i></a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
