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
    $taskslogs  = $tasks->getTasksLog((int) $vars['route']['id']);
} else {
    $tasksource =  $vars['db']->findOne('tasksource', 'id = ?', [(int) $vars['route']['id']]);
    $taskslogs  =  $vars['db']->findAll('tasks', 'tasksource_id = ? ORDER BY id DESC', [(int) $vars['route']['id']]);
}

/**
 * Javascript
 */
ob_start() ?>
<link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet"/>
<script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
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
            
            editor.setReadOnly(true);
            editor.setOptions({
                minLines: 10,
                maxLines: Infinity
            });
            editor.renderer.setShowGutter(false);
            editor.setShowPrintMargin(false);
            editor.setHighlightActiveLine(false);

        $.fn.editable.defaults.mode = 'inline';
        
        $('.editable-input').editable({
            showbuttons: false,
            ajaxOptions: {
                dataType: 'json'
            },
            success: function(response, newValue) {
                if (!response) {
                    return "Unknown error!";
                }          
                
                if (response.success === false) {
                     return response.msg;
                }
            }      
        });
        
        $('.repeats-select').editable({
            // prepend: "not selected",
            source: [
                {value: '0', text: 'No'},
                {value: '1', text: 'Yes'}
            ],
            ajaxOptions: {
                dataType: 'json'
            },
            success: function(response, newValue) {
                if (!response) {
                    return "Unknown error!";
                }          
                
                if (response.success === false) {
                     return response.msg;
                }
            }      
        });
        
        $("tr[class^='result-']").hide();
        $('.toggle-result').on('click', function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            
            $('.result-'+id).toggle();
        });
        
       
        load.script('/js/module/tasks.js', function() {
            nodes.init();
        });
    });
</script>
<?php $vars['js'] .= ob_get_clean() ?>

<?php if (!empty($node)): ?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            Task <small> - <?= $node->name ?></small>
        </h1>
        <ol class="breadcrumb">
            <li>
                <a href="/" class="ajax-link"><i class="fa fa-dashboard"></i> Dashboard</a>
            </li>
            <li>
                <a href="/nodes" class="ajax-link"><i class="fa fa-server"></i> Nodes</a>
            </li>
            <li>
                <a href="/nodes/<?= $node->id ?>" class="ajax-link"><i class="fa fa-server"></i> <?= $node->name ?></a>
            </li>
            <li class="active">
                Task
            </li>
        </ol>
    </div>
</div>
<?php else: ?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            Task <small> - <?= $tasksource->name ?><?= (!empty($node) ? ' on ('.htmlentities($node->name).')' : '') ?></small>
        </h1>
        <ol class="breadcrumb">
            <li>
                <a href="/" class="ajax-link"><i class="fa fa-dashboard"></i> Dashboard</a>
            </li>
            <li>
                <a href="/tasks" class="ajax-link"><i class="fa fa-cogs"></i> Tasks</a>
            </li>
            <li class="active">
                <i class="fa fa-cog"></i> Task
            </li>
        </ol>
    </div>
</div>
<?php endif ?>

<?php if (!empty($_SESSION['alert'])): ?>
<div class="alert alert-<?= $_SESSION['alert'][0] ?>">
    <a href="#" class="close" data-dismiss="alert">&times;</a>
    <?= $_SESSION['alert'][1] ?>
</div>
<?php unset($_SESSION['alert']); endif ?>

<?php if (!file_exists('../tasks/pids/Daemon.pid')): ?>
<div class="alert alert-warning">
    <a href="#" class="close" data-dismiss="alert">&times;</a>
    It appears the task agent is not currently running. <a href="./<?= $tasksource->id ?>">Click here to reload</a>, if this message persists check cron task is correctly configured and that no tasks are breaking it.<br>
    <code class="code">* * * * * cd /var/www/html/tasks && /usr/bin/php /var/www/html/tasks/run.php >/dev/null 2>&1</code>
</div>
<?php endif ?>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-info-circle fa-fw"></i> Task Details</h3>
                <div class="panel-buttons text-right">
                    <a href="/tasks/edit/<?= (int) $vars['route']['id'] ?>" class="btn btn-xs btn-primary ajax-link"><i class="fa fa-pencil"></i> Edit Task</a>
                    <a href="/tasks/remove/task/<?= (int) $vars['route']['id'] ?>" class="btn btn-xs btn-danger ajax-link"><i class="fa fa-trash"></i> Remove Task</a>
                    <a href="/tasks/run/<?= (int) $vars['route']['id'] ?>" class="btn btn-xs btn-success ajax-link"><i class="fa fa-play"></i> Run Task</a>
                </div>
            </div>
            <div class="panel-body nopadding">
                <div class="table-responsive">
                    <table class="table table-condensed form-table">
                        <tbody>
                            <?php foreach ($tasksource as $col => $value): 
                            // extract source from closure
                            if ($col == 'source') {
                               preg_match('/"function";s:\d+:"(.*)";s:\d+:/smi', $value, $matches);
                               
                               $value = (!empty($matches[1]) ? $matches[1] : $value);
                            }
                            
                            if ($col == 'params') {
                                $col = 'Parameters';
                            }
                            
                            ?>
                            <tr>
                                <td class="col-md-2"><?= ucfirst($col) ?></td>
                                <td class="col-md-10"><?= ($col == 'source' ? '
                                    <textarea class="form-control form-textarea" rows="10" id="input-source" name="source">'.htmlentities($value).'</textarea>
                                    <div id="source" style="position: relative;height: 400px;width: 100%"></div>
                                ' : $value) ?></td>
                            </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-info-circle fa-fw"></i> Tasks Queue</h3>
            </div>
            <div class="panel-body nopadding">
                <div class="table-responsive">
                    <table class="table table-condensed">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Parameters</th>
                                <th>Repeats</th>
                                <th>Sleep</th>
                                <th>Run Count</th>
                                <th>Run Last</th>
                                <th>Run Next</th>
                                <th>Completed</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($taskslogs as $row): 
                            // extract source from closure
                            // if ($col == 'source') {
                            //   preg_match('/"function";s:\d+:"(.*)";s:\d+:/smi', $value, $matches);
                            //   $value = $matches[1];
                            // }
                            $params = json_decode($row->params);
                            if (is_array($params)) {
                                $params = json_encode($params);
                            }
                            ?>
                            <tr>
                                <td><?= $row->id ?></td>
                                <td><?= $row->name ?></td>
                                <td><?= $params ?></td>
                                <td><a href="#" class="repeats-select" data-type="select" data-pk="<?= $row->id ?>" data-name="repeats" data-value="<?= $row->repeats ?>" data-url="/tasks/inline_update/queue/<?= $row->id ?>" data-title="Set sleep time in seconds."></a></td>
                                <td>
                                    <a href="#" class="editable-input" data-type="text" data-pk="<?= $row->id ?>" data-name="sleep" data-value="<?= $row->sleep ?>" data-url="/tasks/inline_update/queue/<?= $row->id ?>" data-title="Set sleep time in seconds."></a>
                                </td>
                                <td><?= $row->run_count ?></td>
                                <td><?= $row->run_last ?></td>
                                <td><?= (empty($row->run_next) ? '-' : $row->run_next) ?></td>
                                <td><?= $row->completed ?></td>
                                <td>
                                    <div class="btn-group btn-group-xs" style="display:flex">
                                        <a href="#" class="btn btn-info toggle-result" data-id="<?= $row->id ?>"><i class="fa fa-eye"></i></a>
                                        <a href="/tasks/remove/queue/<?= $row->id ?>" class="btn btn-danger ajax-link"><i class="fa fa-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                            <tr class="result-<?= $row->id ?>">
                                <td colspan="10">
                                    <?php if (empty($row->completed) && empty($row->result)): ?>
                                    Task waiting in queue for completion, <a href="./<?= $tasksource->id ?>">click here to reload</a>.
                                    <?php else: ?>
                                    <pre><?php echo htmlentities($row->result) ?></pre>
                                    <?php endif ?>
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