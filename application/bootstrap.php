<?php defined('SYSPATH') or die('No direct script access.');

//-- Configuration and initialization -----------------------------------------

/**
 * Initialize Kohana, setting the default options.
 *
 */
Kohana::init(array(
	'profile' => FALSE,
));

/**
 * Enable modules. Modules are referenced by a relative or absolute path.
 */
Kohana::modules(array(
	 'profiler'   => MODPATH.'profiler',  // Database access
	));

Kohana::$profiling = TRUE;