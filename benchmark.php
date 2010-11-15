<?php
/**
 * Benchmark for the DokuWiki indexer. It uses the pages directory and creates 
 * an index in the index directory. You probably need to adjust the path to 
 * DokuWiki below. You can get a dump of the pages of dokuwiki.org at 
 * http://dev.splitbrain.org/download/stuff/wiki.tgz
 * There is a limit variable below you probably want to adjust in order to get 
 * the runtime you want.
 * License: GPL2 (http://www.gnu.org/licenses/gpl.html)
 */

define('DOKU_INC', realpath(dirname(__FILE__).'/../../dokuwiki-git/').'/');
require_once DOKU_INC.'inc/init.php';
$conf['savedir'] = realpath(dirname(__FILE__));
$conf['datadir'] = $conf['savedir'].'/pages';
$conf['indexdir'] = $conf['savedir'].'/index';
$start_time = microtime(true);
$limit = 10; // number of pages/namespaces in each level to index, set to -1 to disable limit

function scanpages($dir) {
  global $limit;
  $dh = opendir($dir);
  if (!$dh) {
    echo 'error';
    return;
  }

  $count = $limit;
  while (($file = readdir($dh)) !== false && --$count !== -1) {
    if ($file == '.' || $file == '..' ) continue;
    if (is_dir($dir.'/'.$file)) {
      scanpages($dir.'/'.$file);
      continue;
    }
    if (substr($file,-4) == '.txt') {
      $name = substr($file,0,-4);
      $id = str_replace('/', ':', str_replace(realpath(dirname(__FILE__).'/pages/').'/', '', $dir)).':'.str_replace('.txt', '', $file);
      echo $id, "\n";
      $before_index = microtime(true);
      idx_addPage($id);     
      $after_index = microtime(true);
      echo $after_index - $before_index, "\n";
    }
  }
  closedir($dh);
}
scanpages(dirname(__FILE__).'/pages/');
$end_time = microtime(true);
echo 'Total runtime: '.$end_time-$start_time, "\n";
echo 'Peak memory usage: '.memory_get_peak_usage();
