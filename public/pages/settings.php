<?php

$form = [
    'errors' => [],
    'values' => $vars
];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // username
    if (empty($_POST['webui']['user'])) {
        $form['errors']['webui']['user'] = 'Username is a required field.';
    } else {
        $form['values']['webui']['user'] = str_replace("'", "\'", $_POST['webui']['user']);
    }

    // password
    if (empty($_POST['webui']['pass'])) {
        $form['errors']['webui']['pass'] = 'Password is a required field.';
    } else {
        $form['values']['webui']['pass'] = str_replace("'", "\'", $_POST['webui']['pass']);
    }

    // sleep_time
    if (!isset($_POST['sleep_time']) || !is_numeric($_POST['sleep_time'])) {
        $form['errors']['sleep_time'] = 'Sleep time is a required field.';
    } else {
        $form['values']['sleep_time'] = ((int) $_POST['sleep_time'] <= 0 ? 1 : (int) $_POST['sleep_time']);
    }

    // plinker - peer
    if (empty($_POST['plinker']['peer'])) {
        $form['errors']['plinker']['peer'] = 'Peer is a required field.';
    } else {
        $form['values']['plinker']['peer'] = str_replace("'", "\'", $_POST['plinker']['peer']);
    }

    // plinker - tracker
    if (empty($_POST['plinker']['tracker'])) {
        $form['errors']['plinker']['tracker'] = 'Tracker is a required field.';
    } else {
        $form['values']['plinker']['tracker'] = str_replace("'", "\'", $_POST['plinker']['tracker']);
    }

    // plinker - public_key
    if (empty($_POST['plinker']['public_key'])) {
        $form['errors']['plinker']['public_key'] = 'Public key is a required field.';
    } else {
        $form['values']['plinker']['public_key'] = str_replace("'", "\'", $_POST['plinker']['public_key']);
    }

    // plinker - private_key
    if (empty($_POST['plinker']['private_key'])) {
        $form['errors']['plinker']['private_key'] = 'Private key is a required field.';
    } else {
        $form['values']['plinker']['private_key'] = str_replace("'", "\'", $_POST['plinker']['private_key']);
    }

    // plinker - enabled
    if (isset($_POST['plinker']['enabled'])) {
        $form['values']['plinker']['enabled'] = true;
    } else {
        $form['values']['plinker']['enabled'] = false;
    }

    // plinker - enabled
    if (isset($_POST['plinker']['encrypted'])) {
        $form['values']['plinker']['encrypted'] = true;
    } else {
        $form['values']['plinker']['encrypted'] = false;
    }

    // database - dsn
    if (empty($_POST['database']['dsn'])) {
        $form['errors']['database']['dsn'] = 'DSN is a required field.';
    } else {
        $form['values']['database']['dsn'] = str_replace("'", "\'", $_POST['database']['dsn']);
    }

    // database - username
    if (empty($_POST['database']['username'])) {
        //$form['errors']['database']['username'] = 'Username is a required field.';
        $form['values']['database']['username'] = '';
    } else {
        $form['values']['database']['username'] = str_replace("'", "\'", $_POST['database']['username']);
    }

    // database - password
    if (empty($_POST['database']['password'])) {
        //$form['errors']['database']['password'] = 'Password is a required field.';
        $form['values']['database']['password'] = '';
    } else {
        $form['values']['database']['password'] = str_replace("'", "\'", $_POST['database']['password']);
    }

    // database - freeze
    if (isset($_POST['database']['freeze'])) {
        $form['values']['database']['freeze'] = true;
    } else {
        $form['values']['database']['freeze'] = false;
    }

    if (empty($form['errors'])) {
        file_put_contents('../config.php', "<?php
/**
 * Config - Defines stuff!..
 */

\$config = [
    // plinker configuration
    'plinker' => [
        // self should point to this instance
        'peer' => '{$form['values']['plinker']['peer']}',

        // tracker which seeds peers
        'tracker' => '{$form['values']['plinker']['tracker']}',

        // network keys
        'public_key'  => '{$form['values']['plinker']['public_key']}',
        // required to add nodes
        'private_key' => '{$form['values']['plinker']['private_key']}',

        'enabled' => ".(!empty($form['values']['plinker']['enabled']) ? 'true' : 'false').",
        'encrypted' => ".(!empty($form['values']['plinker']['encrypted']) ? 'true' : 'false')."
    ],

    // database connection
    // default: sqlite:'.__DIR__.'/database.db
    'database' => [
        'dsn' => '{$form['values']['database']['dsn']}',
        'username' => '{$form['values']['database']['username']}',
        'password' => '{$form['values']['database']['password']}',
        'freeze' => ".(!empty($form['values']['database']['freeze']) ? 'true' : 'false').",
        'debug' => ".(!empty($form['values']['database']['debug']) ? 'true' : 'false')."
    ],

    // displays output to consoles
    'debug' => true,

    // daemon sleep time
    'sleep_time' => {$form['values']['sleep_time']},

    // webui login
    'webui' => [
        'user' => '{$form['values']['webui']['user']}',
        'pass' => '{$form['values']['webui']['pass']}'
    ]
];

//
date_default_timezone_set('Europe/London');

// define debug error reporting/output
if (\$config['debug'] === true) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
}");
    }
}

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
                    <div class="form-group<?= (!empty($form['errors']['webui']['user']) ? ' has-error has-feedback' : '') ?>">
                        <label for="webui-user" class="control-label col-xs-2">Username</label>
                        <div class="col-xs-8">
                            <input type="text" class="form-control" id="webui-user" name="webui[user]" value="<?= (!empty($form['values']['webui']['user']) ? stripslashes(htmlentities($form['values']['webui']['user'])) : '') ?>" placeholder="Username...">
                            <?php if (!empty($form['errors']['webui']['user'])): ?>
                            <span class="glyphicon glyphicon-warning-sign form-control-feedback"></span>
                            <span class="help-block"><?= $form['errors']['webui']['user'] ?></span>
                            <?php endif ?>
                        </div>
                    </div>
                    <div class="form-group<?= (!empty($form['errors']['pass']) ? ' has-error has-feedback' : '') ?>">
                        <label for="webui-pass" class="control-label col-xs-2">Password</label>
                        <div class="col-xs-8">
                            <input type="text" class="form-control" id="webui-pass" name="webui[pass]" value="<?= (!empty($form['values']['webui']['pass']) ? htmlentities($form['values']['webui']['pass']) : '') ?>" placeholder="Password...">
                            <?php if (!empty($form['errors']['webui']['pass'])): ?>
                            <span class="glyphicon glyphicon-warning-sign form-control-feedback"></span>
                            <span class="help-block"><?= $form['errors']['webui']['pass'] ?></span>
                            <?php endif ?>
                        </div>
                    </div>

                    <h4>Plinker Server</h4>
                    <hr>
                    <div class="form-group<?= (!empty($form['errors']['plinker']['peer']) ? ' has-error has-feedback' : '') ?>">
                        <label for="plinker-peer" class="control-label col-xs-2">Peer</label>
                        <div class="col-xs-8">
                            <input type="text" class="form-control" id="plinker-peer" name="plinker[peer]" value="<?= (!empty($form['values']['plinker']['peer']) ? htmlentities($form['values']['plinker']['peer']) : '') ?>" placeholder="Peer URL...">
                            <?php if (!empty($form['errors']['plinker']['peer'])): ?>
                            <span class="glyphicon glyphicon-warning-sign form-control-feedback"></span>
                            <span class="help-block"><?= $form['errors']['plinker']['peer'] ?></span>
                            <?php endif ?>
                        </div>
                    </div>
                    <div class="form-group<?= (!empty($form['errors']['plinker']['tracker']) ? ' has-error has-feedback' : '') ?>">
                        <label for="plinker-tracker" class="control-label col-xs-2">Tracker</label>
                        <div class="col-xs-8">
                            <input type="text" class="form-control" id="plinker-tracker" name="plinker[tracker]" value="<?= (!empty($form['values']['plinker']['tracker']) ? htmlentities($form['values']['plinker']['tracker']) : '') ?>" placeholder="Tracker URL...">
                            <?php if (!empty($form['errors']['plinker']['tracker'])): ?>
                            <span class="glyphicon glyphicon-warning-sign form-control-feedback"></span>
                            <span class="help-block"><?= $form['errors']['plinker']['tracker'] ?></span>
                            <?php endif ?>
                        </div>
                    </div>
                    <div class="form-group<?= (!empty($form['errors']['plinker']['public_key']) ? ' has-error has-feedback' : '') ?>">
                        <label for="plinker-public_key" class="control-label col-xs-2">Public Key</label>
                        <div class="col-xs-8">
                            <input type="text" class="form-control" id="plinker-public_key" name="plinker[public_key]" value="<?= (!empty($form['values']['plinker']['public_key']) ? htmlentities($form['values']['plinker']['public_key']) : '') ?>" placeholder="Public Key...">
                            <?php if (!empty($form['errors']['plinker']['public_key'])): ?>
                            <span class="glyphicon glyphicon-warning-sign form-control-feedback"></span>
                            <span class="help-block"><?= $form['errors']['plinker']['public_key'] ?></span>
                            <?php endif ?>
                        </div>
                    </div>
                    <div class="form-group<?= (!empty($form['errors']['plinker']['private_key']) ? ' has-error has-feedback' : '') ?>">
                        <label for="plinker-private_key" class="control-label col-xs-2">Private Key</label>
                        <div class="col-xs-8">
                            <input type="text" class="form-control" id="plinker-private_key" name="plinker[private_key]" value="<?= (!empty($form['values']['plinker']['private_key']) ? htmlentities($form['values']['plinker']['private_key']) : '') ?>" placeholder="Private Key...">
                            <?php if (!empty($form['errors']['plinker']['private_key'])): ?>
                            <span class="glyphicon glyphicon-warning-sign form-control-feedback"></span>
                            <span class="help-block"><?= $form['errors']['plinker']['private_key'] ?></span>
                            <?php endif ?>
                        </div>
                    </div>
                    <div class="form-group<?= (!empty($form['errors']['plinker']['encrypted']) ? ' has-error' : '') ?>">
                        <label for="plinker-encrypted" class="control-label col-xs-2">Encrypted</label>
                        <div class="col-xs-8">
                            <div class="checkbox">
                                <label><input type="checkbox" name="plinker[encrypted]" value="1"<?= (!empty($form['values']['plinker']['encrypted']) ? ' checked' : '') ?>></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group<?= (!empty($form['errors']['plinker']['enabled']) ? ' has-error' : '') ?>">
                        <label for="plinker-enabled" class="control-label col-xs-2">Enabled</label>
                        <div class="col-xs-8">
                            <div class="checkbox">
                                <label><input type="checkbox" name="plinker[enabled]" value="1"<?= (!empty($form['values']['plinker']['enabled']) ? ' checked' : '') ?>></label>
                            </div>
                        </div>
                    </div>

                    <h4>Daemon</h4>
                    <hr>
                    <div class="form-group<?= (!empty($form['errors']['sleep_time']) ? ' has-error has-feedback' : '') ?>">
                        <label for="sleep_time" class="control-label col-xs-2">Sleep time</label>
                        <div class="col-xs-8">
                            <input type="text" class="form-control" id="sleep_time" name="sleep_time" value="<?= (!empty($form['values']['sleep_time']) ? htmlentities($form['values']['sleep_time']) : '') ?>" placeholder="Number of seconds to sleep between task cycles...">
                            <?php if (!empty($form['errors']['sleep_time'])): ?>
                            <span class="glyphicon glyphicon-warning-sign form-control-feedback"></span>
                            <span class="help-block"><?= $form['errors']['sleep_time'] ?></span>
                            <?php endif ?>
                        </div>
                    </div>

                    <h4>Database</h4>
                    <hr>
                    <div class="form-group<?= (!empty($form['errors']['database']['dsn']) ? ' has-error has-feedback' : '') ?>">
                        <label for="database-dns" class="control-label col-xs-2">DSN</label>
                        <div class="col-xs-8">
                            <input type="text" class="form-control" id="database-dns" name="database[dsn]" value="<?= (!empty($form['values']['database']['dsn']) ? htmlentities($form['values']['database']['dsn']) : '') ?>" placeholder="sqlite:/var/www/html/database.db...">
                            <?php if (!empty($form['errors']['database']['dsn'])): ?>
                            <span class="glyphicon glyphicon-warning-sign form-control-feedback"></span>
                            <span class="help-block"><?= $form['errors']['database']['dsn'] ?></span>
                            <?php endif ?>
                        </div>
                    </div>
                    <div class="form-group<?= (!empty($form['errors']['database']['username']) ? ' has-error has-feedback' : '') ?>">
                        <label for="database-username" class="control-label col-xs-2">Username</label>
                        <div class="col-xs-8">
                            <input type="text" class="form-control" id="database-username" name="database[username]" value="<?= (!empty($form['values']['database']['username']) ? htmlentities($form['values']['database']['username']) : '') ?>" placeholder="Username...">
                            <?php if (!empty($form['errors']['database']['username'])): ?>
                            <span class="glyphicon glyphicon-warning-sign form-control-feedback"></span>
                            <span class="help-block"><?= $form['errors']['database']['username'] ?></span>
                            <?php endif ?>
                        </div>
                    </div>
                    <div class="form-group<?= (!empty($form['errors']['database']['password']) ? ' has-error has-feedback' : '') ?>">
                        <label for="database-password" class="control-label col-xs-2">Password</label>
                        <div class="col-xs-8">
                            <input type="text" class="form-control" id="database-password" name="database[password]" value="<?= (!empty($form['values']['database']['password']) ? htmlentities($form['values']['database']['password']) : '') ?>" placeholder="Password...">
                            <?php if (!empty($form['errors']['database']['password'])): ?>
                            <span class="glyphicon glyphicon-warning-sign form-control-feedback"></span>
                            <span class="help-block"><?= $form['errors']['database']['password'] ?></span>
                            <?php endif ?>
                        </div>
                    </div>
                    <div class="form-group<?= (!empty($form['errors']['database']['freeze']) ? ' has-error' : '') ?>">
                        <label for="database-freeze" class="control-label col-xs-2">Freeze</label>
                        <div class="col-xs-8">
                            <div class="checkbox">
                                <label><input type="checkbox" name="database[freeze]" value="1"<?= (!empty($form['values']['database']['freeze']) ? ' checked' : '') ?>></label>
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