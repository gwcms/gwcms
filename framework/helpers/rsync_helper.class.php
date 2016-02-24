<?php

class Rsync_Helper {

		/**
		 * Required params
		 *   destination example "root@uostas.net:/home/www/vr.lt/"
		 *   remote_ssh_port example "22"
		 *   source example /var/www/gw_cms
		 * Optional
		 *   dry_run
		 *   excludes example "Array('.svn','repository/*')"
		 */
		static function exec($params) {
				$rsync_params = "Ovrtgoz";

				$cmd = "rsync " .
						(isset($params['remote_ssh_port']) ? "-e 'ssh -p $params[remote_ssh_port]'" : '') .
						" -$rsync_params ";

				if (isset($params['dry_run']))
						$cmd.="--dry-run ";


				foreach ($params['excludes'] as $exclude)
						$cmd.="--exclude='" . $exclude . "' ";


				if (isset($params['pull']))
						$cmd.=$params['destination'] . " " . $params['source'];
				else
						$cmd.=$params['source'] . " " . $params['destination'];



				dump("RSYNC CMD: $cmd");

				$out = shell_exec($cmd);

				dump($out);

				if (preg_match("/sending incremental file list\n(.*?)\n\n/iUs", $out, $m)) {
						dump(print_r(explode("\n", $m[1]), true));
				}
		}

}
