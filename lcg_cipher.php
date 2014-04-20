<?php

/*
Copyright (c) 2014, Maxime Ferrino
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are met:
1. Redistributions of source code must retain the above copyright
   notice, this list of conditions and the following disclaimer.
2. Redistributions in binary form must reproduce the above copyright
   notice, this list of conditions and the following disclaimer in the
   documentation and/or other materials provided with the distribution.
3. All advertising materials mentioning features or use of this software
   must display the following acknowledgement:
   This product includes software developed by the Maxime Ferrino.
4. Neither the name of the Maxime Ferrino nor the
   names of its contributors may be used to endorse or promote products
   derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY Maxime Ferrino ''AS IS'' AND ANY
EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL Maxime Ferrino BE LIABLE FOR ANY
DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

class LCG {
	function __construct($a, $x, $c, $m) {
		$this->a = $a;
		$this->x = $x;
		$this->c = $c;
		$this->m = $m;
	}
	
	function gen() {
		$this->x = ($this->a * $this->x + $this->c) % $this->m;
		return($this->x);
	}
}

class LCGCipher {
	function __construct($password) {
		$main_key = hash("sha512", $password);
		
		$this->generators = array();
		$this->gen_count = 6;
		
		for($i = 0 ; $i < 5 ; $i++) {
			$a = 1+hexdec(substr($main_key, 0, 6)); $main_key = substr($main_key, 6);
			$x = 0+hexdec(substr($main_key, 0, 6)); $main_key = substr($main_key, 6);
			$c = 0+hexdec(substr($main_key, 0, 6)); $main_key = substr($main_key, 6);
			$m = 1+hexdec(substr($main_key, 0, 6)); $main_key = substr($main_key, 6);
			$this->generators[] = new LCG($a, $x, $c, $m);
		}
		
		$a = 1+hexdec(substr($main_key, 0, 2)); $main_key = substr($main_key, 2);
		$x = 0+hexdec(substr($main_key, 0, 2)); $main_key = substr($main_key, 2);
		$c = 0+hexdec(substr($main_key, 0, 2)); $main_key = substr($main_key, 2);
		$m = 1+hexdec(substr($main_key, 0, 2)); $main_key = substr($main_key, 2);
		$this->generators[] = new LCG($a, $x, $c, $m);
		
		$this->start = true;
		
		$this->iv_len = $this->genRand() % 9 + 8;
		$this->iv = file_get_contents("/dev/urandom", false, null, -1, $this->iv_len);
		assert($this->iv !== false);
		
		$this->p1 = $this->genRandChar();
		$this->p2 = $this->genRandChar();
	}
	
	function genRand() {
		$val = 0;
		for($i = 0 ; $i < $this->gen_count ; $i++) {
			$val += $this->generators[$i]->gen();
		}
		return($val & 0xFF);
	}
	
	function genRandChar() {
		$val = 0;
		for($i = 0 ; $i < $this->gen_count ; $i++) {
			$val += $this->generators[$i]->gen();
		}
		return($val & 0xFF);
	}
	
	function cipher($in) {
		$out = '';
		
		if($this->start) {
			$in = $this->iv.$in;
			$this->start = false;
		}
		
		for($i = 0, $l = strlen($in) ; $i < $l ; $i++) {
			$c = ord($in[$i]);
			$r = $this->genRandChar();
			$e = $c ^ $this->p1 ^ $this->p2 ^ $r;
			$out .= chr($e);
			
			$this->p1 = $c;
			$this->p2 = ($c + $e - $r) & 0xFF;
		}
		
		return($out);
	}
	
	function decipher($in) {
		$out = '';
		
		for($i = 0, $l = strlen($in) ; $i < $l ; $i++) {
			$c = ord($in[$i]);
			$r = $this->genRandChar();
			$e = $c ^ $this->p1 ^ $this->p2 ^ $r;
			$out .= chr($e);
			
			$this->p2 = $e;
			$this->p1 = ($e + $c - $r) & 0xFF;
		}
		
		if($this->start) {
			$out = substr($out, $this->iv_len);
			$this->start = false;
		}
		
		return($out);
	}
}

function usage($cmd, $err = '') {
	if(strlen($err) > 0) {
		fprintf(STDERR, "Error: %s\n", $err);
	}
	fprintf(STDERR, "Usage : %s <cipher|decipher> <password> [input file] [output file]\n", $cmd);
	exit(1);
}

define('BUFFER_SIZE', 1024);

if($argc < 3 || $argc > 5) {
	usage($argv[0]);
}

$mode = $argv[1];
$password = $argv[2];
$in_fd = false;
$out_fd = false;
if($argc >= 4) {
	if($argv[3] != '-') {
		if(!file_exists($argv[3]))
			usage($argv[0], 'Cannot find the input file '.$argv[3]);
		
		$in_fd = fopen($argv[3], 'rb');
		if($in_fd === false)
			usage($argv[0], 'Cannot open '.$argv[3].' for reading!');
	}
}
if($argc == 5) {
	if($argv[4] != '-') {
		$out_fd = fopen($argv[4], 'cb');
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
		fwrite($out_fd, $cdata)."\n";
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
else {
	usage($argv[0]);
}

if($in_fd !== STDIN)
	fclose($in_fd);
if($out_fd !== STDOUT)
	fclose($out_fd);
