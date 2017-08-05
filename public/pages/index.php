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
