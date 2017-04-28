<?php
/**
 * RPG Maker MV Spider
 * Generates a list of all URLs in an RPG Maker MV web game
 *
 * Currently does not detect URLs loaded by plugins
 */

if (!$argv[1]) {
	exit ("Usage: rpgmv-spider <url>");
}

// Determine root directory
// @todo: Detect a missing trailing slash on a directory path
$index = $argv[1];
$urls = [];
if (substr($index, -1, 1) == '/') {
	$root = $index;
	$urls[] = 'index.html';
} else {
	$root = dirname($index) . '/';
	$urls[] = basename($index);
}

// Verify project exists at path
$systemRaw = @file_get_contents($root . 'data/System.json');
if ($systemRaw === false) {
	exit("No project data found.\n");
}

// Add core asset files
$urls[] = 'img/system/Balloon.png';
$urls[] = 'img/system/ButtonSet.png';
$urls[] = 'img/system/Damage.png';
$urls[] = 'img/system/GameOver.png';
$urls[] = 'img/system/IconSet.png';
$urls[] = 'img/system/Loading.png';
$urls[] = 'img/system/Shadow1.png';
$urls[] = 'img/system/Shadow2.png';
$urls[] = 'img/system/States.png';
$urls[] = 'img/system/Weapons1.png';
$urls[] = 'img/system/Weapons2.png';
$urls[] = 'img/system/Weapons3.png';
$urls[] = 'img/system/Window.png';
$urls[] = 'js/main.js';
$urls[] = 'js/plugins';
$urls[] = 'js/plugins.js';
$urls[] = 'js/rpg_core.js';
$urls[] = 'js/rpg_managers.js';
$urls[] = 'js/rpg_objects.js';
$urls[] = 'js/rpg_scenes.js';
$urls[] = 'js/rpg_sprites.js';
$urls[] = 'js/rpg_windows.js';
$urls[] = 'js/libs/fpsmeter.js';
$urls[] = 'js/libs/lz-string.js';
$urls[] = 'js/libs/pixi-picture.js';
$urls[] = 'js/libs/pixi-tilemap.js';
$urls[] = 'js/libs/pixi.js';

// Add core data files
$dataFiles = [
	'Actors', 'Classes', 'Skills', 'Items', 'Weapons', 'Armors', 'Enemies',
	'Troops', 'States', 'Animations', 'Tilesets', 'CommonEvents', 'System',
	'MapInfos'
];
foreach($dataFiles as $d) {
	$urls[] = 'data/' . $d . '.json';
}

// Add plugins
$plugins = [];
$pluginList = file_get_contents($root . 'js/plugins.js');
$pluginList = json_decode(rtrim(substr($pluginList, strpos($pluginList, '[')), "\n;"));
foreach($pluginList as $p) {
	$plugins[] = $p->name;
	$urls[] = 'js/plugins/' . $p->name . '.js';
}

// Check system settings
$system = json_decode($systemRaw);
$imgExt = empty($system->hasEncryptedImages) ? '.png' : '.rpgmvp';
$oggExt = empty($system->hasEncryptedAudio) ? '.m4a' : '.rpgmvm';
$m4aExt = empty($system->hasEncryptedAudio) ? '.ogg' : '.rpgmvo';

// Add system assets
if (!empty($system->airship->bgm->name)) {
	$urls[] = 'audio/bgm/' . $system->airship->bgm->name . $m4aExt;
	$urls[] = 'audio/bgm/' . $system->airship->bgm->name . $oggExt;
}
if (!empty($system->airship->characterName)) {
	$urls[] = 'img/characters/' . $system->airship->characterName . $imgExt;
}
if (!empty($system->battleBgm->name)) {
	$urls[] = 'audio/bgm/' . $system->battleBgm->name . $m4aExt;
	$urls[] = 'audio/bgm/' . $system->battleBgm->name . $oggExt;
}
if (!empty($system->battleback1Name)) {
	$urls[] = 'img/battlebacks1/' . $system->battleback1Name . $imgExt;
}
if (!empty($system->battleback2Name)) {
	$urls[] = 'img/battlebacks2/' . $system->battleback2Name . $imgExt;
}
if (!empty($system->battlerName)) {
	$urls[] = 'img/enemies/' . $system->battlerName . $imgExt;
	$urls[] = 'img/sv_enemies/' . $system->battlerName . $imgExt;
}
if (!empty($system->boat->bgm->name)) {
	$urls[] = 'audio/bgm/' . $system->boat->bgm->name . $m4aExt;
	$urls[] = 'audio/bgm/' . $system->boat->bgm->name . $oggExt;
}
if (!empty($system->boat->characterName)) {
	$urls[] = 'img/characters/' . $system->boat->characterName . $imgExt;
}
if (!empty($system->defeatMe->name)) {
	$urls[] = 'audio/me/' . $system->defeatMe->name . $m4aExt;
	$urls[] = 'audio/me/' . $system->defeatMe->name . $oggExt;
}
if (!empty($system->gameoverMe->name)) {
	$urls[] = 'audio/me/' . $system->gameoverMe->name . $m4aExt;
	$urls[] = 'audio/me/' . $system->gameoverMe->name . $oggExt;
}
if (!empty($system->ship->bgm->name)) {
	$urls[] = 'audio/bgm/' . $system->ship->bgm->name . $m4aExt;
	$urls[] = 'audio/bgm/' . $system->ship->bgm->name . $oggExt;
}
if (!empty($system->ship->characterName)) {
	$urls[] = 'img/characters/' . $system->ship->characterName . $imgExt;
}
foreach($system->sounds as $s) {
	if (!$s) {
		continue;
	}
	$urls[] = 'audio/se/' . $s->name . $m4aExt;
	$urls[] = 'audio/se/' . $s->name . $oggExt;
}
if (!empty($system->titleBgm->name)) {
	$urls[] = 'audio/bgm/' . $system->titleBgm->name . $m4aExt;
	$urls[] = 'audio/bgm/' . $system->titleBgm->name . $oggExt;
}
if (!empty($system->victoryMe->name)) {
	$urls[] = 'audio/me/' . $system->victoryMe->name . $m4aExt;
	$urls[] = 'audio/me/' . $system->victoryMe->name . $oggExt;
}
if (!empty($system->title1Name)) {
	$urls[] = 'img/titles1/' . $system->title1Name . $imgExt;
}
if (!empty($system->title2Name)) {
	$urls[] = 'img/titles2/' . $system->title2Name . $imgExt;
}

// Add actors
$actors = json_decode(file_get_contents($root . 'data/Actors.json'));
foreach($actors as $a) {
	if (!$a) {
		continue;
	}
	if ($a->characterName) {
		$urls[] = 'img/characters/' . $a->characterName . $imgExt;
	}
	if ($a->faceName) {
		$urls[] = 'img/faces/' . $a->faceName . $imgExt;
	}
	if ($a->battlerName) {
		$urls[] = 'img/sv_actors/' . $a->battlerName . $imgExt;
	}
}

// Add animations
$animations = json_decode(file_get_contents($root . 'data/Animations.json'));
foreach($animations as $a) {
	if (!$a) {
		continue;
	}
	$urls[] = 'img/animations/' . $a->animation1Name . $imgExt;
	if ($a->animation2Name) {
		$urls[] = 'img/animations/' . $a->animation2Name . $imgExt;
	}
	foreach ($a->timings as $t) {
		if (!empty($t->se->name)) {
			$urls[] = 'audio/se/' . $t->se->name . $m4aExt;
			$urls[] = 'audio/se/' . $t->se->name . $oggExt;
		}
	}
}

// Add common events
$commonevents = json_decode(file_get_contents($root . 'data/CommonEvents.json'));
foreach($commonevents as $e) {
	if (empty($e->pages)) {
		continue;
	}
	foreach($e->pages as $page) {
		if ($page->image && $page->image->characterName) {
			$urls[] = 'img/characters/' . $page->image->characterName . $imgExt;
		}
		foreach($page->list as $l) {
			if ($l->code == 101 && $l->parameters[0]) { // Text + Face
				$urls[] = 'img/faces/' . $l->parameters[0] . $imgExt;
			}
			if ($l->code == 231) { // Show Picture
				$urls[] = 'img/pictures/' . $l->parameters[1] . $imgExt;
			}
			if ($l->code == 241) { // Play BGM
				$urls[] = 'audio/bgm/' . $l->parameters[0]->name . $m4aExt;
				$urls[] = 'audio/bgm/' . $l->parameters[0]->name . $oggExt;
			}
			if ($l->code == 245) { // Play BGS
				$urls[] = 'audio/bgs/' . $l->parameters[0]->name . $m4aExt;
				$urls[] = 'audio/bgs/' . $l->parameters[0]->name . $oggExt;
			}
			if ($l->code == 249) { // Play ME
				$urls[] = 'audio/me/' . $l->parameters[0]->name . $m4aExt;
				$urls[] = 'audio/me/' . $l->parameters[0]->name . $oggExt;
			}
			if ($l->code == 250) { // Play SE
				$urls[] = 'audio/se/' . $l->parameters[0]->name . $m4aExt;
				$urls[] = 'audio/se/' . $l->parameters[0]->name . $oggExt;
			}
			if ($l->code == 261) { // Play Movie
				$urls[] = 'movies/' . $l->parameters[0] . '.m4v';
				$urls[] = 'movies/' . $l->parameters[0] . '.mp4';
				$urls[] = 'movies/' . $l->parameters[0] . '.webm';
			}
		}
	}
}

// Add enemies
$enemies = json_decode(file_get_contents($root . 'data/Actors.json'));
foreach($enemies as $e) {
	if (!$e) {
		continue;
	}
	if ($e->battlerName) {
		$urls[] = 'img/enemies/' . $e->battlerName . $imgExt;
		$urls[] = 'img/sv_enemies/' . $e->battlerName . $imgExt;
	}
}

// Add tilesets
$tilesets = json_decode(file_get_contents($root . 'data/Tilesets.json'));
foreach($tilesets as $t) {
	if (!$t) {
		continue;
	}
	foreach($t->tilesetNames as $n) {
		if ($n) {
			$urls[] = 'img/tilesets/' . $n . $imgExt;
			$urls[] = 'img/tilesets/' . $n . '.txt';
		}
	}
}

// Add maps and their assets
$maps = json_decode(file_get_contents($root . 'data/MapInfos.json'));
foreach($maps as $m) {
	if (!$m) {
		continue;
	}
	$path = 'data/Map' . str_pad($m->id, 3, '0', STR_PAD_LEFT) . '.json';
	$urls[] = $path;
	$map = json_decode(file_get_contents($root . $path));

	// Add basic images
	if ($map->battleback1Name) {
	$urls[] = 'img/battlebacks1/' . $map->battleback1Name . $imgExt;
	}
	if ($map->battleback2Name) {
		$urls[] = 'img/battlebacks2/' . $map->battleback2Name . $imgExt;
	}
	if ($map->bgm && $map->bgm->name) {
		$urls[] = 'audio/bgm/' . $map->bgm->name . $m4aExt;
		$urls[] = 'audio/bgm/' . $map->bgm->name . $oggExt;
	}
	if ($map->bgs && $map->bgs->name) {
		$urls[] = 'audio/bgs/' . $map->bgs->name . $m4aExt;
		$urls[] = 'audio/bgs/' . $map->bgs->name . $oggExt;
	}
	if ($map->parallaxName) {
		$urls[] = 'img/parallaxes/' . $map->parallaxName . $imgExt;
	}

	// Add images from events
	foreach($map->events as $e) {
		if (!$e) {
			continue;
		}
		foreach($e->pages as $page) {
			if ($page->image && $page->image->characterName) {
				$urls[] = 'img/characters/' . $page->image->characterName . $imgExt;
			}
			foreach($page->list as $l) {
				if ($l->code == 101 && $l->parameters[0]) { // Text + Face
					$urls[] = 'img/faces/' . $l->parameters[0] . $imgExt;
				}
				if ($l->code == 231) { // Show Picture
					$urls[] = 'img/pictures/' . $l->parameters[1] . $imgExt;
				}
				if ($l->code == 241) { // Play BGM
					$urls[] = 'audio/bgm/' . $l->parameters[0]->name . $m4aExt;
					$urls[] = 'audio/bgm/' . $l->parameters[0]->name . $oggExt;
				}
				if ($l->code == 245) { // Play BGS
					$urls[] = 'audio/bgs/' . $l->parameters[0]->name . $m4aExt;
					$urls[] = 'audio/bgs/' . $l->parameters[0]->name . $oggExt;
				}
				if ($l->code == 249) { // Play ME
					$urls[] = 'audio/me/' . $l->parameters[0]->name . $m4aExt;
					$urls[] = 'audio/me/' . $l->parameters[0]->name . $oggExt;
				}
				if ($l->code == 250) { // Play SE
					$urls[] = 'audio/se/' . $l->parameters[0]->name . $m4aExt;
					$urls[] = 'audio/se/' . $l->parameters[0]->name . $oggExt;
				}
				if ($l->code == 261) { // Play Movie
					$urls[] = 'movies/' . $l->parameters[0] . '.m4v';
					$urls[] = 'movies/' . $l->parameters[0] . '.mp4';
					$urls[] = 'movies/' . $l->parameters[0] . '.webm';
				}
			}
		}
	}
}

// @todo: find other stuff!

// Output results
sort($urls);
foreach(array_unique($urls) as $u) {
	echo $root, $u, "\n";
}
