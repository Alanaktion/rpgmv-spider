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

$index = $argv[1];
if (substr($index, -1, 1) == '/') {
	$root = $index;
	$index .= 'index.html';
} else {
	$root = dirname($index) . '/';
}

$urls = [$index];

// Add core data files
$dataFiles = [
	'Actors', 'Classes', 'Skills', 'Items', 'Weapons', 'Armors', 'Enemies',
	'Troops', 'States', 'Animations', 'Tilesets', 'CommonEvents', 'System',
	'MapInfos'
];
foreach($dataFiles as $d) {
	$urls[] = $root . 'data/' . $d . '.json';
}

// Add plugins
$plugins = [];
$pluginList = file_get_contents($root . 'js/plugins.js');
$pluginList = json_decode(rtrim(substr($pluginList, strpos($pluginList, '[')), "\n;"));
foreach($pluginList as $p) {
	$plugins[] = $p->name;
	$urls[] = $root . 'js/plugins/' . $p->name . '.js';
}

// Add system assets
$system = json_decode(file_get_contents($root . 'data/System.json'));
if (!empty($system->airship->bgm->name)) {
	$urls[] = $root . 'audio/bgm/' . $system->airship->bgm->name . '.m4a';
	$urls[] = $root . 'audio/bgm/' . $system->airship->bgm->name . '.ogg';
}
if (!empty($system->airship->characterName)) {
	$urls[] = $root . 'img/characters/' . $system->airship->characterName . '.png';
}
if (!empty($system->battleBgm->name)) {
	$urls[] = $root . 'audio/bgm/' . $system->battleBgm->name . '.m4a';
	$urls[] = $root . 'audio/bgm/' . $system->battleBgm->name . '.ogg';
}
if (!empty($system->battleback1Name)) {
	$urls[] = $root . 'img/battlebacks1/' . $system->battleback1Name . '.png';
}
if (!empty($system->battleback2Name)) {
	$urls[] = $root . 'img/battlebacks2/' . $system->battleback2Name . '.png';
}
if (!empty($system->battlerName)) {
	$urls[] = $root . 'img/enemies/' . $system->battlerName . '.png';
	$urls[] = $root . 'img/sv_enemies/' . $system->battlerName . '.png';
}
if (!empty($system->boat->bgm->name)) {
	$urls[] = $root . 'audio/bgm/' . $system->boat->bgm->name . '.m4a';
	$urls[] = $root . 'audio/bgm/' . $system->boat->bgm->name . '.ogg';
}
if (!empty($system->boat->characterName)) {
	$urls[] = $root . 'img/characters/' . $system->boat->characterName . '.png';
}
if (!empty($system->defeatMe->name)) {
	$urls[] = $root . 'audio/me/' . $system->defeatMe->name . '.m4a';
	$urls[] = $root . 'audio/me/' . $system->defeatMe->name . '.ogg';
}
if (!empty($system->gameoverMe->name)) {
	$urls[] = $root . 'audio/me/' . $system->gameoverMe->name . '.m4a';
	$urls[] = $root . 'audio/me/' . $system->gameoverMe->name . '.ogg';
}
if (!empty($system->ship->bgm->name)) {
	$urls[] = $root . 'audio/bgm/' . $system->ship->bgm->name . '.m4a';
	$urls[] = $root . 'audio/bgm/' . $system->ship->bgm->name . '.ogg';
}
if (!empty($system->ship->characterName)) {
	$urls[] = $root . 'img/characters/' . $system->ship->characterName . '.png';
}
foreach($system->sounds as $s) {
	if (!$s) {
		continue;
	}
	$urls[] = $root . 'audio/se/' . $s->name . '.m4a';
	$urls[] = $root . 'audio/se/' . $s->name . '.ogg';
}
if (!empty($system->titleBgm->name)) {
	$urls[] = $root . 'audio/bgm/' . $system->titleBgm->name . '.m4a';
	$urls[] = $root . 'audio/bgm/' . $system->titleBgm->name . '.ogg';
}
if (!empty($system->victoryMe->name)) {
	$urls[] = $root . 'audio/me/' . $system->victoryMe->name . '.m4a';
	$urls[] = $root . 'audio/me/' . $system->victoryMe->name . '.ogg';
}
if (!empty($system->title1Name)) {
	$urls[] = $root . 'img/titles1/' . $system->title1Name . '.png';
}
if (!empty($system->title2Name)) {
	$urls[] = $root . 'img/titles2/' . $system->title2Name . '.png';
}

// Add actors
$actorList = json_decode(file_get_contents($root . 'data/Actors.json'));
foreach($actorList as $a) {
	if (!$a) {
		continue;
	}
	if ($a->characterName) {
		$urls[] = $root . 'img/characters/' . $a->characterName . '.png';
	}
	if ($a->faceName) {
		$urls[] = $root . 'img/faces/' . $a->faceName . '.png';
	}
	if ($a->battlerName) {
		$urls[] = $root . 'img/sv_actors/' . $a->battlerName . '.png';
		$urls[] = $root . 'img/sv_enemies/' . $a->battlerName . '.png';
	}
}

// Add maps and their assets
$mapList = json_decode(file_get_contents($root . 'data/MapInfos.json'));
foreach($mapList as $m) {
	if (!$m) {
		continue;
	}
	$url = $root . 'data/Map' . str_pad($m->id, 3, '0', STR_PAD_LEFT) . '.json';
	$urls[] = $url;
	$map = json_decode(file_get_contents($url));

	// Add basic images
	if ($map->battleback1Name) {
	$urls[] = $root . 'img/battlebacks1/' . $map->battleback1Name . '.png';
	}
	if ($map->battleback2Name) {
		$urls[] = $root . 'img/battlebacks2/' . $map->battleback2Name . '.png';
	}
	if ($map->bgm && $map->bgm->name) {
		$urls[] = $root . 'audio/bgm/' . $map->bgm->name . '.m4a';
		$urls[] = $root . 'audio/bgm/' . $map->bgm->name . '.ogg';
	}
	if ($map->bgs && $map->bgs->name) {
		$urls[] = $root . 'audio/bgs/' . $map->bgs->name . '.m4a';
		$urls[] = $root . 'audio/bgs/' . $map->bgs->name . '.ogg';
	}
	if ($map->parallaxName) {
		$urls[] = $root . 'img/parallaxes/' . $map->parallaxName . '.png';
	}

	// Add images from events
	foreach($map->events as $e) {
		if (!$e) {
			continue;
		}
		foreach($e->pages as $page) {
			if ($page->image && $page->image->characterName) {
				$urls[] = $root . 'img/characters/' . $page->image->characterName . '.png';
			}
			foreach($page->list as $l) {
				if ($l->code == 231) { // Show Picture
					$urls[] = $root . 'img/pictures/' . $l->parameters[1] . '.png';
				}
				if ($l->code == 241) { // Play BGM
					$urls[] = $root . 'audio/bgm/' . $l->parameters[0]->name . '.m4a';
					$urls[] = $root . 'audio/bgm/' . $l->parameters[0]->name . '.ogg';
				}
				if ($l->code == 245) { // Play BGS
					$urls[] = $root . 'audio/bgs/' . $l->parameters[0]->name . '.m4a';
					$urls[] = $root . 'audio/bgs/' . $l->parameters[0]->name . '.ogg';
				}
				if ($l->code == 249) { // Play ME
					$urls[] = $root . 'audio/me/' . $l->parameters[0]->name . '.m4a';
					$urls[] = $root . 'audio/me/' . $l->parameters[0]->name . '.ogg';
				}
				if ($l->code == 250) { // Play SE
					$urls[] = $root . 'audio/se/' . $l->parameters[0]->name . '.m4a';
					$urls[] = $root . 'audio/se/' . $l->parameters[0]->name . '.ogg';
				}
				if ($l->code == 261) { // Play Movie
					$urls[] = $root . 'movies/' . $l->parameters[0] . '.mp4';
					$urls[] = $root . 'movies/' . $l->parameters[0] . '.webm';
				}
			}
		}
	}
}

// @todo: find other stuff!

// Output results
sort($urls);
foreach(array_unique($urls) as $u) {
	echo $u, "\n";
}
