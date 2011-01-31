<?php defined('SYSPATH') or die('No direct script access.');

class Profiler extends Kohana_Profiler {

	public static $included = FALSE;
	public static $extensions = FALSE;
	public static $server = FALSE;
	
	public static function check()
	{
		echo "checked!";
	}
	
	public static function start($group, $name)
	{
		static $counter = 0;

		// Create a unique token based on the counter
		$token = 'kp/'.base_convert($counter++, 10, 32);

		Profiler::$_marks[$token] = array
		(
			'group' => strtolower($group),
			'name'  => (string) $name,

			// Start the benchmark
			'start_time'   => microtime(TRUE),
			'start_memory' => memory_get_usage(),

			// Set the stop keys without values
			'stop_time'    => FALSE,
			'stop_memory'  => FALSE,
			'other'  => FALSE,
		);

		return $token;
	}

	public static function add($token, $data = NULL)
	{
		// Stop the benchmark
		Profiler::$_marks[$token]['other']   = $data;
	}
	
	public static function stats(array $tokens)
	{
		// Min and max are unknown by default
		$min = $max = array(
			'time' => NULL,
			'memory' => NULL);

		// Total values are always integers
		$total = array(
			'time' => 0,
			'memory' => 0);

		foreach ($tokens as $token)
		{
			
			$other = Profiler::$_marks[$token]['other'];
			// Get the total time and memory for this benchmark
			list($time, $memory) = Profiler::total($token);

			if ($max['time'] === NULL OR $time > $max['time'])
			{
				// Set the maximum time
				$max['time'] = $time;
			}

			if ($min['time'] === NULL OR $time < $min['time'])
			{
				// Set the minimum time
				$min['time'] = $time;
			}

			// Increase the total time
			$total['time'] += $time;

			if ($max['memory'] === NULL OR $memory > $max['memory'])
			{
				// Set the maximum memory
				$max['memory'] = $memory;
			}

			if ($min['memory'] === NULL OR $memory < $min['memory'])
			{
				// Set the minimum memory
				$min['memory'] = $memory;
			}

			// Increase the total memory
			$total['memory'] += $memory;
		}

		// Determine the number of tokens
		$count = count($tokens);

		// Determine the averages
		$average = array(
			'time' => $total['time'] / $count,
			'memory' => $total['memory'] / $count);

		return array(
			'min' => $min,
			'max' => $max,
			'total' => $total,
			'average' => $average,
			'other'=>$other);
	}


}
