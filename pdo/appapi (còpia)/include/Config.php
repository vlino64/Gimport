<?php
/**
 * Database configuration
 */
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'vlino');
define('DB_HOST', 'localhost');
define('DB_NAME', 'cooper_actual');

//referencia generado con MD5(uniqueid(<some_string>, true))
define('API_KEY','3d524a53c110e4c22463b10ed32cef9d');

/**
 * API Response HTTP CODE
 * Used as reference for API REST Response Header
 *
 */
/*
200 	OK
201 	Created
304 	Not Modified
400 	Bad Request
401 	Unauthorized
403 	Forbidden
404 	Not Found
422 	Unprocessable Entity
500 	Internal Server Error
*/

?>
