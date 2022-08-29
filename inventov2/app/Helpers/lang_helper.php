<?php

function langSlashes(string $line, array $args = [], string $locale = null) {
	return addslashes(lang($line, $args, $locale));
}