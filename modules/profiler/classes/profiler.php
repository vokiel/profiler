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
		
			'file' => FALSE,
			'line' => FALSE,
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
			'other'=>$other
		);
	}

	/**
	 * Stops a benchmark.
	 *
	 *     Profiler::stop($token);
	 *
	 * @param   string  token
	 * @return  void
	 */
	public static function stop($token,$arr=false)
	{
		// Stop the benchmark
		Profiler::$_marks[$token]['stop_time']   = microtime(TRUE);
		Profiler::$_marks[$token]['stop_memory'] = memory_get_usage();
		if (Arr::is_array($arr)){
			Profiler::$_marks[$token]['file'] = $arr['file'];
			Profiler::$_marks[$token]['line'] = $arr['line'];
		}
	}

	/**
	 * Gets the total execution time and memory usage of a benchmark as a list.
	 *
	 *     list($time, $memory) = Profiler::total($token);
	 *
	 * @param   string  token
	 * @return  array   execution time, memory
	 */
	public static function total($token)
	{
		// Import the benchmark data
		$mark = Profiler::$_marks[$token];

		if ($mark['stop_time'] === FALSE)
		{
			// The benchmark has not been stopped yet
			$mark['stop_time']   = microtime(TRUE);
			$mark['stop_memory'] = memory_get_usage();
		}

		return array
		(
			// Total time in seconds
			$mark['stop_time'] - $mark['start_time'],
			// Amount of memory in bytes
			$mark['stop_memory'] - $mark['start_memory'],
			
			$mark['file'],
			$mark['line']
		);
	}
}
