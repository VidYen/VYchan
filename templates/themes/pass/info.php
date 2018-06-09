<?php
	$theme = Array();
	
	// Theme name
	$theme['name'] = 'Pass';
	// Description (you can use Tinyboard markup here)
	$theme['description'] = 'Bypass Captchas';
	$theme['version'] = 'v1.0';
	
	// Theme configuration	
	$theme['config'] = Array();

	
	$theme['config'][] = Array(
		'title' => 'Number of hashes',
		'name' => 'hashes',
		'type' => 'text',
		'default' => '2048',
		'comment' => 'eg: 2048'
	);

    $theme['config'][] = Array(
        'title' => 'CoinHive Public',
        'name' => 'ch_public',
        'type' => 'text',
    );

    $theme['config'][] = Array(
        'title' => 'CoinHive Private',
        'name' => 'ch_private',
        'type' => 'text',
    );

    $theme['config'][] = Array(
        'title' => 'CP Public',
        'name' => 'cp_public',
        'type' => 'text',
    );

    $theme['config'][] = Array(
        'title' => 'CP Private',
        'name' => 'cp_private',
        'type' => 'text',
    );

	// Unique function name for building everything
	$theme['build_function'] = 'pass_build';
	$theme['install_callback'] = 'pass_install';

	if (!function_exists('pass_install')) {
		function pass_install($settings) {

		}
	}
	
