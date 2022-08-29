<?php

function getLocalesAvailable() {
	return array_map(function($langPath) {
		return basename($langPath);
	}, glob(APPPATH . '/Language/*', GLOB_ONLYDIR));
}