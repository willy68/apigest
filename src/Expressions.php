<?php
	namespace Library;
	
/**
 * @package ActiveRecord
 */
//namespace ActiveRecord;

/**
 * Templating like class for building SQL statements.
 *
 * Examples:
 * 'name = :name AND author = :author'
 * 'id = IN(:ids)'
 * 'id IN(:subselect)'
 * 
 * @package ActiveRecord
 */

class Expressions
{
	const ParameterMarker = '?';
	/**
	 * The quote character for stuff like column and field names.
	 * @var string
	 */
	private $QUOTE_CHARACTER = '`';

	private $expressions;
	private $values = array();
	private $connection;

	public function __construct($connection, $expressions=null /* [, $values ... ] */)
	{
		$values = null;
		$this->connection = $connection;
	
		if (is_array($expressions))
		{
			$glue = func_num_args() > 2 ? func_get_arg(2) : ' AND ';
			$op = func_num_args() > 3 ? func_get_arg(3) : '=';
			list($expressions,$values) = $this->build_sql_from_hash($expressions,$glue,$op);
		}
	
		if ($expressions != '')
		{
			if (!$values)
				$values = array_slice(func_get_args(),2);
	
			$this->values = $values;
			$this->expressions = $expressions;
		}	
	}

	/**
	* Bind a value to the specific one based index. There must be a bind marker
	* for each value bound or to_s() will throw an exception.
	*/
	public function bind($parameter_number, $value)
	{
		if ($parameter_number <= 0)
			throw new \Exception("Invalid parameter index: $parameter_number");

		$this->values[$parameter_number-1] = $value;
	}

	public function bind_values($values)
	{
		$this->values = $values;
	}

	/**
	* Returns all the values currently bound.
	*/
	public function values()
	{
		return $this->values;
	}

	/**
	* Returns the connection object.
	*/
	public function get_connection()
	{
		return $this->connection;
	}

	/**
	* Sets the connection object. It is highly recommended to set this so we can
	* use the adapter's native escaping mechanism.
	*
	* @param string $connection a Connection instance
	*/
	public function set_connection($connection)
	{
		$this->connection = $connection;
	}

	public function to_s($substitute=false, &$options=null)
	{
		if (!$options) $options = array();

		$values = array_key_exists('values',$options) ? $options['values'] : $this->values;

		$ret = "";
		$replace = array();
		$num_values = count($values);
		$len = strlen($this->expressions);
		$quotes = 0;

		for ($i=0,$n=strlen($this->expressions),$j=0; $i<$n; ++$i)
		{
			$ch = $this->expressions[$i];

			if ($ch == self::ParameterMarker)
			{
				if ($quotes % 2 == 0)
				{
					if ($j > $num_values-1)
						throw new Exception("No bound parameter for index $j");

					$ch = $this->substitute($values,$substitute,$i,$j++);
				}
			}
			elseif ($ch == '\'' && $i > 0 && $this->expressions[$i-1] != '\\')
				++$quotes;

			$ret .= $ch;
		}
		return $ret;
	}

	private function build_sql_from_hash(&$hash, $glue, $op)
	{
		$sql = $g = "";
		foreach ($hash as $name => $value)
		{
			if ($this->connection)
				$name = $this->quote_name($name);
		
			
			if (is_array($value))
				$sql .= "$g$name IN(?)";
			elseif (is_null($value))
				$sql .= "$g$name IS ?";
			else
				$sql .= "$g$name$op?";

			$g = $glue;
		}
		return array($sql,array_values($hash));
	}

	private function substitute(&$values, $substitute, $pos, $parameter_index)
	{
		$value = $values[$parameter_index];

		if (is_array($value))
		{
			if ($substitute)
			{
				$ret = '';

				for ($i=0,$n=count($value); $i<$n; ++$i)
					$ret .= ($i > 0 ? ',' : '') . $this->stringify_value($value[$i]);

				return $ret;
			}
			return join(',',array_fill(0,count($value),self::ParameterMarker));
		}

		if ($substitute)
			return $this->stringify_value($value);

		return $this->expressions[$pos];
	}

	private function stringify_value($value)
	{
		if (is_null($value))
			return "NULL";

		return is_string($value) ? $this->quote_string($value) : $value;
	}

	private function quote_string($value)
	{
		if ($this->connection)
			return $this->connection->quote($value);
	
		return "'" . str_replace("'","''",$value) . "'";
	}

	public function quote_name($string)
	{
		return $string[0] === $this->QUOTE_CHARACTER || $string[strlen($string) - 1] === $this->QUOTE_CHARACTER ?
			$string : $this->QUOTE_CHARACTER . $string . $this->QUOTE_CHARACTER;
	}
}
