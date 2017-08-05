<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="Lawrence Cherone">

        <title>PlinkerUI<?php 
            if ($vars['route']['view'] == 'index'): ?> - Dashboard<?php endif;
            if ($vars['route']['view'] == 'tasks'): ?> - Tasks<?php endif;
            if ($vars['route']['view'] == 'nodes'): ?> - Nodes<?php endif;
            if ($vars['route']['view'] == 'settings'): ?> - Settings<?php endif;
            if ($vars['route']['view'] == 'sign-in'): ?> - Sign In<?php endif;
        ?></title>

        <link rel="stylesheet" href="/dist/vendors.min.css">
        <link rel="stylesheet" href="/dist/styles.min.css">
        <?= $vars['css'] ?>
    </head>
    <body>

        <div id="<?= (!empty($_SESSION['user']) ? 'wrapper' : '') ?>">
            
            <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
                <div class="navbar-header">
                    <?php if (!empty($_SESSION['user'])): ?>
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <?php endif ?>
                    <a class="navbar-brand" href="/" class="ajax-link">PlinkerUI</a>
                </div>

                <?php if (!empty($_SESSION['user'])): ?>
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
                        <li>
                            <a href="/sign-out"><i class="fa fa-fw fa-sign-out"></i> Sign Out</a>
                        </li>
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
                <?php endif ?>
            </nav>
    
            <div id="page-wrapper">
                <div class="container-fluid ajax-container">
                    <?= $vars['body'] ?>
                </div>
            </div>
        </div>

        <script src="/dist/vendors.min.js"></script>
        <script src="https://ajaxorg.github.io/ace-builds/src-min-noconflict/ace.js"></script>
        <script src="/dist/app.min.js"></script>
       
        <?= $vars['js'] ?>
    </body>
</html>