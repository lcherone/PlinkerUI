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

$form = [
    'errors' => [],
    'values' => $vars
];

?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            Settings <small> - Bit of configuration.</small>
        </h1>
        <ol class="breadcrumb">
            <li>
                <a href="/"><i class="fa fa-dashboard"></i> Dashboard</a>
            </li>
            <li class="active">
                <i class="fa fa-cog"></i> Settings
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
                <h3 class="panel-title"><i class="fa fa-info-circle fa-fw"></i> Settings</h3>
            </div>
            <div class="panel-body">
                <form class="form-horizontal" method="post">
                    
                    <h4>User</h4>
                    <hr>
                    
                    <div class="form-group<?= (!empty($form['errors']['name']) ? ' has-error has-feedback' : '') ?>">
                        <label for="input-name" class="control-label col-xs-2">Username</label>
                        <div class="col-xs-8">
                            <input type="text" class="form-control" id="input-name" name="name" value="<?= (!empty($form['values']['webui']['user']) ? htmlentities($form['values']['webui']['user']) : '') ?>" placeholder="Username...">
                            <?php if (!empty($form['errors']['name'])): ?><span class="glyphicon glyphicon-warning-sign form-control-feedback"></span><?php endif ?>
                            <?php if (!empty($form['errors']['name'])): ?><span class="help-block"><?= $form['errors']['name'] ?></span><?php endif ?>
                        </div>
                    </div>
                    
                    <div class="form-group<?= (!empty($form['errors']['name']) ? ' has-error has-feedback' : '') ?>">
                        <label for="input-name" class="control-label col-xs-2">Password</label>
                        <div class="col-xs-8">
                            <input type="text" class="form-control" id="input-name" name="name" value="<?= (!empty($form['values']['webui']['pass']) ? htmlentities($form['values']['webui']['pass']) : '') ?>" placeholder="Password...">
                            <?php if (!empty($form['errors']['name'])): ?><span class="glyphicon glyphicon-warning-sign form-control-feedback"></span><?php endif ?>
                            <?php if (!empty($form['errors']['name'])): ?><span class="help-block"><?= $form['errors']['name'] ?></span><?php endif ?>
                        </div>
                    </div>
                 
                    <h4>Plinker Server</h4>
                    <hr>
                    
                    <div class="form-group<?= (!empty($form['errors']['name']) ? ' has-error has-feedback' : '') ?>">
                        <label for="input-name" class="control-label col-xs-2">Peer Address</label>
                        <div class="col-xs-8">
                            <input type="text" class="form-control" id="input-name" name="name" value="<?= (!empty($form['values']['plinker']['peer']) ? htmlentities($form['values']['plinker']['peer']) : '') ?>" placeholder="Name...">
                            <?php if (!empty($form['errors']['name'])): ?><span class="glyphicon glyphicon-warning-sign form-control-feedback"></span><?php endif ?>
                            <?php if (!empty($form['errors']['name'])): ?><span class="help-block"><?= $form['errors']['name'] ?></span><?php endif ?>
                        </div>
                    </div>
                    
                    <div class="form-group<?= (!empty($form['errors']['name']) ? ' has-error has-feedback' : '') ?>">
                        <label for="input-name" class="control-label col-xs-2">Tracker</label>
                        <div class="col-xs-8">
                            <input type="text" class="form-control" id="input-name" name="name" value="<?= (!empty($form['values']['plinker']['tracker']) ? htmlentities($form['values']['plinker']['tracker']) : '') ?>" placeholder="Name...">
                            <?php if (!empty($form['errors']['name'])): ?><span class="glyphicon glyphicon-warning-sign form-control-feedback"></span><?php endif ?>
                            <?php if (!empty($form['errors']['name'])): ?><span class="help-block"><?= $form['errors']['name'] ?></span><?php endif ?>
                        </div>
                    </div>
                    
                    <div class="form-group<?= (!empty($form['errors']['name']) ? ' has-error has-feedback' : '') ?>">
                        <label for="input-name" class="control-label col-xs-2">Public Key</label>
                        <div class="col-xs-8">
                            <input type="text" class="form-control" id="input-name" name="name" value="<?= (!empty($form['values']['plinker']['public_key']) ? htmlentities($form['values']['plinker']['public_key']) : '') ?>" placeholder="Name...">
                            <?php if (!empty($form['errors']['name'])): ?><span class="glyphicon glyphicon-warning-sign form-control-feedback"></span><?php endif ?>
                            <?php if (!empty($form['errors']['name'])): ?><span class="help-block"><?= $form['errors']['name'] ?></span><?php endif ?>
                        </div>
                    </div>
                    
                    <div class="form-group<?= (!empty($form['errors']['name']) ? ' has-error has-feedback' : '') ?>">
                        <label for="input-name" class="control-label col-xs-2">Private Key</label>
                        <div class="col-xs-8">
                            <input type="text" class="form-control" id="input-name" name="name" value="<?= (!empty($form['values']['plinker']['private_key']) ? htmlentities($form['values']['plinker']['private_key']) : '') ?>" placeholder="Name...">
                            <?php if (!empty($form['errors']['name'])): ?><span class="glyphicon glyphicon-warning-sign form-control-feedback"></span><?php endif ?>
                            <?php if (!empty($form['errors']['name'])): ?><span class="help-block"><?= $form['errors']['name'] ?></span><?php endif ?>
                        </div>
                    </div>
                    
                    <div class="form-group<?= (!empty($form['errors']['encrypted']) ? ' has-error' : '') ?>">
                        <label for="input-encrypted" class="control-label col-xs-2">Encrypted</label>
                        <div class="col-xs-8">
                            <div class="checkbox">
                                <label><input type="checkbox" name="encrypted" value="1"<?= (!empty($form['values']['plinker']['encrypted']) ? ' checked' : '') ?>> Enable</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group<?= (!empty($form['errors']['enabled']) ? ' has-error' : '') ?>">
                        <label for="input-enabled" class="control-label col-xs-2">Enabled</label>
                        <div class="col-xs-8">
                            <div class="checkbox">
                                <label><input type="checkbox" name="enabled" value="1"<?= (!empty($form['values']['plinker']['enabled']) ? ' checked' : '') ?>> Enable</label>
                            </div>
                        </div>
                    </div>
                    
                    <h4>Daemon</h4>
                    <hr>
                    
                    <div class="form-group<?= (!empty($form['errors']['name']) ? ' has-error has-feedback' : '') ?>">
                        <label for="input-name" class="control-label col-xs-2">Sleep time</label>
                        <div class="col-xs-8">
                            <input type="text" class="form-control" id="input-name" name="name" value="<?= (!empty($form['values']['sleep_time']) ? htmlentities($form['values']['sleep_time']) : '') ?>" placeholder="Name...">
                            <?php if (!empty($form['errors']['name'])): ?><span class="glyphicon glyphicon-warning-sign form-control-feedback"></span><?php endif ?>
                            <?php if (!empty($form['errors']['name'])): ?><span class="help-block"><?= $form['errors']['name'] ?></span><?php endif ?>
                        </div>
                    </div>
                    
                    <h4>Database</h4>
                    <hr>
                    
                    <div class="form-group<?= (!empty($form['errors']['name']) ? ' has-error has-feedback' : '') ?>">
                        <label for="input-name" class="control-label col-xs-2">DSN</label>
                        <div class="col-xs-8">
                            <input type="text" class="form-control" id="input-name" name="name" value="<?= (!empty($form['values']['database']['dsn']) ? htmlentities($form['values']['database']['dsn']) : '') ?>" placeholder="sqlite:/var/www/html/database.db...">
                            <?php if (!empty($form['errors']['name'])): ?><span class="glyphicon glyphicon-warning-sign form-control-feedback"></span><?php endif ?>
                            <?php if (!empty($form['errors']['name'])): ?><span class="help-block"><?= $form['errors']['name'] ?></span><?php endif ?>
                        </div>
                    </div>
                    
                    <div class="form-group<?= (!empty($form['errors']['name']) ? ' has-error has-feedback' : '') ?>">
                        <label for="input-name" class="control-label col-xs-2">Username</label>
                        <div class="col-xs-8">
                            <input type="text" class="form-control" id="input-name" name="name" value="<?= (!empty($form['values']['database']['username']) ? htmlentities($form['values']['database']['username']) : '') ?>" placeholder="Name...">
                            <?php if (!empty($form['errors']['name'])): ?><span class="glyphicon glyphicon-warning-sign form-control-feedback"></span><?php endif ?>
                            <?php if (!empty($form['errors']['name'])): ?><span class="help-block"><?= $form['errors']['name'] ?></span><?php endif ?>
                        </div>
                    </div>
                    
                    <div class="form-group<?= (!empty($form['errors']['name']) ? ' has-error has-feedback' : '') ?>">
                        <label for="input-name" class="control-label col-xs-2">Password</label>
                        <div class="col-xs-8">
                            <input type="text" class="form-control" id="input-name" name="name" value="<?= (!empty($form['values']['database']['password']) ? htmlentities($form['values']['database']['password']) : '') ?>" placeholder="Name...">
                            <?php if (!empty($form['errors']['name'])): ?><span class="glyphicon glyphicon-warning-sign form-control-feedback"></span><?php endif ?>
                            <?php if (!empty($form['errors']['name'])): ?><span class="help-block"><?= $form['errors']['name'] ?></span><?php endif ?>
                        </div>
                    </div>
                    
                    <div class="form-group<?= (!empty($form['errors']['enabled']) ? ' has-error' : '') ?>">
                        <label for="input-enabled" class="control-label col-xs-2">Freeze</label>
                        <div class="col-xs-8">
                            <div class="checkbox">
                                <label><input type="checkbox" name="enabled" value="1"<?= (!empty($form['values']['database']['freeze']) ? ' checked' : '') ?>> Enable</label>
                            </div>
                        </div>
                    </div>
                       
                    <div class="form-group">
                        <div class="col-xs-offset-2 col-xs-10">
                            <button type="submit" class="btn btn-success">Save</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
 
    </div>
</div>

<?php ob_start() ?>
<script>
    $(document).ready(function() {
        load.script('/js/module/settings.js', function(){
           
        });
    });
</script>
<?php $vars['js'] .= ob_get_clean() ?>
