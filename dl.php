<?php
// This file downloads a project using the rpgmv-spider script to find files.

if (empty($argv[1]) || empty($argv[2])) {
	exit("Usage: dl.php <url> <output_dir> [-v]");
}

$verbose = !empty($argv[3]);

// Run the spider
ob_start();
require("rpgmv-spider.php");
ob_end_clean();

require_once("decrypt.php");

$dir = rtrim($argv[2], '\\/') . '/';
@mkdir($dir, 0774, true);

// Download the files
$files = array_unique($urls);

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

foreach($files as $path) {
	if (!is_dir($dir . dirname($path))) {
		mkdir($dir . dirname($path), 0774, true);
	}
	if (substr($path, -7, 6) == '.rpgmv') {
		$data = @file_get_contents($root . $path);
		if ($data !== false) {
			try {
				$newpath = str_replace($oldExt, $newExt, $path);
				file_put_contents($dir . $newpath, $d->decrypt($data));
			} catch (Exception $e) {
				echo "Error decrypting file {$path}: ", $e->getMessage(), "\n";
			}
		} else {
			if ($verbose) {
				echo "Error downloading encrypted file {$path}\n";
			}
		}
	} else {
		if (substr($root, 0, 4) == 'http') {
			try {
				download($root . $path, $dir . $path);
			} catch(Exception $e) {
				if ($verbose) {
					echo "Error downloading file {$path}";
					echo $e->getMessage(), "\n";
				}
			}
		} else {
			if (is_file($root . $path)) {
				copy($root . $path, $dir . $path);
			}
		}
	}
}

/**
 * Download a file using curl
 * @param  string $url
 * @param  string $dest [description]
 * @return bool
 * @throws Exception
 */
function download(string $url, string $dest)
{
	$options = [
		CURLOPT_FILE => is_resource($dest) ? $dest : fopen($dest, 'w'),
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_URL => $url,
		CURLOPT_FAILONERROR => true,
    ];

    $ch = curl_init();
    curl_setopt_array($ch, $options);
    $return = curl_exec($ch);

    if ($return === false) {
    	throw new Exception($curl_error($ch));
    }

	return true;
}
