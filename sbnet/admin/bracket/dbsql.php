<?php
define('DEBUG',0);
abstract class Db_Actions
{
	protected $_cacheFile;
	protected $_cacheDir = '/usr/local/firstworks/var/sqlrelay/cache/';
	protected $_isCache = TRUE;
	protected $_csvFile = 0;
	
	
	function setCacheDirectory($cachedir){$this->_cacheDir = $cachedir; $this->_isCache = TRUE;}
	function setCacheFile($file) {$this->_cacheFile = $this->_cacheDir . $file;}
	function getCacheFile() {return $this->_cacheFile;}
	function setCSV($c) {$this->_csvFile = $c;}

	abstract public function closeConnection();
	abstract public function sendQuery($query, $isCache=true);

}
class Db_Sqlr_Basic extends Db_Actions 
{
	const HOST = 'localhost';
	const PORT = 9000;
	const SOCKET = '';
	const RETRYTIME = 0;
	const TRIES = 1;
	
	protected $_con;
	protected $_cur;
	protected $Cache_Lite;
	protected $options = array('cacheDir' => 'cache/', 'lifeTime'=> 1);
	
	function __construct($uname, $pass)
	{
		if (DEBUG)
		{
			$this->Cache_Lite = new Cache_Lite($this->options);
			return;
		}
		//@mysql_connect('localhost', $uname, $pass);
		//@mysql_select_db('sb_contests');
		//$this->Cache_Lite = new Cache_Lite($this->options);
	}
	public function closeConnection()
	{
		//sqlrcur_free($this->_cur);
		//sqlrcon_free($this->_con);
		@mysql_close();
	}
	public function getLastError() 
	{
		//return  sqlrcur_errorMessage($this->_cur);
		return mysql_error();
	}
	public function sendQuery($query, $isCache=false)
	{
		 // caching with sqlrelay doesn't work as sqlrelay is run all the time - better to serialize the results to a file
		/*if ($isCache != false)
		{
			if (($data = $this->Cache_Lite->get('CONTEST_BRACKET_'.$isCache))) 
			{
				return unserialize($data);
			}
		}*/
		//sqlrcur_sendQuery($this->_cur,$query);
		//sqlrcon_endSession($this->_con);
		return @mysql_query($query);
	}
	
}