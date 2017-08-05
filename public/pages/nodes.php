<?php 

// Handle actions

// ./nodes/new
if ($vars['route']['action'] == 'new') {
    echo view('./pages/nodes/new.php', $vars); return;
}
// ./nodes/edit/*
elseif($vars['route']['action'] == 'edit') {
    echo view('./pages/nodes/edit.php', $vars); return;
}
// ./nodes/view/*
elseif($vars['route']['action'] == 'view') {
    echo view('./pages/nodes/view.php', $vars); return;
}
// ./nodes/remove/*
elseif($vars['route']['action'] == 'remove') {
    echo view('./pages/nodes/remove.php', $vars); return;
}
// ./nodes/files/*
elseif($vars['route']['action'] == 'file') {
    echo view('./pages/nodes/file.php', $vars); return;
}

$nodes = $vars['db']->findAll('node');

if (empty($nodes)) {
    // fall back to self if peer not set
    $vars['plinker']['peer'] = (!empty($vars['plinker']['peer']) ? $vars['plinker']['peer'] : 'http://'.$_SERVER['HTTP_HOST']);
    
    $vars['db']->findOrCreate([
        'node',
        'name' => $_SERVER['HTTP_HOST'],
        'peer' => $vars['plinker']['peer'],
        'public_key' => $vars['plinker']['public_key'],
        'private_key' => $vars['plinker']['private_key'],
        'enabled' => $vars['plinker']['enabled'],
        'encrypted' => $vars['plinker']['encrypted'],
        'config' => json_encode($vars)
    ]);
    
    $nodes = $vars['db']->findAll('node');
} ?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            Nodes <small> - Instances of this script.</small>
        </h1>
        <ol class="breadcrumb">
            <li>
                <a href="/" class="ajax-link"><i class="fa fa-dashboard"></i> Dashboard</a>
            </li>
            <li class="active">
                <i class="fa fa-server"></i> Nodes
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
                <h3 class="panel-title"><i class="fa fa-server fa-fw"></i> Nodes</h3>
                <div class="panel-buttons text-right">
                    <div class="btn-group-xs">
                        <a href="/nodes/new" class="btn btn-success ajax-link">New Node</a>
                    </div>
                </div>
            </div>
            <div class="panel-body nopadding">
                <div class="table-responsive">
                    <table class="table table-condensed">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Public Key</th>
                                <th>Enabled</th>
                                <th>Encrypted</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php $i = 0; foreach ($nodes as $row): ?>
                            <tr>
                               <td><a href="/nodes/view/<?= $row->id ?>" class="ajax-link"><?= $row->name ?></a></td>
                               <td><pre><?= $row->public_key ?></pre></td>
                               <td><?= (!empty($row->enabled) ? '<i class="fa fa-check text-success"></i>' : '<i class="fa fa-times text-danger"></i>') ?></td>
                               <td><?= (!empty($row->encrypted) ? '<i class="fa fa-check text-success"></i>' : '<i class="fa fa-times text-danger"></i>') ?></td>
                               <td>
                                   <div class="btn-group">
                                       <a href="/nodes/edit/<?= $row->id ?>" class="btn btn-xs btn-primary ajax-link"><i class="fa fa-pencil"></i></a>
                                       <?php if ($i > 0): ?>
                                       <a href="/nodes/remove/<?= $row->id ?>" class="btn btn-xs btn-danger ajax-link"><i class="fa fa-times"></i></a>
                                       <?php endif ?>
                                   </div>
                                </td>
                            </tr>
                        <?php $i++; endforeach ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php ob_start() ?>
<script>
    $(document).ready(function() {
        load.script('/js/module/nodes.js', function(){
           nodes.init();
        });
    });
</script>
<?php $vars['js'] .= ob_get_clean() ?>
