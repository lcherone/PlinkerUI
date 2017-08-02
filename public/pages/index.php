<?php 

$nodes = $vars['db']->findAll('node');

if (empty($nodes)) {
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
}

?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            Dashboard <small> - An overview of sorts.</small>
        </h1>
        <ol class="breadcrumb">
            <li class="active">
                <i class="fa fa-dashboard"></i> Dashboard
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
        <h2>Whats it do?</h2>
        <p>This little PHP script acts as a little UI for Plinker, which uses the Tasks Component to apply tasks to remote nodes and remotely execute them. Just to demonstrate some of the uses for Plinker.</p>
        
        <h2>Why did you make it?</h2>
        <p>Because its cool.</p>
    </div>
</div>

<?php ob_start() ?>
<script>
    $(document).ready(function() {
        load.script('/js/module/index.js', function(){
           
        });
    });
</script>
<?php $vars['js'] .= ob_get_clean() ?>

<?php return ?>



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