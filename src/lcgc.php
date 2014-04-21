<?php

define('BUFFER_SIZE', 1024);

require_once(dirname(__FILE__).'/../lib/lcg_cipher.php');

function usage($cmd, $err = '') {
	if(strlen($err) > 0) {
		fprintf(STDERR, "Error: %s\n", $err);
	}
	fprintf(STDERR, "Usages : %s cipher|decipher <password> [input file [output file]]\n", $cmd);
	fprintf(STDERR, "         %s prng <password> <count>\n", $cmd);
	fprintf(STDERR, "         %s debug <password>\n", $cmd);
	exit(1);
}

function do_cipher_decipher($argc, $argv) {
	$password = $argv[2];
	$in_fd = false;
	$out_fd = false;
	
	if($argc >= 4) {
		if($argv[3] != '-') {
			$in_fd = fopen($argv[3], 'rb');
			if($in_fd === false)
				usage($argv[0], 'Cannot open '.$argv[3].' for reading!');
		}
	}
	if($argc == 5) {
		if($argv[4] != '-') {
			$out_fd = fopen($argv[4], 'wb');
			if($out_fd === false)
				usage($argv[0], 'Cannot open '.$argv[4].' for writing!');
		}
	}
	
	if($in_fd === false)
		$in_fd = STDIN;
	if($out_fd === false)
		$out_fd = STDOUT;
	
	$lc = new LCGCipher($password);
	
	if($mode == 'cipher') {
		$data = fread($in_fd, BUFFER_SIZE);
		while($data !== false) {
			$cdata = $lc->cipher($data);
			fwrite($out_fd, $cdata);
			if(feof($in_fd))
				break;
			$data = fread($in_fd, BUFFER_SIZE);
		}
	}
	else if($mode == 'decipher') {
		$data = fread($in_fd, BUFFER_SIZE);
		while($data !== false) {
			$ddata = $lc->decipher($data);
			fwrite($out_fd, $ddata);
			if(feof($in_fd))
				break;
			$data = fread($in_fd, BUFFER_SIZE);
		}
	}
	
	if($in_fd !== STDIN)
		fclose($in_fd);
	if($out_fd !== STDOUT)
		fclose($out_fd);
}

function do_prng($argc, $argv) {
	$password = $argv[2];
	$count = $argv[3];
	
	$lc = new LCGCipher($password);
	
	for($i = 0 ; $i < $count ; $i++)
		echo $lc->genRand()."\n";
}

function do_debug($argc, $argv) {
	$password = $argv[2];
	
	$lc = new LCGCipher($password);
	
	
	echo "a         x         c         m\n";
	
	for($i = 0, $l = count($lc->generators) ; $i < $l ; $i++) {
		echo str_pad($lc->generators[$i]->a, 9).' '.
		     str_pad($lc->generators[$i]->x, 9).' '.
		     str_pad($lc->generators[$i]->c, 9).' '.
		     str_pad($lc->generators[$i]->m, 9)."\n";
	}
}

if($argc < 2) {
	usage($argv[0]);
}

$mode = $argv[1];
if($mode == 'cipher' || $mode == 'decipher') {
	if($argc < 3 || $argc > 5)
		usage($argv[0]);
	
	do_cipher_decipher(argc, argv);
}
else if($mode == 'prng') {
	if($argc != 4)
		usage($argv[0]);
	
	do_prng($argc, $argv);
}
else if($mode == 'debug') {
	if($argc != 3)
		usage($argv[0]);
	
	do_debug($argc, $argv);
}
else {
	usage($argv[0]);
}
