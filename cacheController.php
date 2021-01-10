<?php
	/**
	*   Это третий адекватный класс на всех проектах
	*	@category   Controllers
	*	@package    Cache
	*	@author     dev1lroot@protonmail.com
	*	@copyright  2021 Hadrian Bell (C)
	*	@license    GNU AGPL v3.0
	*	@version    2.0
	*/
	class cacheController
	{
		var $table;
		var $database;
		var $userController;
		function __construct($database,$timeout,$table)
		{
			$this->table = $table;
			$this->database = $database;
			$this->timeout = $timeout;
		}
		function getCache($name)
		{
			$ret = $this->database->query("
				SELECT
					`date`,`data`
				FROM
					`{$this->table}`
				WHERE
					`name`='{$name}'
				LIMIT
					1
			")->fetch_assoc();
			if($ret["data"]) $ret["data"] = base64_decode($ret["data"]);
			return $ret;
		}
		function putCache($name,$data)
		{
			$time = time();
			$data = base64_encode($data);
			if($this->existsCache($name))
			{
				$this->database->query("
					UPDATE 
						`{$this->table}`
					SET 
						`date` = {$time},
						`data` = '{$data}'
					WHERE
						`name` = '{$name}'
				");
			}
			else
			{
				$this->database->query("
					INSERT INTO
						`{$this->table}`
						(`id`,`name`,`date`,`data`)
					VALUES
						(NULL,'{$name}',{$time},'{$data}')
				");
			}
		}
		function existsCache($name)
		{
			if(count($this->getCache($name)) > 0)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		function actualCache($name)
		{	
			$cache = $this->getCache($name);
			if(count($cache) == 0) return false;
			if(intval($cache["date"]) + intval($this->timeout) > time())
			{
				return true;
			}
			else
			{
				return false;
			}
		}
	}
?>
