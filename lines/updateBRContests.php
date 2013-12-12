<?php
//error_reporting(E_ALL);
//ini_set('display_errors',1);

//define('_VALID_MOS', '1');//for accessing br_func.php

//file for db connection, also includes other required files
require_once("br_func.php");

br_db_connect();//create connection with database

updateBRContests();//update all BR Contest scripts

br_db_close();//close database connection
?>