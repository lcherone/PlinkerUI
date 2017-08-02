<?php
namespace Tasks\Lib\Traits;

use RedBeanPHP\R;
use Exception;

trait LXC {

    /**
     *
     */
    public function __exec($cmd)
    {
        // has parameters
        $params = [];
        if (!empty($this->current_task->params)) {
            $params = json_decode($this->current_task->params, true);
        }

        //
        $logged_cmds = [
            'lxc-create', 
            'lxc-destroy'
        ];
        
        // uniqueid
        $uniqid = uniqid();

        // process type
        $type = explode(' ', $cmd)[0];

        //
        if (in_array($type, $logged_cmds)) {

            // match container name from cmd
            preg_match_all(
                '/[-n]+\s{1}(\w+)/i',
                $cmd,
                $container
            );

            // match container template from cmd
            preg_match_all(
                '/[-t]+\s{1}(\w+)/i',
                $cmd,
                $template
            );

            // humanize log msg
            $msg = '';
            if ($type == 'lxc-create') {
                $msg = 'Create container';
            } elseif ($type == 'lxc-destroy') {
                $msg = 'Destroy container';
            }

            $log = $this->model->findOrCreate([
                'log',
                'uniqid' => $uniqid,
                'token' => (!empty($params['token']) ? $params['token'] : ''),
                'msg' => $msg,
                'cmd' => $cmd,
                'date' => date_create()->format('Y-m-d h:i:s')
            ]);

            $log->container = (!empty($container[1][0]) ? $container[1][0] : '-');
            $log->template = (!empty($template[1][0]) ? $template[1][0] : '-');
        }

        flush();
        $process = proc_open($cmd, [
            0 => ["pipe", "r"], // stdin pipe that the child reads from
            1 => ["pipe", "w"], // stdout pipe that the child writes to
            2 => ["pipe", "w"]  // stderr pipe that the child writes to
        ], $pipes, realpath('./'), []);

        if (is_resource($process)) {
            // handle process
            $state = true;

            while ($line = fgets($pipes[1])) {
                if (in_array($type, $logged_cmds)) {
                    $log->ownLogitem[] = $this->model->create([
                        'logitem',
                        'uniqid' => $uniqid,
                        'token' => (!empty($params['token']) ? $params['token'] : ''),
                        'type' => $type,
                        'pipe' => 'stdout',
                        'line' => trim($line)
                    ]);
                    $this->model->store($log);
                }

                echo $line;
                flush(); 
            }
            while ($line = fgets($pipes[2])) {
                $state = false;
                if (in_array($type, $logged_cmds)) {
                    $log->ownLogitem[] = $this->model->create([
                        'logitem',
                        'uniqid' => $uniqid,
                        'token' => (!empty($params['token']) ? $params['token'] : ''),
                        'type' => $type,
                        'pipe' => 'stderr',
                        'line' => trim($line)
                    ]);
                    $this->model->store($log);
                }

                echo $line;
                flush();
            }
        } else {
            $state = false;
        }
        proc_close($process);

        return $state;
    }

	/**
	 * 
	 */
    public function monitor($n = null)
    {
        $args = ($n !== null) ? ' -n ' . $n : null;

        return $this->__exec('lxc-monitor'.$args);      
    }

	/**
	 * 
	 */
    public function create($n = null) 
    {
        if ($n === null) {
            throw new Exception('Container name cannot be blank');
        }

        $args = ($n !== null) ? ' -n ' . $n : null;
        $args .= ' -t ubuntu';

        ob_start();
        $this->__exec('lxc-create'.$args);
        $output = ob_get_contents();
        ob_end_clean();

        $output = trim($output);

        if ($output == 'Container already exists') {
            throw new Exception('Container '.$n.' already exists');
        }

        return true;      
    }

	/**
	 * 
	 */
    public function stop($n = null) 
    {
        if ($n === null) {
            throw new Exception('Container name cannot be blank');
        }

        $args = ($n !== null) ? ' -n ' . $n : null;

        // has parameters
        $params = [];
        if (!empty($this->current_task->params)) {
            $params = json_decode($this->current_task->params, true);
        }

        // log it 
        $this->model->store($this->model->findOrCreate([
            'log',
            'container' => $n,
            'uniqid' => uniqid(),
            'token' => (!empty($params['token']) ? $params['token'] : ''),
            'msg' => 'Stop container',
            'cmd' => 'lxc-stop'.$args,
            'date' => date_create()->format('Y-m-d h:i:s')
        ]));

        return $this->__exec('lxc-stop'.$args);      
    }

	/**
	 * 
	 */
    public function start($n = null) 
    {
        if ($n === null) {
            throw new Exception('Container name cannot be blank');
        }

        $args = ($n !== null) ? ' -n ' . $n : null;
        $args .= ' -d ';

        // has parameters
        $params = [];
        if (!empty($this->current_task->params)) {
            $params = json_decode($this->current_task->params, true);
        }

        // log it 
        $this->model->store($this->model->findOrCreate([
            'log',
            'container' => $n,
            'uniqid' => uniqid(),
            'token' => (!empty($params['token']) ? $params['token'] : ''),
            'msg' => 'Start container',
            'cmd' => 'lxc-start'.$args,
            'date' => date_create()->format('Y-m-d h:i:s')
        ]));

        return $this->__exec('lxc-start'.$args);      
    }

	/**
	 * 
	 */
    public function info($n = null) 
    {
        if ($n === null) {
            throw new Exception('Container name cannot be blank');
        }

        $args = ($n !== null) ? ' -n ' . $n : null;

        ob_start();
        $return = $this->__exec('lxc-info'.$args);
        $output = ob_get_contents();
        ob_end_clean();

        $lines = explode(PHP_EOL, $output);

        $return = [];
        foreach ($lines as $line) {
            preg_match_all(
                '/(.*[:])\s+(.*)/i',
                $output,
                $matches
            );

            foreach ($matches[0] as $key => $match) {
                $return[strtolower(str_replace([' ', ':'], ['_', ''], trim($matches[1][$key])))] = trim($matches[2][$key]);
            }
        }

        return $return;
    }

	/**
	 * 
	 */
    public function destroy($n = null) 
    {
        if ($n === null) {
            throw new Exception('Container name cannot be blank');
        }

        $args = ($n !== null) ? ' -n ' . $n : null;

        ob_start();
        $this->__exec('lxc-destroy'.$args);
        $output = ob_get_contents();
        ob_end_clean();

        $output = trim($output);

        if ($output == 'Container is not defined') {
            throw new Exception('Container '.$n.' does not exist');
        }        

        if ($output == $n.' is running') {
            throw new Exception('Container '.$n.' is running');
        }

        return true;
    }

	/**
	 * 
	 */
    public function wait($n = null, $s = '', $t = 3600) 
    {
        if ($n === null) {
            throw new Exception('Container name cannot be blank');
        }

        $args = ($n !== null) ? ' -n ' . $n : null;
        $args .= ($s !== null) ? ' -s ' . $s : null;
        $args .= ($t !== null) ? ' -t ' . (int) $t : null;

        return $this->__exec('lxc-wait'.$args);      
    }

	/**
	 * 
	 */
    public function freeze($n = null) 
    {
        if ($n === null) {
            throw new Exception('Container name cannot be blank');
        }

        $args = ($n !== null) ? ' -n ' . $n : null;

        // has parameters
        $params = [];
        if (!empty($this->current_task->params)) {
            $params = json_decode($this->current_task->params, true);
        }

        // log it 
        $this->model->store($this->model->findOrCreate([
            'log',
            'container' => $n,
            'uniqid' => uniqid(),
            'token' => (!empty($params['token']) ? $params['token'] : ''),
            'msg' => 'Freeze container',
            'cmd' => 'lxc-freeze'.$args,
            'date' => date_create()->format('Y-m-d h:i:s')
        ]));

        return $this->__exec('lxc-freeze'.$args);      
    }

	/**
	 * 
	 */
    public function unfreeze($n = null) 
    {
        if ($n === null) {
            throw new Exception('Container name cannot be blank');
        }

        $args = ($n !== null) ? ' -n ' . $n : null;

        // has parameters
        $params = [];
        if (!empty($this->current_task->params)) {
            $params = json_decode($this->current_task->params, true);
        }

        // log it 
        $this->model->store($this->model->findOrCreate([
            'log',
            'container' => $n,
            'uniqid' => uniqid(),
            'token' => (!empty($params['token']) ? $params['token'] : ''),
            'msg' => 'Unfreeze container',
            'cmd' => 'lxc-unfreeze'.$args,
            'date' => date_create()->format('Y-m-d h:i:s')
        ]));

        return $this->__exec('lxc-unfreeze'.$args);      
    }     

	/**
	 * 
	 */
    public function copy($n = null, $nn = null) 
    {
        if ($n === null) {
            throw new Exception('Container name cannot be blank');
        }

        $args = ($n !== null) ? ' -o ' . $n : null;
        $args .= ($n !== null) ? ' -n ' . $nn : '-K ';
        
        // has parameters
        $params = [];
        if (!empty($this->current_task->params)) {
            $params = json_decode($this->current_task->params, true);
        }

        // log it 
        $this->model->store($this->model->findOrCreate([
            'log',
            'container' => $n,
            'uniqid' => uniqid(),
            'token' => (!empty($params['token']) ? $params['token'] : ''),
            'msg' => 'Container cloned => '.$nn,
            'cmd' => 'lxc-clone'.$args,
            'date' => date_create()->format('Y-m-d h:i:s')
        ]));

        return $this->__exec('lxc-clone'.$args);      
    }    

	/**
	 * 
	 */
    public function exec($n = null, $c = null) 
    {
        if ($n === null) {
            throw new Exception('Container name cannot be blank');
        }        

        if ($c === null) {
            throw new Exception('Command cannot be empty.');
        }
        
        // has parameters
        $params = [];
        if (!empty($this->current_task->params)) {
            $params = json_decode($this->current_task->params, true);
        }

        // log it 
        $this->model->store($this->model->findOrCreate([
            'log',
            'container' => $n,
            'uniqid' => uniqid(),
            'token' => (!empty($params['token']) ? $params['token'] : ''),
            'msg' => 'Executed Command',
            'cmd' => 'lxc-attach -n '.$n.' -- /bin/bash -c "'.$c.'"',
            'date' => date_create()->format('Y-m-d h:i:s')
        ]));

        return $this->__exec('lxc-attach -n '.$n.' -- /bin/bash -c "'.$c.'"');      
    }

	/**
	 * 
	 */
    public function snapshot($n = null, $a = null, $sn = null) 
    {
        if ($n === null) {
            throw new Exception('Container name cannot be blank');
        }   
        
        if ($a === null) {
            throw new Exception('Action cannot be blank');
        }   

        if ($a == 'create') {
            $cmd = ' -n '.$n;
        } elseif ($a == 'destroy') {
            $cmd = ' -d '.$n;
        } elseif ($a == 'list') {
            $cmd = ' -L';
        } elseif ($a == 'restore') {
            if ($sn === null) {
                throw new Exception('Snapshot name cannot be empty when restoring');
            }
            $cmd = ' -r '.$sn;
        } else {
            throw new Exception('Invalid action');
        }
        
        // has parameters
        $params = [];
        if (!empty($this->current_task->params)) {
            $params = json_decode($this->current_task->params, true);
        }

        // log it 
        $this->model->store($this->model->findOrCreate([
            'log',
            'container' => $n,
            'uniqid' => uniqid(),
            'token' => (!empty($params['token']) ? $params['token'] : ''),
            'msg' => 'Snapshot container',
            'cmd' => 'lxc-snapshot '.$cmd,
            'date' => date_create()->format('Y-m-d h:i:s')
        ]));

        return $this->__exec('lxc-snapshot '.$cmd);      
    }   

	/**
	 * 
	 */
    public function ls()
    {
        ob_start();
        $this->__exec('lxc-ls -f -1 -F "name,state,ipv4,autostart,pid,memory,ram,swap"');
        $output = ob_get_contents();
        ob_end_clean();

        preg_match_all(
            '/(\S+)\s+(RUNNING|STOPPED|FROZEN|STARTING|STOPPING|ABORTING|FREEZING|FROZEN|THAWED)\s+(-|\d+\.\d+\.\d+\.\d+)\s+(YES|NO)\s+(-|\d+)\s+(-|\S+)\s+(-|\S+)\s+(-|\S+)/i',
            $output,
            $matches
        );

        $return = [];
        foreach ($matches[0] as $key => $value) { 
            $return[$key] = [
                'name' => isset($matches[1][$key]) ? $matches[1][$key] : null,
                'state' => isset($matches[2][$key]) ? $matches[2][$key] : null,
                'ipv4' => isset($matches[3][$key]) ? $matches[3][$key] : null,
                'autostart' => isset($matches[4][$key]) ? $matches[4][$key] : null,
                'pid' => isset($matches[5][$key]) ? $matches[5][$key] : null,
                'memory' => isset($matches[6][$key]) ? $matches[6][$key] : null,
                'ram' => isset($matches[7][$key]) ? $matches[7][$key] : null,
                'swap' => isset($matches[8][$key]) ? $matches[8][$key] : null
            ];
        }

        return $return;
    }

	/**
	 * 
	 */
    public function backup($n, $path)
    {
		if (empty($path)) {
            throw new Exception('Backup path cannot be empty.');
		}
		
        // has parameters
        $params = [];
        if (!empty($this->current_task->params)) {
            $params = json_decode($this->current_task->params, true);
        }
        
        // log it 
        $this->model->store($this->model->findOrCreate([
            'log',
            'container' => $n,
            'uniqid' => uniqid(),
            'token' => (!empty($params['token']) ? $params['token'] : ''),
            'msg' => 'Backup container',
            'cmd' => 'tar --numeric-owner -czvf '.$path.' *',
            'date' => date_create()->format('Y-m-d h:i:s')
        ]));

        $cwd = getcwd();
        chdir('/var/lib/lxc/'.$n);
        $this->__exec('tar --numeric-owner -czvf '.$path.' *'); 
        chdir($cwd);
    }    

	/**
	 * 
	 */
    public function restore($container, $path)
    {
        if (file_exists('/var/lib/lxc/'.$container)) {
            throw new \Exception('Container already exists');
        }
        
        if (empty($path)) {
            throw new Exception('Path to backup cannot be empty.');
		}
        
        // has parameters
        $params = [];
        if (!empty($this->current_task->params)) {
            $params = json_decode($this->current_task->params, true);
        }

        // log it 
        $this->model->store($this->model->findOrCreate([
            'log',
            'container' => $n,
            'uniqid' => uniqid(),
            'token' => (!empty($params['token']) ? $params['token'] : ''),
            'msg' => 'Restore container backup',
            'cmd' => 'tar --numeric-owner -xzvf '.$path,
            'date' => date_create()->format('Y-m-d h:i:s')
        ]));

        $cwd = getcwd();
        mkdir('/var/lib/lxc/'.$container);
        chdir('/var/lib/lxc/'.$container);
        $this->__exec('tar --numeric-owner -xzvf '.$path); 
        chdir($cwd);
    }

	/**
	 * 
	 */
    public function ls_backups($path)
    {
		if (empty($path)) {
            throw new Exception('Path cannot be empty on ls_backups() method call.');
		}
		
        return glob($path.'/*');
    }

	/**
	 * 
	 */
    public function rename($container, $new_name)
    {
        // has parameters
        $params = [];
        if (!empty($this->current_task->params)) {
            $params = json_decode($this->current_task->params, true);
        }
        
        // log it 
        $this->model->store($this->model->findOrCreate([
            'log',
            'container' => $container,
            'uniqid' => uniqid(),
            'token' => (!empty($params['token']) ? $params['token'] : ''),
            'msg' => 'Rename container: '.$container.' => '.$new_name,
            'cmd' => '',
            'date' => date_create()->format('Y-m-d h:i:s')
        ]));
        
        // backup directory
        if (!file_exists(RUN_PATH.'/.lxc-tool/backups')) {
			mkdir(RUN_PATH.'/.lxc-tool/backups', 0755 ,true);
		}

        $this->backup($container, RUN_PATH.'/.lxc-tool/backups/'.$container.'.tar.gz');
        $this->restore($new_name, RUN_PATH.'/.lxc-tool/backups/'.$container.'.tar.gz');

        $this->setConfig($new_name, 'lxc.rootfs', '/var/lib/lxc/'.$new_name.'/rootfs');
        $this->setConfig($new_name, 'lxc.mount', '/var/lib/lxc/'.$new_name.'/fstab');
        $this->setConfig($new_name, 'lxc.utsname', $new_name);
    }

	/**
	 * 
	 */
    public function loadConfig($c) 
    {
        if (!file_exists('/var/lib/lxc/'.$c.'/config')) {
            throw new \Exception('Config file not found');
        }

        $return = [
            'meta' => [
				'container' => $c,
				'path' => '/var/lib/lxc/'.$c.'/config',
			],
            'config' => []
        ];

        $src = file_get_contents('/var/lib/lxc/'.$c.'/config');

        if (empty($src)) {
            throw new \Exception('Could not read file contents');
        }

        preg_match_all(
            '/(lxc.*)\s+=\s+([A-Za-z0-9.:\/,_-]+)/i',
            $src,
            $matches
        );

        foreach ($matches[1] as $key => $value) {
            $return['config'][$value] = $matches[2][$key];
        }

        return $return;
    }

	/**
	 * 
	 */
    public function storeConfig($config)
    {
        if (!is_readable($config['meta']['path'])) {
            throw new \Exception('Config file not readable');
        }
             
        if (!file_exists($config['meta']['path'])) {
            throw new \Exception('Config file not found');
        }

        $src = null;
        foreach ($config['config'] as $key => $value) {
            $src .= $key.' = '.$value.PHP_EOL;
        }

        file_put_contents($config['meta']['path'], $src, LOCK_EX);

        return $config;
    }

	/**
	 * 
	 */
    public function setConfig($c, $key, $value) 
    {
        $config = $this->loadConfig($c);

        $config['config'][$key] = $value;

        $this->storeConfig($config);

        return null;
    }

	/**
	 * 
	 */
    public function getConfig($c, $key = null) 
    {
        $config = $this->loadConfig($c);

        if ($key === null) {
            if (isset($config['config'])) {
                return $config['config'];
            }
        } else {
            if (isset($config['config'][$key])) {
                return $config['config'][$key];
            }
        }

        return null;
    }

	/**
	 * 
	 */
    public function destroyBackups($path)
    {   
        if (!file_exists($path)) {
            throw new \Exception('Backup path empty or not a real path.');
        }
        
        if (in_array($path, ['/', '', '.'])) {
            throw new \Exception('Invalid backup path!');
        }

        return $this->__exec('rm -Rf '.$path.'/*');
    }
    
}
