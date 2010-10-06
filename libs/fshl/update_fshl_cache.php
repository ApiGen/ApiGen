<?php
	// ----------------------------------------------------------------------
	// fshlGenerator class example
	// ----------------------------------------------------------------------
	//
	//                     !!!! SECURITY WARNING !!!
	//
	//          Don't use this dangerous script in your live webs!
	//
	// ----------------------------------------------------------------------
	// NOTE for developers
	//
	// This script automatically detects changes between source and
	// generated lexers. When you make changes in your source grammar script
	// you must simple run this one. Your new grammars must be added to
	// global array (look for TODO..)
	// ----------------------------------------------------------------------

	error_reporting(E_ALL);
?>
<html>
<head>
	<title>FSHL gen</title>
</head>
<body>
<p>
	Click to: <a href="update_fshl_cache.php">default</a>,
	rebuild: <a href="?all">release</a>,
	<a href="?stat">statistic</a>
</p>
<hr/>
<p>
<?php
	if(isset($_GET['all'])) {
		echo "Last build: release";
		echo '<hr/>';
	} else if(isset($_GET['stat'])) {
		echo "Last build: statistic<br/>";
		echo "WARNING: don't use this build in live webs";
		echo '<hr/>';
	}
?>
</p>
<pre>
<?php

	include ('fshl-generator.php');

	$languages = array(

				'PHP',
				'PHPCB',
				'HTML',
				'HTMLCB',
				'HTMLonly',
				'CSS',
				'JAVA',
				'JS',
				'JSCB',
				'CPP',
				'SQL',
				'PY',
				'SAFE',
				'TEXY',
				//TODO: add your new languages here
				);
	$errors=0;
	$touch=false;
	$options=!isset($_GET['stat']) ? P_DEFAULT : P_STATISTIC;
	foreach($languages as $lang)
	{
		$lang_file = $lang.'_lang.php';
		$update_file = true;
		if(file_exists(FSHL_CACHE.$lang_file)) {
			// update cache, when source language is newer than cached, or generator was changed
			$update_file = 	(filemtime(FSHL_LANG.$lang_file) > filemtime(FSHL_CACHE.$lang_file)) ||
							(filemtime(FSHL_CACHE.$lang_file) < filemtime(FSHL_PATH.'fshl-generator.php')) ||
							isset($_GET['stat']) || isset($_GET['all']);
		}
		if($update_file) {
				echo "- Updating <b>$lang</b> language -\n";
				$fgen = new fshlGenerator($lang,$options);
				$fgen->write();
				$errors += $fgen->is_error();
				$touch = true;
			} else {
				echo "-          <b>$lang</b> without changes -\n";
			}
	}
	if($errors) {
		echo "\nThere are <b>$errors</b> error(s).\n";
	} else {
		echo "\nUpdate OK.\n";
	}
	if($touch) {
		touch(FSHL_CACHE.'.fshl_cache_touch');
	}
?>
</pre>
</body>
</html>