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

//print_r($taskSources);

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
<?php $vars['js'] .= ob_get_clean()

/**
 * Page Title
 */
?>
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
                                <th style="width:3%">ID</th>
                                <th style="width:18%">Name</th>
                                <th style="width:10%">Type</th>
                                <th style="width:13%">Source Size (bytes)</th>
                                <th style="width:20%">Checksum</th>
                                <th style="width:12.5%">Created</th>
                                <th style="width:12.5%">Updated</th>
                                <th style="width:10%">Tasks</th>
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
                                <td class="text"><span class="nowrap"><?= $row->checksum ?></span></td>
                                <td><?= $row->created ?></td>
                                <td><?= $row->updated ?></td>
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

<?php return; ?>



 <!-- Page Heading -->
                    <div class="row">
                        <div class="col-lg-12">
                            <h1 class="page-header">
                                Peers <small> Overview</small>
                            </h1>
                            <!--<ol class="breadcrumb">-->
                            <!--    <li class="active">-->
                            <!--        <i class="fa fa-dashboard"></i> Dashboard-->
                            <!--    </li>-->
                            <!--</ol>-->
                        </div>
                    </div>
                    <!-- /.row -->

                    <?php if (empty($_GET['peer'])): ?>
                    <div class="row">
                        <div class="col-lg-12">
 
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Peer</th>
                                                    <th>IP</th>
                                                    <th>Peers</th>
                                                    <th>Log</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($result['peers'] as $key => $value): ?>
                                                <tr>
                                                    <td><a href="?peer=<?= urlencode($value['peer']) ?>&action=view"><?= $value['peer'] ?></a></td>
                                                    <td><?= $value['ip'] ?></td>
                                                    <td><?= count($value['peers']) ?></td>
                                                    <td>
                                                        <?php foreach ((array) $value['log'] as $log): ?>
                                                        <span><?= $log['action'] ?> - <?= $log['time'] ?></span><br>
                                                        <?php endforeach ?>
                                                    </td>
                                                    <td><span class="label label-success">Online</span></td>
                                                </tr>
                                                <?php endforeach ?>
                                            </tbody>
                                        </table>
                                    </div>

                            
                        </div>
                    </div>
                    <?php else: ?>
                    <pre><?php echo print_r($peer, true) ?></pre>
                    <pre><?php echo print_r($result, true) ?></pre>
                    <?php endif ?>
    
                    <!--<div class="row">-->
                    <!--    <div class="col-lg-12">-->
                    <!--        <div class="alert alert-info alert-dismissable">-->
                    <!--            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>-->
                    <!--            <i class="fa fa-info-circle"></i>  <strong>Like SB Admin?</strong> Try out <a href="http://startbootstrap.com/template-overviews/sb-admin-2" class="alert-link">SB Admin 2</a> for additional features!-->
                    <!--        </div>-->
                    <!--    </div>-->
                    <!--</div>-->
                    <!-- /.row -->
    
                    <!--<div class="row">-->
                    <!--    <div class="col-lg-3 col-md-6">-->
                    <!--        <div class="panel panel-primary">-->
                    <!--            <div class="panel-heading">-->
                    <!--                <div class="row">-->
                    <!--                    <div class="col-xs-3">-->
                    <!--                        <i class="fa fa-comments fa-5x"></i>-->
                    <!--                    </div>-->
                    <!--                    <div class="col-xs-9 text-right">-->
                    <!--                        <div class="huge">26</div>-->
                    <!--                        <div>New Comments!</div>-->
                    <!--                    </div>-->
                    <!--                </div>-->
                    <!--            </div>-->
                    <!--            <a href="#">-->
                    <!--                <div class="panel-footer">-->
                    <!--                    <span class="pull-left">View Details</span>-->
                    <!--                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>-->
                    <!--                    <div class="clearfix"></div>-->
                    <!--                </div>-->
                    <!--            </a>-->
                    <!--        </div>-->
                    <!--    </div>-->
                    <!--    <div class="col-lg-3 col-md-6">-->
                    <!--        <div class="panel panel-green">-->
                    <!--            <div class="panel-heading">-->
                    <!--                <div class="row">-->
                    <!--                    <div class="col-xs-3">-->
                    <!--                        <i class="fa fa-tasks fa-5x"></i>-->
                    <!--                    </div>-->
                    <!--                    <div class="col-xs-9 text-right">-->
                    <!--                        <div class="huge">12</div>-->
                    <!--                        <div>New Tasks!</div>-->
                    <!--                    </div>-->
                    <!--                </div>-->
                    <!--            </div>-->
                    <!--            <a href="#">-->
                    <!--                <div class="panel-footer">-->
                    <!--                    <span class="pull-left">View Details</span>-->
                    <!--                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>-->
                    <!--                    <div class="clearfix"></div>-->
                    <!--                </div>-->
                    <!--            </a>-->
                    <!--        </div>-->
                    <!--    </div>-->
                    <!--    <div class="col-lg-3 col-md-6">-->
                    <!--        <div class="panel panel-yellow">-->
                    <!--            <div class="panel-heading">-->
                    <!--                <div class="row">-->
                    <!--                    <div class="col-xs-3">-->
                    <!--                        <i class="fa fa-shopping-cart fa-5x"></i>-->
                    <!--                    </div>-->
                    <!--                    <div class="col-xs-9 text-right">-->
                    <!--                        <div class="huge">124</div>-->
                    <!--                        <div>New Orders!</div>-->
                    <!--                    </div>-->
                    <!--                </div>-->
                    <!--            </div>-->
                    <!--            <a href="#">-->
                    <!--                <div class="panel-footer">-->
                    <!--                    <span class="pull-left">View Details</span>-->
                    <!--                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>-->
                    <!--                    <div class="clearfix"></div>-->
                    <!--                </div>-->
                    <!--            </a>-->
                    <!--        </div>-->
                    <!--    </div>-->
                    <!--    <div class="col-lg-3 col-md-6">-->
                    <!--        <div class="panel panel-red">-->
                    <!--            <div class="panel-heading">-->
                    <!--                <div class="row">-->
                    <!--                    <div class="col-xs-3">-->
                    <!--                        <i class="fa fa-support fa-5x"></i>-->
                    <!--                    </div>-->
                    <!--                    <div class="col-xs-9 text-right">-->
                    <!--                        <div class="huge">13</div>-->
                    <!--                        <div>Support Tickets!</div>-->
                    <!--                    </div>-->
                    <!--                </div>-->
                    <!--            </div>-->
                    <!--            <a href="#">-->
                    <!--                <div class="panel-footer">-->
                    <!--                    <span class="pull-left">View Details</span>-->
                    <!--                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>-->
                    <!--                    <div class="clearfix"></div>-->
                    <!--                </div>-->
                    <!--            </a>-->
                    <!--        </div>-->
                    <!--    </div>-->
                    <!--</div>-->
                    <!-- /.row -->
    
                    <!--<div class="row">-->
                    <!--    <div class="col-lg-12">-->
                    <!--        <div class="panel panel-default">-->
                    <!--            <div class="panel-heading">-->
                    <!--                <h3 class="panel-title"><i class="fa fa-bar-chart-o fa-fw"></i> Area Chart</h3>-->
                    <!--            </div>-->
                    <!--            <div class="panel-body">-->
                    <!--                <div id="morris-area-chart"></div>-->
                    <!--            </div>-->
                    <!--        </div>-->
                    <!--    </div>-->
                    <!--</div>-->
                    <!-- /.row -->
    
                    <!--<div class="row">-->
                    <!--    <div class="col-lg-4">-->
                    <!--        <div class="panel panel-default">-->
                    <!--            <div class="panel-heading">-->
                    <!--                <h3 class="panel-title"><i class="fa fa-long-arrow-right fa-fw"></i> Donut Chart</h3>-->
                    <!--            </div>-->
                    <!--            <div class="panel-body">-->
                    <!--                <div id="morris-donut-chart"></div>-->
                    <!--                <div class="text-right">-->
                    <!--                    <a href="#">View Details <i class="fa fa-arrow-circle-right"></i></a>-->
                    <!--                </div>-->
                    <!--            </div>-->
                    <!--        </div>-->
                    <!--    </div>-->
                    <!--    <div class="col-lg-4">-->
                    <!--        <div class="panel panel-default">-->
                    <!--            <div class="panel-heading">-->
                    <!--                <h3 class="panel-title"><i class="fa fa-clock-o fa-fw"></i> Tasks Panel</h3>-->
                    <!--            </div>-->
                    <!--            <div class="panel-body">-->
                    <!--                <div class="list-group">-->
                    <!--                    <a href="#" class="list-group-item">-->
                    <!--                        <span class="badge">just now</span>-->
                    <!--                        <i class="fa fa-fw fa-calendar"></i> Calendar updated-->
                    <!--                    </a>-->
                    <!--                    <a href="#" class="list-group-item">-->
                    <!--                        <span class="badge">4 minutes ago</span>-->
                    <!--                        <i class="fa fa-fw fa-comment"></i> Commented on a post-->
                    <!--                    </a>-->
                    <!--                    <a href="#" class="list-group-item">-->
                    <!--                        <span class="badge">23 minutes ago</span>-->
                    <!--                        <i class="fa fa-fw fa-truck"></i> Order 392 shipped-->
                    <!--                    </a>-->
                    <!--                    <a href="#" class="list-group-item">-->
                    <!--                        <span class="badge">46 minutes ago</span>-->
                    <!--                        <i class="fa fa-fw fa-money"></i> Invoice 653 has been paid-->
                    <!--                    </a>-->
                    <!--                    <a href="#" class="list-group-item">-->
                    <!--                        <span class="badge">1 hour ago</span>-->
                    <!--                        <i class="fa fa-fw fa-user"></i> A new user has been added-->
                    <!--                    </a>-->
                    <!--                    <a href="#" class="list-group-item">-->
                    <!--                        <span class="badge">2 hours ago</span>-->
                    <!--                        <i class="fa fa-fw fa-check"></i> Completed task: "pick up dry cleaning"-->
                    <!--                    </a>-->
                    <!--                    <a href="#" class="list-group-item">-->
                    <!--                        <span class="badge">yesterday</span>-->
                    <!--                        <i class="fa fa-fw fa-globe"></i> Saved the world-->
                    <!--                    </a>-->
                    <!--                    <a href="#" class="list-group-item">-->
                    <!--                        <span class="badge">two days ago</span>-->
                    <!--                        <i class="fa fa-fw fa-check"></i> Completed task: "fix error on sales page"-->
                    <!--                    </a>-->
                    <!--                </div>-->
                    <!--                <div class="text-right">-->
                    <!--                    <a href="#">View All Activity <i class="fa fa-arrow-circle-right"></i></a>-->
                    <!--                </div>-->
                    <!--            </div>-->
                    <!--        </div>-->
                    <!--    </div>-->
                    <!--    <div class="col-lg-4">-->
                            
                    <!--    </div>-->
                    <!--</div>-->
                    <!-- /.row -->