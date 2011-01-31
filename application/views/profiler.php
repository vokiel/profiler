<?php defined('SYSPATH') or die('No direct script access.');

echo (Kohana::$environment==Kohana::DEVELOPMENT)? View::factory('profiler/stats') : '';