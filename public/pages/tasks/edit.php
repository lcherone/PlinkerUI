<?php

if (!empty($_SESSION['node'])) {
    $node =  $vars['db']->findOne('node', 'id = ?', [(int) $_SESSION['node']]);

    $tasks = new Plinker\Core\Client(
        $node->peer,
        'Tasks\Manager',
        hash('sha256', gmdate('h').$node->public_key),
        hash('sha256', gmdate('h').$node->private_key),
        $vars,
        $node->encrypted // enable encryption [default: true]
    );

    $tasksource = $tasks->getSource((int) $vars['route']['id']);
} else {
    $tasksource =  $vars['db']->findOne('tasksource', 'id = ?', [(int) $vars['route']['id']]);
}

if (empty($tasksource)) {
    alert('danger', 'Invalid task id.');
    redirect('/tasks');
}

if ($tasksource->type == 'serializableclosure') {
    alert('danger', 'You cannot edit that task.');
    redirect('/tasks');
}

$form = [
    'errors' => [],
    'values' => [
        'name'    => (isset($_POST['name'])    ? trim($_POST['name'])   : $tasksource->name),
        'description' => (isset($_POST['description']) ? trim($_POST['description']) : $tasksource->description),
        'type'    => (isset($_POST['type'])    ? trim($_POST['type'])   : $tasksource->type),
        'params'  => (isset($_POST['params'])  ? $_POST['params'] : []),
        'repeats' => (isset($_POST['repeats']) ? 1 : 0),
        'sleep'   => (isset($_POST['sleep'])   ? (int) $_POST['sleep']  : $tasksource->sleep),
        'source'  => (isset($_POST['source'])  ? trim($_POST['source']) : $tasksource->source),
    ]
];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // type
    if (!in_array($form['values']['type'], ['php-closure', 'php-raw', 'bash'])) {
        $form['errors']['type'] = 'Invalid task source type, choose from the list.';
    }

    // name
    if (empty($form['values']['name'])) {
        $form['errors']['name'] = 'Name is a required field.';
    }

    // description
    if (empty($form['values']['description'])) {
        $form['errors']['description'] = 'Description is a required field.';
    } else {
        $form['values']['description'] = strip_tags($form['values']['description']);
    }

    // sleep
    // if (!empty($form['values']['repeats'])) {
    //     if ($form['values']['sleep'] < 1) {
    //         $form['errors']['sleep'] = 'Min sleep value is 1 second.';

    //     } elseif ($form['values']['sleep'] > 31557600) {
    //         $form['errors']['sleep'] = 'Max sleep value is 31557600 seconds. (around 1 year).';
    //     }
    // } else {
    //     $form['values']['sleep'] = 0;
    // }

    // source
    if (empty($form['values']['source'])) {
        $form['errors']['source'] = 'Source is a required field.';
    }

    // all is good
    if (empty($form['errors'])) {

        $error = [];
        if (empty($node->peer)) {
            $tasks = new Plinker\Core\Client(
                $vars['plinker']['tracker'],
                'Tasks\Manager',
                hash('sha256', gmdate('h').$vars['plinker']['public_key']),
                hash('sha256', gmdate('h').$vars['plinker']['private_key']),
                $vars,
                $vars['plinker']['encrypted'] // enable encryption [default: true]
            );
        }

        // php-closure
        if ($form['values']['type'] == 'php-closure') {
            $source = $form['values']['source'];
        } elseif ($form['values']['type'] == 'php-raw') {
            $source = $form['values']['source'];
        } elseif ($form['values']['type'] == 'bash') {
            $source = $form['values']['source'];
        } else {
            alert('danger', 'Invalid task source type, please try again.');
            redirect('/tasks');
        }

        // create the task on the server - we call it system
        //echo '<pre>'.print_r(

        $task = $tasks->update(
            $vars['route']['id'],
            // name
            $form['values']['name'],
            // source
            $source,
            // type
            $form['values']['type'],
            // description
            $form['values']['description'],
            // params
            json_encode($form['values']['params'])
        );

        // , true).'</pre>';

        alert('success', 'Task updated.');
        redirect('/tasks/edit/'.$vars['route']['id']);
    }
}

/**
 * Javascript
 */
ob_start() ?>
<script>
    $(document).ready(function() {
        var textarea = $('textarea[name="source"]').hide();
            var editor = ace.edit("source");
            editor.getSession().setUseWorker(false);
            editor.setTheme("ace/theme/eclipse");
            editor.getSession().setMode("ace/mode/php");

            editor.getSession().setValue(textarea.val());
            editor.getSession().on('change', function() {
                textarea.val(editor.getSession().getValue());
            });
            editor.setOptions({
                minLines: 20,
                maxLines: Infinity
            });
            
                
            $(document).on('click', '.add-row', function(e) {
                            e.preventDefault();

                            var controlForm = $('#fields:first'),
                                currentEntry = $(this).parents('.entry:first'),
                                newEntry = $(currentEntry.clone()).appendTo(controlForm);

                            newEntry.find('input').val('');

                            controlForm.find('.entry:not(:last) .add-row')
                                .removeClass('add-row').addClass('btn-remove')
                                .removeClass('btn-success').addClass('btn-danger')
                                .html('<i class="fa fa-times"></i>')
                                .closest('.input-group').css('paddingBottom', '10px');

                        }).on('click', '.btn-remove', function(e) {
                            e.preventDefault();
                            $(this).parents('.entry:first').remove();
                            return false;
                        });
             

        load.script('/js/module/tasks.js', function() {
            nodes.init();
        });
    });
</script>
<?php $vars['js'] .= ob_get_clean() ?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            Task <small> - Edit<?= (!empty($node) ? ' on ('.htmlentities($node->name).')' : '') ?></small>
        </h1>
        <ol class="breadcrumb">
            <li>
                <a href="/"><i class="fa fa-dashboard"></i> Dashboard</a>
            </li>
            <li>
                <a href="/tasks"><i class="fa fa-cogs"></i> Tasks</a>
            </li>
            <li class="active">
                <i class="fa fa-cog"></i> Edit
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
                <h3 class="panel-title"><i class="fa fa-info-circle fa-fw"></i> Edit Task</h3>
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
                    <div class="form-group<?= (!empty($form['errors']['description']) ? ' has-error has-feedback' : '') ?>">
                        <label for="input-description" class="control-label col-xs-2">Description</label>
                        <div class="col-xs-8">
                            <input type="text" class="form-control" id="input-description" name="description" value="<?= (!empty($form['values']['description']) ? htmlentities($form['values']['description']) : '') ?>" placeholder="Description...">
                            <?php if (!empty($form['errors']['description'])): ?><span class="glyphicon glyphicon-warning-sign form-control-feedback"></span><?php endif ?>
                            <?php if (!empty($form['errors']['description'])): ?><span class="help-block"><?= $form['errors']['description'] ?></span><?php endif ?>
                        </div>
                    </div>
                    <div class="form-group<?= (!empty($form['errors']['type']) ? ' has-error has-feedback' : '') ?>">
                        <label for="input-type" class="control-label col-xs-2">Type</label>
                        <div class="col-xs-8">
                            <select class="form-control" name="type" id="input-type">
                                <option value="php-raw"<?= ($form['values']['type']  == 'php-raw' ? ' selected' : '') ?>>PHP Raw</option>
                                <option value="php-closure"<?= ($form['values']['type']  == 'php-closure' ? ' selected' : '') ?>>PHP Closure</option>
                                <option value="bash"<?= ($form['values']['type']  == 'bash' ? ' selected' : '') ?>>Bash</option>
                            </select>
                            <?php if (!empty($form['errors']['type'])): ?><span class="glyphicon glyphicon-warning-sign form-control-feedback"></span><?php endif ?>
                            <?php if (!empty($form['errors']['type'])): ?><span class="help-block"><?= $form['errors']['type'] ?></span><?php endif ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="field1" class="control-label col-xs-2">Parameter Keys</label>
                        <div class="col-xs-8" id="fields">
                            <div class="input-group entry">
                                <input class="form-control" autocomplete="off" name="params[]" type="text" placeholder="Passed to $params = [...];"/>
                                <span class="input-group-btn">
                                    <button class="btn btn-success add-row" type="button"><i class="fa fa-plus"></i></button>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!--<div class="form-group<?= (!empty($form['errors']['repeats']) ? ' has-error' : '') ?>">-->
                    <!--    <label for="input-repeats" class="control-label col-xs-2">Repeats</label>-->
                    <!--    <div class="col-xs-8">-->
                    <!--        <div class="checkbox">-->
                    <!--            <label><input type="checkbox" name="repeats" value="1"<?= (!empty($form['values']['repeats']) ? ' checked' : '') ?>> Enable</label>-->
                    <!--        </div>-->
                    <!--    </div>-->
                    <!--</div>-->
                    <!--<div class="form-group<?= (!empty($form['errors']['sleep']) ? ' has-error has-feedback' : '') ?>">-->
                    <!--    <label for="input-sleep" class="control-label col-xs-2">Repeat every X seconds</label>-->
                    <!--    <div class="col-xs-8">-->
                    <!--        <input type="number" class="form-control" id="input-sleep" name="sleep" value="<?= (!empty($form['values']['sleep']) ? $form['values']['sleep'] : '') ?>" placeholder="Sleep time in seconds...">-->
                    <!--        <?php //if (!empty($form['errors']['sleep'])): ?><span class="glyphicon glyphicon-warning-sign form-control-feedback"></span><?php //endif ?>-->
                    <!--        <?php //if (!empty($form['errors']['sleep'])): ?><span class="help-block"><?= $form['errors']['sleep'] ?></span><?php //endif ?>-->
                    <!--    </div>-->
                    <!--</div>-->
                    <div class="form-group<?= (!empty($form['errors']['source']) ? ' has-error has-feedback' : '') ?>">
                        <label for="input-source" class="control-label col-xs-2">Task Code</label>
                        <div class="col-xs-8">
                            <textarea class="form-control form-textarea" rows="10" id="input-source" name="source"><?= (!empty($form['values']['source']) ? htmlentities($form['values']['source']) : '') ?></textarea>
                            <div id="source" style="position: relative;height: 380px;width: 100%"></div>

                            <?php if (!empty($form['errors']['source'])): ?><span class="glyphicon glyphicon-warning-sign form-control-feedback"></span><?php endif ?>
                            <?php if (!empty($form['errors']['source'])): ?><span class="help-block"><?= $form['errors']['source'] ?></span><?php endif ?>
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