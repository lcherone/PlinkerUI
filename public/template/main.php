<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="Lawrence Cherone">

        <title>PlinkerUI - v0.0.1
        <?php if ($vars['route']['view'] == 'index'): ?>- Dashboard<?php endif ?>
        <?php if ($vars['route']['view'] == 'tasks'): ?>- Tasks<?php endif ?>
        <?php if ($vars['route']['view'] == 'nodes'): ?>- Nodes<?php endif ?>
        <?php if ($vars['route']['view'] == 'settings'): ?>- Settings<?php endif ?>
        </title>

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css" integrity="sha384-T8Gy5hrqNKT+hzMclPo118YTQO6cYprQmhrYwIiQ/3axmI1hQomh7Ud2hPOy8SP1" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha256-916EbMg70RQy9LHiGkXzG8hSg9EdNy97GazNG/aiY1w=" crossorigin="anonymous" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha256-ZT4HPpdCOt2lvDkXokHuhJfdOKSPFLzeAJik5U/Q+l4=" crossorigin="anonymous" />
        <link rel="stylesheet" href="/css/style.css">
        
        <?= $vars['css'] ?>
    </head>
    <body>

        <div id="wrapper">
    
            <!-- Navigation -->
            <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="/" class="ajax-link">PlinkerUI - v0.0.1</a>
                </div>
                <!-- Top Menu Items -->
                <!--<ul class="nav navbar-right top-nav">-->
                <!--    <li class="dropdown">-->
                <!--        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i> John Smith <b class="caret"></b></a>-->
                <!--        <ul class="dropdown-menu">-->
                <!--            <li>-->
                <!--                <a href="#"><i class="fa fa-fw fa-user"></i> Profile</a>-->
                <!--            </li>-->
                <!--            <li>-->
                <!--                <a href="#"><i class="fa fa-fw fa-envelope"></i> Inbox</a>-->
                <!--            </li>-->
                <!--            <li>-->
                <!--                <a href="#"><i class="fa fa-fw fa-gear"></i> Settings</a>-->
                <!--            </li>-->
                <!--            <li class="divider"></li>-->
                <!--            <li>-->
                <!--                <a href="#"><i class="fa fa-fw fa-power-off"></i> Log Out</a>-->
                <!--            </li>-->
                <!--        </ul>-->
                <!--    </li>-->
                <!--</ul>-->
                <!-- Sidebar Menu Items - These collapse to the responsive navigation menu on small screens -->
                <div class="collapse navbar-collapse navbar-collapse">
                    <ul class="nav navbar-nav side-nav">
                        <li<?= ($vars['route']['view'] == 'index') ? ' class="active"' : '' ?>>
                            <a href="/" class="ajax-link"><i class="fa fa-fw fa-dashboard"></i> Dashboard</a>
                        </li>
                        <li<?= ($vars['route']['view'] == 'tasks') ? ' class="active"' : '' ?>>
                            <a href="/tasks" class="ajax-link"><i class="fa fa-fw fa-cog"></i> Tasks</a>
                        </li>
                        <li<?= ($vars['route']['view'] == 'nodes') ? ' class="active"' : '' ?>>
                            <a href="/nodes" class="ajax-link"><i class="fa fa-fw fa-server"></i> Nodes</a>
                        </li>
                        <li<?= ($vars['route']['view'] == 'settings') ? ' class="active"' : '' ?>>
                            <a href="/settings" class="ajax-link"><i class="fa fa-fw fa-cogs"></i> Settings</a>
                        </li>
                        <!--<li>-->
                        <!--    <a href="charts.html"><i class="fa fa-fw fa-bar-chart-o"></i> Charts</a>-->
                        <!--</li>-->
                        <!--<li>-->
                        <!--    <a href="tables.html"><i class="fa fa-fw fa-table"></i> Tables</a>-->
                        <!--</li>-->
                        <!--<li>-->
                        <!--    <a href="forms.html"><i class="fa fa-fw fa-edit"></i> Forms</a>-->
                        <!--</li>-->
                        <!--<li>-->
                        <!--    <a href="bootstrap-elements.html"><i class="fa fa-fw fa-desktop"></i> Bootstrap Elements</a>-->
                        <!--</li>-->
                        <!--<li>-->
                        <!--    <a href="bootstrap-grid.html"><i class="fa fa-fw fa-wrench"></i> Bootstrap Grid</a>-->
                        <!--</li>-->
                        <!--<li>-->
                        <!--    <a href="javascript:;" data-toggle="collapse" data-target="#demo"><i class="fa fa-fw fa-arrows-v"></i> Dropdown <i class="fa fa-fw fa-caret-down"></i></a>-->
                        <!--    <ul id="demo" class="collapse">-->
                        <!--        <li>-->
                        <!--            <a href="#">Dropdown Item</a>-->
                        <!--        </li>-->
                        <!--        <li>-->
                        <!--            <a href="#">Dropdown Item</a>-->
                        <!--        </li>-->
                        <!--    </ul>-->
                        <!--</li>-->
                        <!--<li>-->
                        <!--    <a href="blank-page.html"><i class="fa fa-fw fa-file"></i> Blank Page</a>-->
                        <!--</li>-->
                        <!--<li>-->
                        <!--    <a href="index-rtl.html"><i class="fa fa-fw fa-dashboard"></i> RTL Dashboard</a>-->
                        <!--</li>-->
                    </ul>
                </div>
                <!-- /.navbar-collapse -->
            </nav>
    
            <div id="page-wrapper">
    
                <div class="container-fluid ajax-container">
                    <?= $vars['body'] ?>
                </div>
                <!-- /.container-fluid -->
    
            </div>
            <!-- /#page-wrapper -->
    
        </div>
        <!-- /#wrapper -->
        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js" integrity="sha384-nvAa0+6Qg9clwYCGGPpDQLVpLNn0fRaROjHqs13t4Ggj3Ez50XnGQqc/r8MhnRDZ" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha256-U5ZEeKfGNOja007MMD3YBI0A3OSZOQbeG6z2f2Y0hu8=" crossorigin="anonymous"></script>
        <script src="//ajaxorg.github.io/ace-builds/src-min-noconflict/ace.js" type="text/javascript" charset="utf-8"></script>
        <script src="/js/app.js"></script>
       
        <?= $vars['js'] ?>
    </body>
</html>