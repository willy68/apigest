<?php
    function autoload($class)
    {
    	$file = '../'.str_replace('\\', DIRECTORY_SEPARATOR, $class).'.class.php';
    	//var_dump($file);
	if (file_exists($file))
	{
        	require $file;
		return;
	}
    }
    
    spl_autoload_register('autoload');

