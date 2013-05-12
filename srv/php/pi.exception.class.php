<?php
/**
 * π custom exception class
 * 
 * @author Johan Telstad, Kroma AS
 */


define('INPUTFILE_NOT_FOUND', 1);
define('NO_JOB',              2);
define('EMPTY_FILE',          3);
define('UNKNOWN_ERROR',       9);


class PiException extends Exception{}


?>