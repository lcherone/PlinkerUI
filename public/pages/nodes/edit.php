<?php

$node =  $vars['db']->findOne('node', 'id = ?', [(int) $vars['route']['id']]);

if (empty($node)) {
    alert('danger', '<strong>Error:</strong> Node not found.');
    redirect('/nodes');
}

$form = [
    'errors' => [],
    'values' => [
        'name'        => (isset($_POST['name'])        ? trim($_POST['name'])        : $node->name),
        'peer'        => (isset($_POST['peer'])        ? trim($_POST['peer'])        : $node->peer),
        'public_key'  => (isset($_POST['public_key'])  ? trim($_POST['public_key'])  : $node->public_key),
        'private_key' => (isset($_POST['private_key']) ? trim($_POST['private_key']) : $node->private_key),
        'encrypted'   => (isset($_POST['encrypted'])   ? 1 : 0),
        'enabled'     => (isset($_POST['enabled'])     ? 1 : 0)
    ]
];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {


    
    // // type
    // if (!in_array($form['values']['type'], ['php-closure', 'php-raw', 'bash'])) {
    //     $form['errors']['type'] = 'Invalid task source type, choose from the list.';
    // }

    // // name
    // if (empty($form['values']['name'])) {
    //     $form['errors']['name'] = 'Name is a required field.';
    // }
    
    // // description
    // if (empty($form['values']['description'])) {
    //     $form['errors']['description'] = 'Description is a required field.';
    // } else {
    //     $form['values']['description'] = strip_tags($form['values']['description']);
    // }
    
    // // sleep
    // if (!empty($form['values']['repeats'])) {
    //     if ($form['values']['sleep'] < 1) {
    //         $form['errors']['sleep'] = 'Min sleep value is 1 second.';
            
    //     } elseif ($form['values']['sleep'] > 31557600) {
    //         $form['errors']['sleep'] = 'Max sleep value is 31557600 seconds. (around 1 year).';
    //     }
    // } else {
    //     $form['values']['sleep'] = 0;
    // }
    
    // // source
    // if (empty($form['values']['source'])) {
    //     $form['errors']['source'] = 'Source is a required field.';
    // }
    
    // all is good
    if (empty($form['errors'])) {
        

        $node->import([
            'node',
            'name' => $form['values']['name'],
            'peer' => $form['values']['peer'],
            'public_key' => $form['values']['public_key'],
            'private_key' => $form['values']['private_key'],
            'enabled' => $form['values']['enabled'],
            'encrypted' => $form['values']['encrypted'],
            'config' => json_encode($vars)
        ]);
        
        $vars['db']->store($node);

        // , true).'</pre>';

        alert('success', 'Node updated.');
        redirect('/nodes');
    }
}

/**
 * Javascript
 */
ob_start() ?>
<script>
    $(document).ready(function() {
        load.script('/js/module/tasks.js', function() {
            nodes.init();
        });
    });
</script>
<?php $vars['js'] .= ob_get_clean() ?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            Node <small> - Edit</small>
        </h1>
        <ol class="breadcrumb">
            <li>
                <a href="/"><i class="fa fa-dashboard"></i> Dashboard</a>
            </li>
            <li class="active">
                <i class="fa fa-servers"></i> Nodes
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
                <h3 class="panel-title"><i class="fa fa-info-circle fa-fw"></i> Edit Node</h3>
            </div>
            <div class="panel-body">
                <form class="form-horizontal" method="post">
                    <div class="form-group<?= (!empty($form['errors']['name']) ? ' has-error has-feedback' : '') ?>">
                        <label for="input-name" class="control-label col-xs-2">Name</label>
                        <div class="col-xs-8">
                            <input type="text" class="form-control" id="input-name" name="name" value="<?= (!empty($form['values']['name']) ? htmlentities($form['values']['name']) : '') ?>" placeholder="Name...">
                            <?php if (!empty($form['errors']['name'])): ?><span class="glyphicon glyphicon-warning-sign form-control-feedback"></span><?php endif ?>
                            <?php if (!empty($form['errors']['name'])): ?><span class="help-block"><?= $form['errors']['name'] ?></span><?php endif ?>
                        </div>
                    </div>

                    <div class="form-group<?= (!empty($form['errors']['peer']) ? ' has-error has-feedback' : '') ?>">
                        <label for="input-peer" class="control-label col-xs-2">Endpoint URL</label>
                        <div class="col-xs-8">
                            <input type="text" class="form-control" id="input-peer" name="peer" value="<?= (!empty($form['values']['peer']) ? htmlentities($form['values']['peer']) : '') ?>" placeholder="Endpoint URL... E.G: http://<?= $_SERVER['HTTP_HOST'] ?>">
                            <?php if (!empty($form['errors']['peer'])): ?><span class="glyphicon glyphicon-warning-sign form-control-feedback"></span><?php endif ?>
                            <?php if (!empty($form['errors']['peer'])): ?><span class="help-block"><?= $form['errors']['peer'] ?></span><?php endif ?>
                        </div>
                    </div>
                    
                    <div class="form-group<?= (!empty($form['errors']['public_key']) ? ' has-error has-feedback' : '') ?>">
                        <label for="input-public_key" class="control-label col-xs-2">Public Key</label>
                        <div class="col-xs-8">
                            <input type="text" class="form-control" id="input-public_key" name="public_key" value="<?= (!empty($form['values']['public_key']) ? htmlentities($form['values']['public_key']) : '') ?>" placeholder="Public Key...">
                            <?php if (!empty($form['errors']['public_key'])): ?><span class="glyphicon glyphicon-warning-sign form-control-feedback"></span><?php endif ?>
                            <?php if (!empty($form['errors']['public_key'])): ?><span class="help-block"><?= $form['errors']['public_key'] ?></span><?php endif ?>
                        </div>
                    </div>
                    
                    <div class="form-group<?= (!empty($form['errors']['private_key']) ? ' has-error has-feedback' : '') ?>">
                        <label for="input-private_key" class="control-label col-xs-2">Private Key</label>
                        <div class="col-xs-8">
                            <input type="text" class="form-control" id="input-private_key" name="private_key" value="<?= (!empty($form['values']['private_key']) ? htmlentities($form['values']['private_key']) : $vars['plinker']['private_key']) ?>" placeholder="Private Key..." readonly>
                            <?php if (!empty($form['errors']['private_key'])): ?><span class="glyphicon glyphicon-warning-sign form-control-feedback"></span><?php endif ?>
                            <?php if (!empty($form['errors']['private_key'])): ?><span class="help-block"><?= $form['errors']['private_key'] ?></span><?php else: ?><span class="help-block" style="margin-bottom:-10px">* Private key must be the same across all nodes.</span><?php endif ?>
                        </div>
                    </div>

                    <div class="form-group<?= (!empty($form['errors']['encrypted']) ? ' has-error' : '') ?>">
                        <label for="input-encrypted" class="control-label col-xs-2">Encrypted</label>
                        <div class="col-xs-8">
                            <div class="checkbox">
                                <label><input type="checkbox" name="encrypted" value="1"<?= (!empty($form['values']['encrypted']) ? ' checked' : '') ?>> Enable</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group<?= (!empty($form['errors']['enabled']) ? ' has-error' : '') ?>">
                        <label for="input-enabled" class="control-label col-xs-2">Enabled</label>
                        <div class="col-xs-8">
                            <div class="checkbox">
                                <label><input type="checkbox" name="enabled" value="1"<?= (!empty($form['values']['enabled']) ? ' checked' : '') ?>> Enable</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="col-xs-offset-2 col-xs-10">
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
