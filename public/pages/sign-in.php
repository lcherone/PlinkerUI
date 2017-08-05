<?php
$form = [
    'errors' => [],
    'values' => []
];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($_POST['username'])) {
        $form['errors']['username'] = 'Username is a required field.';
    } else {
        $form['values']['username'] = trim($_POST['username']); 
    }
    
    if (empty($_POST['password'])) {
        $form['errors']['password'] = 'Username is a required field.';
    } else {
        $form['values']['password'] = trim($_POST['password']); 
    }
    
    if (empty($form['errors'])) {
        if ($vars['webui']['user'] != $_POST['username'] || $vars['webui']['pass'] != $_POST['password']) {
            $form['errors']['global'] = 'Incorrect username or password.';
        } else {
            $_SESSION['user'] = true;
            redirect('/');
        }
    }
}
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            Sign In <small> - Authorised users only.</small>
        </h1>
        <ol class="breadcrumb">
            <li class="active">
                <i class="fa fa-dashboard"></i> Dashboard
            </li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-md-4"></div>
    <div class="col-md-4">
        <div class="login-panel panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Sign In</h3>
            </div>
            <div class="panel-body">
                <form method="post" action="">
                    <?php if (!empty($form['errors']['global'])): ?>
                    <div class="alert alert-danger">
                        <a href="#" class="close" data-dismiss="alert">&times;</a>
                        <?= $form['errors']['global'] ?>
                    </div>
                    <?php endif ?>
                    <fieldset>
                        <div class="form-group<?= (!empty($form['errors']['username']) ? ' has-error has-feedback' : '') ?>">
                            <input class="form-control" value="<?= (!empty($form['values']['username']) ? htmlentities($form['values']['username']) : '') ?>" placeholder="Enter username..." name="username" type="text" autofocus="">
                            <?php if (!empty($form['errors']['username'])): ?><span class="glyphicon glyphicon-warning-sign form-control-feedback"></span><?php endif ?>
                            <?php if (!empty($form['errors']['username'])): ?><span class="help-block"><?= $form['errors']['username'] ?></span><?php endif ?>
                        </div>
                        <div class="form-group<?= (!empty($form['errors']['password']) ? ' has-error has-feedback' : '') ?>">
                            <input class="form-control" placeholder="Enter password..." name="password" type="password">
                            <?php if (!empty($form['errors']['password'])): ?><span class="glyphicon glyphicon-warning-sign form-control-feedback"></span><?php endif ?>
                            <?php if (!empty($form['errors']['password'])): ?><span class="help-block"><?= $form['errors']['password'] ?></span><?php endif ?>
                        </div>

                        <button type="submit" class="btn btn-sm btn-success">Sign In</button>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-4"></div>
</div>

<?php ob_start() ?>
<script>
    $(document).ready(function() {
        load.script('/js/module/index.js', function(){
           
        });
    });
</script>
<?php $vars['js'] .= ob_get_clean() ?>
