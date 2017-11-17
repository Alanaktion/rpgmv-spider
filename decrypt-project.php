<?php
// This file decrypts a local project

if (empty($argv[1])) {
	exit("Usage: decrypt-project.php <dir> [-v]");
}

$verbose = !empty($argv[2]);

require_once("decrypt.php");

$dir = rtrim($argv[1], '\\/') . '/';

$system = json_decode(file_get_contents("{$dir}data/System.json"));

// Initialize decryption
$d = new Decrypter($system->encryptionKey);
$oldExt = [
	'.rpgmvp',
	'.rpgmvm',
	'.rpgmvo',
];
$newExt = [
	'.png',
	'.m4a',
	'.ogg',
];

$files = array_merge(glob("{$dir}*/*.rpgmv*"), glob("{$dir}*/*/*.rpgmv*"));

foreach($files as $path) {
	if (substr($path, -7, 6) == '.rpgmv') {
		$data = @file_get_contents($path);
		if ($data !== false) {
			try {
				$newpath = str_replace($oldExt, $newExt, $path);
				file_put_contents($newpath, $d->decrypt($data));
				echo "Writing $newpath\n";
			} catch (Exception $e) {
				echo "Error decrypting file {$path}: ", $e->getMessage(), "\n";
			}
		} else {
			if ($verbose) {
				echo "Error loading encrypted file {$path}\n";
			}
		}
	}
}
