<?php defined('SYSPATH') or die('No direct script access.');
/**
 * MySQL database connection.
 *
 * @package    Database
 * @author     Kohana Team
 * @copyright  (c) 2008-2009 Kohana Team
 * @license    http://kohanaphp.com/license
 */
class Kohana_Database_MySQL extends Database {

	public function query($type, $sql, $as_object)
	{
		// Make sure the database is connected
		$this->_connection or $this->connect();

		if ( ! empty($this->_config['profiling']))
		{
			// Benchmark this query for the current instance
			$benchmark = Profiler::start("Database ({$this->_instance})", $sql);
		}

		if ( ! empty($this->_config['connection']['persistent']) AND $this->_config['connection']['database'] !== Database_MySQL::$_current_databases[$this->_connection_id])
		{
			// Select database on persistent connections
			$this->_select_db($this->_config['connection']['database']);
		}

		// Execute the query
		if (($result = mysql_query($sql, $this->_connection)) === FALSE)
		{
			if (isset($benchmark))
			{
				// This benchmark is worthless
				Profiler::delete($benchmark);
			}

			throw new Database_Exception(':error [ :query ]',
				array(':error' => mysql_error($this->_connection), ':query' => $sql),
				mysql_errno($this->_connection));
		}

		if (isset($benchmark))
		{
			$debug = debug_backtrace();
			$arr = array (
				'file' => $debug[count($debug)-6]['line'],
				'line' => $debug[count($debug)-6]['file'],
			);			
			Profiler::stop($benchmark,$arr);
			Profiler::add($benchmark, 'R: '.mysql_affected_rows($this->_connection));
		}

		// Set the last query
		$this->last_query = $sql;

		if ($type === Database::SELECT)
		{
			// Return an iterator of results
			return new Database_MySQL_Result($result, $sql, $as_object);
		}
		elseif ($type === Database::INSERT)
		{
			// Return a list of insert id and rows created
			return array(
				mysql_insert_id($this->_connection),
				mysql_affected_rows($this->_connection),
			);
		}
		else
		{
			// Return the number of rows affected
			return mysql_affected_rows($this->_connection);
		}
	}

} // End Database_MySQL
