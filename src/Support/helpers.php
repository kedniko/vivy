<?php

function base_path($path = '')
{
	return __DIR__ . '/../../' . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, ltrim($path, '/'));
}
