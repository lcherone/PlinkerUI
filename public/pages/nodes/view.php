<?php

$node =  $vars['db']->findOne('node', 'id = ?', [(int) $vars['route']['id']]);

$_SESSION['node'] = $node->id;

if (empty($node)) {
    alert('danger', '<strong>Error:</strong> Node not found.');
    redirect('/nodes');
}

$error = [];
$tasks = new Plinker\Core\Client(
    $node->peer,
    'Tasks\Manager',
    hash('sha256', gmdate('h').$node->public_key),
    hash('sha256', gmdate('h').$node->private_key),
    json_decode($node->config, true),
    $node->encrypted // enable encryption [default: true]
);

$system = new \Plinker\System\System();

$function = function ($params = []) use ($system) {
    // expected dependencies, imported from use above
    // $system = new \Plinker\System\System();

    $return = [];

    if ($params[0] == 'system_updates') {   $return[@$params[0]] = $system->system_updates(); }
    if ($params[0] == 'disk_space') {       $return[@$params[0]] = $system->disk_space([@$params[1]]); }
    if ($params[0] == 'memory_stats') {     $return[@$params[0]] = $system->memory_stats(); }
    if ($params[0] == 'memory_total') {     $return[@$params[0]] = $system->memory_total(); }
    if ($params[0] == 'server_cpu_usage') { $return[@$params[0]] = $system->server_cpu_usage(); }
    if ($params[0] == 'machine_id') {       $return[@$params[0]] = $system->machine_id(); }
    if ($params[0] == 'netstat') {          $return[@$params[0]] = $system->netstat(); }
    if ($params[0] == 'arch') {             $return[@$params[0]] = $system->arch(); }
    if ($params[0] == 'hostname') {         $return[@$params[0]] = $system->hostname(); }
    if ($params[0] == 'logins') {           $return[@$params[0]] = $system->logins(); }
    if ($params[0] == 'pstree') {           $return[@$params[0]] = $system->pstree(); }
    if ($params[0] == 'top') {              $return[@$params[0]] = $system->top(); }
    if ($params[0] == 'uname') {            $return[@$params[0]] = $system->uname(); }
    if ($params[0] == 'cpuinfo') {          $return[@$params[0]] = $system->cpuinfo(); }
    if ($params[0] == 'netusage') {         $return[@$params[0]] = $system->netusage(); }
    if ($params[0] == 'load') {             $return[@$params[0]] = $system->load(); }
    if ($params[0] == 'disks') {            $return[@$params[0]] = $system->disks(); }
    if ($params[0] == 'uptime') {           $return[@$params[0]] = $system->uptime([@$params[1]]); }
    if ($params[0] == 'ping') {             $return[@$params[0]] = $system->ping([@$params[1]]); }
    if ($params[0] == 'distro') {           $return[@$params[0]] = $system->distro(); }
    if ($params[0] == 'drop_cache') {       $return[@$params[0]] = $system->drop_cache(); }
    if ($params[0] == 'clear_swap') {       $return[@$params[0]] = $system->clear_swap(); }
    if ($params[0] == 'reboot') {           $return[@$params[0]] = $system->reboot(); }
    if ($params[0] == 'check_updates') {    $return[@$params[0]] = $system->check_updates(); }

    return json_encode($return);
};

// // create the task on the server - we call it system
try {
    $tasks->create(
        // name
        'System Information',
        // source
        serialize(new \Opis\Closure\SerializableClosure($function)),
        // type
        'serializableclosure',
        // description
        'System task - Collects system metrics - Hooks into official Plinker System component.'
    );
} catch (\Exception $e) {
    if ($e->getMessage() == 'Unauthorised') {
        alert('warning', '<strong>Error:</strong> Connected successfully but could not authenticate! Check public and private keys.</pre>');
        redirect('/nodes');
    }
    alert('danger', '<strong>Error:</strong> '.str_replace('Could not unserialize response:', '', '<pre>'.trim(htmlentities($e->getMessage()))).'</pre>');
    redirect('/nodes');
}

/**
 * Javascript
 */
ob_start() ?>
<script src="/js/jquery.simplefilebrowser.js"></script>
<script>
$(function(){
    
    var textarea = $('textarea[name="source"]').hide();
            var editor = ace.edit("source");
            editor.getSession().setUseWorker(false);
            editor.setTheme("ace/theme/eclipse");
            editor.getSession().setMode("ace/mode/php");

            editor.getSession().setValue(textarea.val());
            editor.getSession().on('change', function() {
                textarea.val(editor.getSession().getValue());
            });

	        $("#example1").simpleFileBrowser({
                    json: <?= $tasks->files('/var/www/html') ?>,
                    path: '/',
                    view: 'details',
                    select: false,
                    breadcrumbs: true,
                    onSelect: function (obj, file, folder, type) {
                        $('button.new-file').data('folder', folder);
                    },
                    onOpen: function (obj, file, folder, type) {
                        if (type=='file') {
                            $('.remove-file').removeClass('hidden').data('file', folder+'/'+file);
                            $('.save-file').removeClass('hidden').data('file', folder+'/'+file);
                            $('.new-file').data('file', folder+'/'+file);

                            if (!folder) {
                                folder = '';
                            } else {}

                            loadFile(folder+'/'+file);
                        } else {
                            $('.remove-file').addClass('hidden');
                            $('.save-file').addClass('hidden');
                            $('button.new-file').data('folder', folder);
                        }
                        $('.sfbBreadCrumbs li').first().html('/var/www/html');
                    }
                });
                
                function loadFile(path) {
                    $.get('http://phive.free.lxd.systems/nodes/file/<?= (int) $vars['route']['id']?>'+path, function(data, status){
                        textarea.val(data);
                        editor.getSession().setValue(data);
                        $("#select").html(data);
                    });
                }

                $("input[name='path']").on("change", function () {
                    $input = $(this);

                    $("#example1").simpleFileBrowser("chgOption", {
                        path: $input.val()
                    });
                    $("#example1").simpleFileBrowser("redraw");
                });

    $(document).ready(function() {
        load.script('/js/module/nodes.js', function() {
            nodes.init();
        });
        
        
        $('button.remove-file').on('click', function(e){
            e.preventDefault();
            if (!$(this).data('file')) {
                $(this).data('file', '');
            }
            $.get('http://phive.free.lxd.systems/nodes/file/<?= (int) $vars['route']['id']?>'+$(this).data('file')+'?del=1', function(data, status) {
                window.location = '/nodes/view/<?= (int) $vars['route']['id']?>';
            });
        });
        
        $('button.save-file').on('click', function(e){
            e.preventDefault();
            if (!$(this).data('file')) {
                $(this).data('file', '');
            }
            $.post('http://phive.free.lxd.systems/nodes/file/<?= (int) $vars['route']['id']?>'+$(this).data('file')+'?save=1', {data: editor.getSession().getValue() }, function(data, status) {
                //window.location = '/nodes/view/<?= (int) $vars['route']['id']?>';
            });
        });
        
        $('button.new-file').on('click', function(e){
            e.preventDefault();
            if (!$(this).data('folder')) {
                $(this).data('folder', '');
            }
            var new_file = $(this).data('folder')+'/'+$('#new-file-name').val();

            $.get('http://phive.free.lxd.systems/nodes/file/<?= (int) $vars['route']['id']?>'+new_file, function(data, status) {
                window.location = '/nodes/view/<?= (int) $vars['route']['id']?>';
            });
        });
        
        $('.sfbBreadCrumbs').first('li').html('/var/www/html');
        
        
    });
});
</script>
<?php $vars['js'] .= ob_get_clean() ?>

<style>
.sfb{overflow-y:auto;font-size:12px}.sfb ul{list-style:none}.sfb .sfbBreadCrumbs{background:#f5f5f5;margin:0!important;padding:5px 10px}.sfb .sfbBreadCrumbs li{display:inline-block;cursor:pointer;margin:0}.sfb .sfbBreadCrumbs li:before{content:"/";color:#bbb;padding:0 10px}.sfb .sfbBreadCrumbs li:first-child:before{content:"";padding:0}.sfb .sfbContent ul{padding:0;margin:0}.sfb .sfbContent li{cursor:pointer;margin:0;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box}.sfb .sfbContent li.selected{background:#eee}.sfb .sfbContent.icon i{display:block}.sfb .sfbContent.icon li{display:inline-block;text-align:center}.sfb .sfbContent.icon span{width:100%;overflow:hidden;text-overflow:ellipsis;-webkit-line-clamp:2;display:-webkit-box;-webkit-box-orient:vertical;word-wrap:break-word;line-height:1.2}.sfb .sfbContent.details li{padding:5px 10px;display:block;overflow:hidden;text-overflow:ellipsis;word-wrap:break-word}.sfb .sfbContent.details span{padding-left:10px}.sfb.x32 .sfbContent.icon li{width:80px;height:87px;padding:10px}.sfb.x32 .sfbContent.icon i{font-size:32px}.sfb.x32 .sfbContent.icon span{margin-top:5px;height:30px;font-size:12px}.sfb.x22 .sfbContent.icon{padding:5px}.sfb.x22 .sfbContent.icon li{width:70px;height:63px;padding:5px}.sfb.x22 .sfbContent.icon i{font-size:22px}.sfb.x22 .sfbContent.icon span{margin-top:5px;height:25px;font-size:11px}.sfb.x16 .sfbContent.icon{padding:5px}.sfb.x16 .sfbContent.icon li{width:64px;height:55px;padding:5px}.sfb.x16 .sfbContent.icon i{font-size:16px}.sfb.x16 .sfbContent.icon span{margin-top:5px;height:25px;font-size:11px}.sfb .sfbContent.details i{font-size:16px;diplay:inline-block;vertical-align:middle}
</style>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            Node <small> - <?= $node->name ?></small>
        </h1>
        <ol class="breadcrumb">
            <li>
                <a href="/" class="ajax-link"><i class="fa fa-dashboard"></i> Dashboard</a>
            </li>
            <li>
                <a href="/nodes" class="ajax-link"><i class="fa fa-server"></i> Nodes</a>
            </li>
            <li class="active">
                <?= $node->name ?>
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
                <h3 class="panel-title"><i class="fa fa-info-circle fa-fw"></i> Tasks</h3>
                <div class="panel-buttons text-right">
                    <div class="btn-group-xs">
                        <a href="/tasks/new" class="btn btn-success ajax-link">New Task</a>
                    </div>
                </div>
            </div>
            <div class="panel-body nopadding">
                <div class="table-responsive">
                    <table class="table table-condensed form-table">
                        <thead>
                            <tr>
                                <th class="col-xs-1">ID</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Source Size (bytes)</th>
                                <th>Checksum</th>
                                <th>Created</th>
                                <th>Updated</th>
                                <th>Tasks</th>
                                <th style="width:1%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tasks->getTaskSources() as $row): ?>
                            <tr>
                                <td><?= $row->id ?></td>
                                <td><a href="/tasks/view/<?= $row->id ?>"><?= $row->name ?></a></td>
                                <td><?= $row->type ?></td>
                                <td><?= strlen($row->source) ?></td>
                                <td><pre><?= $row->checksum ?></pre></td>
                                <td><?= $row->created ?></td>
                                <td><?= $row->updated ?></td>
                                <td><?= $tasks->getTasksLogCount($row->id) ?></td>
                                <td>
                                    <div class="btn-group" style="display:flex">
                                        <a href="/tasks/run/<?= $row->id ?>?return=/nodes/view/<?= $node->id ?>" class="btn btn-xs btn-success ajax-link"><i class="fa fa-play"></i></a>
                                        <a href="/tasks/edit/<?= $row->id ?>?return=/nodes/view/<?= $node->id ?>" class="btn btn-xs btn-primary ajax-link"><i class="fa fa-pencil"></i></a>
                                        <a href="/tasks/remove/task/<?= $row->id ?>?return=/nodes/view/<?= $node->id ?>" class="btn btn-xs btn-danger ajax-link"><i class="fa fa-times"></i></a>
                                    </div>
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

<div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-files-o" aria-hidden="true"></i> Files</h3>
                <div class="panel-buttons text-right" style="height:22px">
                    <div class="btn-group-xs">
                        <button type="button" class="btn btn-success save-file hidden"><i class="fa fa-floppy-o" aria-hidden="true"></i> Save</button>
                    </div>
                </div>
            </div>
            <div class="panel-body nopadding">
               
                <div class="col-sm-3 nopadding">
                    
                    <div class="row" style="padding:5px;background:#f5f5f5;margin-left:0px;margin-right:0px">
                        <div class="col-xs-12 col-sm-3">
                            <button style="margin-left:-15px" type="button" class="btn btn-xs btn-danger remove-file hidden"><i class="fa fa-trash" aria-hidden="true"></i> Delete File</button>
                        </div>
                            <div class="col-xs-12 col-sm-9 text-right">
                                <form class="form-inline" style="margin-right:-15px">
                                    <div class="input-group">
                                        <input type="text" class="form-control input-xs" id="new-file-name" value="" placeholder="Enter filename and extension&hellip;" >
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-xs btn-success new-file"><i class="fa fa-file" aria-hidden="true"></i> New File</button>
                                        </span>
                                    </div>
                                </form>

                            </div>
                        </div>
                  

                    <div id="example1" style="width:100%"></div>
                    <!--<div id="select"></div>-->
                    <!--<p class="sep">&nbsp;</p>-->
                    <!--<div class="row">-->
                    <!--    <div class="col-sm-2">View:</div>-->
                    <!--    <div class="col-sm-10">-->
                    <!--        <label><input type="radio" name="view" value="icon_32" checked="checked" /> 32x32</label>-->
                    <!--        <label><input type="radio" name="view" value="icon_22" /> 22x22</label>-->
                    <!--        <label><input type="radio" name="view" value="icon_16" /> 16x16</label>-->
                    <!--        <label><input type="radio" name="view" value="details" /> List</label>-->
                    <!--    </div>-->
                    <!--</div>-->
                    <!--<div class="row">-->
                    <!--    <div class="col-sm-2">Select:</div>-->
                    <!--    <div class="col-sm-10">-->
                    <!--        <label><input type="radio" name="select" value="0" checked="checked" /> No</label>-->
                    <!--        <label><input type="radio" name="select" value="1" /> Yes</label>-->
                    <!--    </div>-->
                    <!--</div>-->
                    <!--<div class="row">-->
                    <!--    <div class="col-sm-2">Path:</div>-->
                    <!--    <div class="col-sm-10">-->
                    <!--        <label><input type="radio" name="path" value="/Metallica - Best of the best" checked="checked" /> Metallica</label>-->
                    <!--        <label><input type="radio" name="path" value="/" /> /</label>-->
                    <!--    </div>-->
                    <!--</div>-->

                    <form class="form-inline example row" role="form">
                        <div class="col-lg-12">
                            <p class="data1"></p>
                        </div>
                    </form>

    </div>
    <div class="col-sm-9 nopadding">

<textarea class="form-control form-textarea" rows="10" id="input-source" name="source"></textarea>
                            <div id="source" style="position: relative;height: 550px;min-height:100%;width: 100%"></div>

    </div>

            </div>
        </div>




<div class="row">
    <div class="col-lg-12">
        <?php /*
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-info-circle fa-fw"></i> Tasks</h3>
            </div>
            <div class="panel-body nopadding">
                <?php $taskSources = $tasks->getTaskSources() ?>
                <div class="table-responsive">
                    <table class="table table-condensed form-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Source Size (bytes)</th>
                                <th>Checksum</th>
                                <th>Created</th>
                                <th>Updated</th>
                                <th>Tasks</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($taskSources as $row): ?>
                            <tr>
                                <td class="col-md-1"><?= $row->id ?></td>
                                <td class="col-md-2"><?= $row->name ?></td>
                                <td class="col-md-2"><?= strlen($row->source) ?></td>
                                <td class="col-md-2"><?= $row->checksum ?></td>
                                <td class="col-md-2"><?= $row->created ?></td>
                                <td class="col-md-2"><?= $row->updated ?></td>
                                <td class="col-md-2"><?= $tasks->getTasksLogCount($row->id) ?></td>
                                <td class="col-md-1"></td>
                            </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-info-circle fa-fw"></i> Tasks</h3>
            </div>
            <div class="panel-body nopadding">
                <?php //$taskSources = $tasks->getTaskSources() ?>
                <?php foreach ($taskSources as $row): ?>
                <div class="table-responsive">
                    <table class="table table-condensed form-table">
                        <tbody>
                            <?php foreach ($row as $col => $value):
                            // extract source from closure
                            if ($col == 'source') {
                               preg_match('/"function";s:\d+:"(.*)";s:\d+:/smi', $value, $matches);
                               $value = $matches[1];
                            }
                            ?>
                            <tr>
                                <td class="col-md-2"><?= ucfirst($col) ?></td>
                                <td class="col-md-10"><?= ($col == 'source' ? '<pre>'.$value.'</pre>' : $value) ?></td>
                            </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
                <?php endforeach ?>
            </div>
        </div>


        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-info-circle fa-fw"></i> Tasks Log</h3>
            </div>
            <div class="panel-body nopadding">
                <?php $getTasksLogs = $tasks->getTasksLog() ?>
                <?php foreach ($getTasksLogs as $row): ?>
                <div class="table-responsive">
                    <table class="table table-condensed form-table">
                        <tbody>
                            <?php foreach ($row as $col => $value):
                            // extract source from closure
                            if ($col == 'source') {
                               preg_match('/"function";s:\d+:"(.*)";s:\d+:/smi', $value, $matches);
                               $value = $matches[1];
                            }
                            ?>
                            <tr>
                                <td class="col-md-2"><?= ucfirst($col) ?></td>
                                <td class="col-md-10"><?= ($col == 'source' ? '<pre>'.$value.'</pre>' : $value) ?></td>
                            </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
                <?php endforeach ?>
            </div>
        </div>
         */?>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-info-circle fa-fw"></i> System Information</h3>
            </div>
            <div class="panel-body nopadding">
                <div class="table-responsive">

<?php /*
                    <h4>System Resource Usage <small class="text-nowrap">- Updated times per minute.</small></h4>
        <div class="box bg-white">
            <div class="table-responsive">
                <table class="table table-condensed form-table" style="margin-top:-1px">
                    <tbody>
                        <tr>
                            <td class="col-xs-1 text-right" style="padding-right:10px"><b>CPU</b></td>
                            <td class="col-xs-11" style="padding:0">
                                <?php $cpu_used = $report['cpu']; ?>
                                <?php $cpu_free = 100-$report['cpu']; ?>

                                <div class="progress" data-placement="bottom" data-toggle="tooltip" href="#" data-original-title="<?= count($report['cpus']).'x '.$report['cpus'][0]['model-name'] ?> @ <?= number_format($report['cpus'][0]['cpu-mhz']/1000, 2) ?>GHz">
                                    <div class="progress-bar progress-bar-danger" style="width: <?= $cpu_used ?>%">
                                        <?= $cpu_used ?>%
                                    </div>
                                    <div class="progress-bar progress-bar-success" style="width: <?= $cpu_free ?>%">
                                        <?= $cpu_free ?>%
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-condensed" style="margin-top:-1px;margin-bottom:0;border:0">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Model</th>
                                                <th>Vendor</th>
                                                <th>Cache Size</th>
                                                <th>Speed</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($report['cpus'] as $cpu): ?>
                                            <tr>
                                                <td style="background:white"><?= $cpu['processor']+1 ?></td>
                                                <td><?= $cpu['model-name'] ?></td>
                                                <td><?= $cpu['vendor-id'] ?></td>
                                                <td><?= $cpu['cache-size'] ?></td>
                                                <td><?= number_format($cpu['cpu-mhz']/1000, 2) ?>GHz</td>
                                            </tr>
                                            <?php endforeach ?>
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-right" style="padding-right:10px"><b>Memory</b></td>
                            <td style="padding:0">
                                <?php
                                $offline = (stristr($report['last_check'], 'days ago') !== false);
                                $mem_used = $report['mem_used'];
                                $mem_cache = $report['mem_cache'];
                                $mem_free = 100 - $mem_used - $mem_cache;
                                ?>
                                <div class="progress" data-placement="bottom" data-toggle="tooltip" href="#" data-original-title="Total memory:">
                                    <div class="progress-bar progress-bar-danger" style="width: <?= $mem_used ?>%">
                                        <?= $mem_used ?>%
                                    </div>
                                    <div class="progress-bar progress-bar-warning" style="width: <?= $mem_cache ?>%">
                                        <?= $mem_cache ?>%
                                    </div>
                                    <div class="progress-bar progress-bar-success" style="width: <?= $mem_free ?>%">
                                        <?= $mem_free ?>%
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-condensed" style="margin-top:-1px;margin-bottom:0;border:0">
                                        <thead>
                                            <tr>
                                                <th>Used</th>
                                                <th>Cached</th>
                                                <th>Free</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td style="background:white"><?= $report['mem_used'] ?>%</td>
                                                <td><?= $report['mem_cache'] ?>%</td>
                                                <td><?= $report['mem_free'] ?>%</td>
                                                <td><?= $report['mem_total']/1000 ?>kB ()</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-right" style="padding-right:10px"><b>Disks</b></td>
                            <td style="padding:0">
                                <div class="progress" data-placement="bottom" data-toggle="tooltip" href="#" data-original-title="Total disk space: ">
                                    <div class="progress-bar progress-bar-danger" style="width: <?= 100-$report['diskfree'] ?>%">
                                        <?= 100-$report['diskfree'] ?>%
                                    </div>
                                    <div class="progress-bar progress-bar-success" style="width: <?= $report['diskfree'] ?>%">
                                        <?= $report['diskfree'] ?>%
                                    </div>
                                </div>
                                <?php $lines = explode(PHP_EOL, $report['disks']); unset($lines[0]); ?>
                                <div class="table-responsive">
                                    <table class="table table-condensed" style="margin-top:-1px;margin-bottom:0;border:0">
                                        <thead>
                                            <tr>
                                                <th>Filesystem</th>
                                                <th>Type</th>
                                                <th>Size</th>
                                                <th>Used</th>
                                                <th>Avail</th>
                                                <th>Use%</th>
                                                <th>Mount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($lines as $disk): $attr = array_filter(explode(' ', $disk)); ?>
                                            <tr>
                                                <?php foreach ($attr as $value): ?>
                                                <td style="background:white"><?= $value ?></td>
                                                <?php endforeach ?>
                                            </tr>
                                            <?php endforeach ?>
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
*/?>


                    <table class="table table-condensed form-table">
                        <tbody>
                            
                            <?php
                            $key        = 'hostname';
                            $params     = [$key];
                            $taskResult = $tasks->run('System Information', $params, 21600);
                            $result     = (!empty($taskResult['result']) ? json_decode($taskResult['result'], true)[$key] : '-');
                            ?>
                            <tr>
                                <td>Hostname<?= (!empty($taskResult['run_last']) ? '<br><small class="text-muted">'.$taskResult['run_last'].'</small>' : '') ?></td>
                                <td><?= $result ?></td>
                            </tr>
                            
                            <?php
                            $key        = 'uname';
                            $params     = [$key];
                            $taskResult = $tasks->run('System Information', $params, 86400);
                            $result     = (!empty($taskResult['result']) ? json_decode($taskResult['result'], true)[$key] : '-');
                            ?>
                            <tr>
                                <td>Uname<?= (!empty($taskResult['run_last']) ? '<br><small class="text-muted">'.$taskResult['run_last'].'</small>' : '') ?></td>
                                <td><?= $result ?></td>
                            </tr>
                            
                            <?php
                            $key        = 'uptime';
                            $params     = [$key];
                            $taskResult = $tasks->run('System Information', $params, 60);
                            $result     = (!empty($taskResult['result']) ? json_decode($taskResult['result'], true)[$key] : '-');
                            ?>
                            <tr>
                                <td>Uptime<?= (!empty($taskResult['run_last']) ? '<br><small class="text-muted">'.$taskResult['run_last'].'</small>' : '') ?></td>
                                <td><?= $result ?></td>
                            </tr>

                            <?php
                            $key        = 'ping';
                            $params     = [$key, 'phive.free.lxd.systems'];
                            $taskResult = $tasks->run('System Information', $params, 300);
                            $result     = (!empty($taskResult['result']) ? json_decode($taskResult['result'], true)[$key] : '-');
                            ?>
                            <tr>
                                <td>Ping<?= (!empty($taskResult['run_last']) ? '<br><small class="text-muted">'.$taskResult['run_last'].'</small>' : '') ?></td>
                                <td><?= $result ?>ms</td>
                            </tr>

                            <?php
                            $key        = 'distro';
                            $params     = [$key];
                            $taskResult = $tasks->run('System Information', $params, 86400);
                            $result     = (!empty($taskResult['result']) ? json_decode($taskResult['result'], true)[$key] : '-');
                            ?>
                            <tr>
                                <td>Distro<?= (!empty($taskResult['run_last']) ? '<br><small class="text-muted">'.$taskResult['run_last'].'</small>' : '') ?></td>
                                <td><?= $result ?></td>
                            </tr>
                            
                            <?php
                            $key        = 'arch';
                            $params     = [$key];
                            $taskResult = $tasks->run('System Information', $params, 86400);
                            $result     = (!empty($taskResult['result']) ? json_decode($taskResult['result'], true)[$key] : '-');
                            ?>
                            <tr>
                                <td>Arch<?= (!empty($taskResult['run_last']) ? '<br><small class="text-muted">'.$taskResult['run_last'].'</small>' : '') ?></td>
                                <td><?= $result ?></td>
                            </tr>

                            <?php
                            $key        = 'load';
                            $params     = [$key];
                            $taskResult = $tasks->run('System Information', $params, 120);
                            $result     = (!empty($taskResult['result']) ? json_decode($taskResult['result'], true)[$key] : '-');
                            ?>
                            <tr>
                                <td>Load<?= (!empty($taskResult['run_last']) ? '<br><small class="text-muted">'.$taskResult['run_last'].'</small>' : '') ?></td>
                                <td><?= $result ?></td>
                            </tr>
                            
                            <?php
                            $key        = 'server_cpu_usage';
                            $params     = [$key];
                            $taskResult = $tasks->run('System Information', $params, 15);
                            $result     = (!empty($taskResult['result']) ? json_decode($taskResult['result'], true)[$key] : '-');
                            ?>
                            <tr>
                                <td>CPU Usage<?= (!empty($taskResult['run_last']) ? '<br><small class="text-muted">'.$taskResult['run_last'].'</small>' : '') ?></td><td>
                                    <div class="progress" data-placement="bottom" data-toggle="tooltip" href="#">
                                        <div class="progress-bar progress-bar-danger" style="width: <?= $result ?>%">
                                            <?= $result ?>%
                                        </div>
                                        <div class="progress-bar progress-bar-success" style="width: <?= 100-$result ?>%">
                                            <?= 100-$result ?>%
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            
                            <?php
                            $key        = 'memory_stats';
                            $params     = [$key];
                            $taskResult = $tasks->run('System Information', $params, 15);
                            $result     = (!empty($taskResult['result']) ? json_decode($taskResult['result'], true)[$key] : '-');
                            ?>
                            <tr>
                                <td class="col-md-2">Memory stats<?= (!empty($taskResult['run_last']) ? '<br><small class="text-muted">'.$taskResult['run_last'].'</small>' : '') ?></td>
                                <td class="col-md-10">
                                    <?php
                                    $mem_used = $result['used'];
                                    $mem_cache = $result['cache'];
                                    $mem_free = 100 - $mem_used - $mem_cache;
                                    ?>
                                    <div class="progress" data-placement="bottom" data-toggle="tooltip" href="#" data-original-title="Total memory:">
                                        <div class="progress-bar progress-bar-danger" style="width: <?= $mem_used ?>%">
                                            <?= $mem_used ?>%
                                        </div>
                                        <div class="progress-bar progress-bar-warning" style="width: <?= $mem_cache ?>%">
                                            <?= $mem_cache ?>%
                                        </div>
                                        <div class="progress-bar progress-bar-success" style="width: <?= $mem_free ?>%">
                                            <?= $mem_free ?>%
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <?php
                            $key        = 'disk_space';
                            $params     = [$key, '/'];
                            $taskResult = $tasks->run('System Information', $params, 60);
                            $result     = (!empty($taskResult['result']) ? json_decode($taskResult['result'], true)[$key] : '-');
                            ?>
                            <tr>
                                <td class="col-md-2">Diskspace<?= (!empty($taskResult['run_last']) ? '<br><small class="text-muted">'.$taskResult['run_last'].'</small>' : '') ?></td>
                                <td class="col-md-10">
                                    <div class="progress" data-placement="bottom" data-toggle="tooltip" href="#" data-original-title="Total disk space: ">
                                        <div class="progress-bar progress-bar-danger" style="width: <?= 100-$result ?>%">
                                            <?= 100-$result ?>%
                                        </div>
                                        <div class="progress-bar progress-bar-success" style="width: <?= $result ?>%">
                                            <?= $result ?>%
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <?php
                            $key        = 'memory_total';
                            $params     = [$key];
                            $taskResult = $tasks->run('System Information', $params, 21600);
                            $result     = (!empty($taskResult['result']) ? json_decode($taskResult['result'], true)[$key] : '-');
                            ?>
                            <tr>
                                <td>Total Memory<?= (!empty($taskResult['run_last']) ? '<br><small class="text-muted">'.$taskResult['run_last'].'</small>' : '') ?></td>
                                <td><pre><?= $result ?></pre></td>
                            </tr>

                            <?php
                            $key        = 'machine_id';
                            $params     = [$key];
                            $taskResult = $tasks->run('System Information', $params, 86400);
                            $result     = (!empty($taskResult['result']) ? json_decode($taskResult['result'], true)[$key] : '-');
                            ?>
                            <tr>
                                <td>Machine ID<?= (!empty($taskResult['run_last']) ? '<br><small class="text-muted">'.$taskResult['run_last'].'</small>' : '') ?></td>
                                <td><pre><?= $result ?></pre></td>
                            </tr>

                            <?php
                            $key        = 'netstat';
                            $params     = [$key, '-pant'];
                            $taskResult = $tasks->run('System Information', $params, 3600);
                            $result     = (!empty($taskResult['result']) ? json_decode($taskResult['result'], true)[$key] : '-');
                            ?>
                            <tr>
                                <td>Netstat<?= (!empty($taskResult['run_last']) ? '<br><small class="text-muted">'.$taskResult['run_last'].'</small>' : '') ?></td>
                                <td><pre><?= $result ?></pre></td>
                            </tr>

                            <?php
                            $key        = 'logins';
                            $params     = [$key];
                            $taskResult = $tasks->run('System Information', $params, 600);
                            $result     = (!empty($taskResult['result']) ? json_decode($taskResult['result'], true)[$key] : '-');
                            ?>
                            <tr>
                                <td>System Logins<?= (!empty($taskResult['run_last']) ? '<br><small class="text-muted">'.$taskResult['run_last'].'</small>' : '') ?></td>
                                <td><pre><?= $result ?></pre></td>
                            </tr>
                            
                            <?php
                            $key        = 'pstree';
                            $params     = [$key];
                            $taskResult = $tasks->run('System Information', $params, 21600);
                            $result     = (!empty($taskResult['result']) ? json_decode($taskResult['result'], true)[$key] : '-');
                            ?>
                            <tr>
                                <td>Process Tree<?= (!empty($taskResult['run_last']) ? '<br><small class="text-muted">'.$taskResult['run_last'].'</small>' : '') ?></td>
                                <td><pre><?= $result ?></pre></td>
                            </tr>

                            <?php
                            $key        = 'top';
                            $params     = [$key];
                            $taskResult = $tasks->run('System Information', $params, 21600);
                            $result     = (!empty($taskResult['result']) ? json_decode($taskResult['result'], true)[$key] : '-');
                            ?>
                            <tr>
                                <td>Top<?= (!empty($taskResult['run_last']) ? '<br><small class="text-muted">'.$taskResult['run_last'].'</small>' : '') ?></td>
                                <td><pre><?= $result ?></pre></td>
                            </tr>

                            <?php
                            $key        = 'cpuinfo';
                            $params     = [$key];
                            $taskResult = $tasks->run('System Information', $params, 21600);
                            $result     = (!empty($taskResult['result']) ? json_decode($taskResult['result'], true)[$key] : '-');
                            ?>
                            <tr>
                                <td>CPU Information<?= (!empty($taskResult['run_last']) ? '<br><small class="text-muted">'.$taskResult['run_last'].'</small>' : '') ?></td>
                                <td><pre><?= $result ?></pre></td>
                            </tr>

                            <?php
                            $key        = 'disks';
                            $params     = [$key];
                            $taskResult = $tasks->run('System Information', $params, 21600);
                            $result     = (!empty($taskResult['result']) ? json_decode($taskResult['result'], true)[$key] : '-');
                            ?>
                            <tr>
                                <td>Disks<?= (!empty($taskResult['run_last']) ? '<br><small class="text-muted">'.$taskResult['run_last'].'</small>' : '') ?></td>
                                <td><pre><?= $result ?></pre></td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

 <?php /*
<div class="row">
    <div class="col-lg-12">
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Public Key</th>
                        <th>Enabled</th>
                        <th>Encrypted</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ([$node] as $row): ?>
                    <tr>
                        <td><a href="/nodes/view/<?= $row->id ?>"><?= $row->name ?></a></td>
                        <td>
                            <?= $row->public_key ?>
                        </td>
                        <td>
                            <?= $row->enabled ?>
                        </td>
                        <td>
                            <?= $row->encrypted ?>
                        </td>
                        <td><a href="" class="btn btn-xs btn-danger"><i class="fa fa-times"></i></a></td>
                    </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
 */?>


<?php return; ?>
