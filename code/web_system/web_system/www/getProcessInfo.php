<?php
$status = array();
if($_SERVER['REQUEST_METHOD']=='POST' &&  isset($_REQUEST['head']) ){
			switch($_REQUEST['head']){
					case 'check':
							$pid=isset($_REQUEST['pid'])?intval($_REQUEST['pid']):0;
						if($pid!=0){
							//$process->setPid($pid);
							//if($process->isRunning()) {
							if(is_process_running($pid)){
								$status['isrunning'] = true;
							}else{
								$status['isrunning'] = false;
							}
						}
					break;
					case 'stop':
						$pid=isset($_REQUEST['pid'])?intval($_REQUEST['pid']):0;
						if($pid!=0){
							//$process->setPid($pid);
							//if($process->isRunning()) {
							//	 $process->stop();
							if(is_process_running($pid)){
									stop_process($pid);
								 echo 'Process stopped';
							}else{
								echo 'Process not running';
							}
						}	 
					break;
			}
			echo json_encode($status);
			exit;
	}
	function is_process_running($pid){
				//tasklist /FI "PID eq 6480"
			$result = shell_exec('tasklist /FI "PID eq '.$pid.'"' );
			if (count(preg_split("/\n/", $result)) > 0 && !preg_match('/No tasks/', $result)) {
				return true;
			}
		return false;
	}
	function stop_process($pid){	
		$result = shell_exec('taskkill /PID '.$pid );
		if (count(preg_split("/\n/", $result)) > 0 && !preg_match('/No tasks/', $result)) {
			return true;
			}
	}

	?>