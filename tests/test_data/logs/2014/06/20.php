<?php defined('SYSPATH') OR die('No direct script access.'); ?>

2014-06-20 14:20:47 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: testuri/test4 ~ SYSPATH/classes/Kohana/HTTP/Exception.php [ 17 ] in /var/www/domain.com/system/classes/Kohana/Request.php:979
2014-06-20 16:20:47 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: testuri/test5 ~ SYSPATH/classes/Kohana/HTTP/Exception.php [ 17 ] in /var/www/domain.com/system/classes/Kohana/Request.php:979
2014-06-20 16:20:47 --- DEBUG: #0 /var/www/domain.com/system/classes/Kohana/Request.php(979): test/debug1 Kohana_HTTP_Exception::factory(404, 'Unable to find ...', Array)
#1 /var/www/domain.com/index.php(118): Kohana_Request->execute()
#2 {main} in /var/www/domain.com/system/classes/Kohana/Request.php:979
2014-06-20 19:20:47 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: testuri/test6 ~ SYSPATH/classes/Kohana/HTTP/Exception.php [ 17 ] in /var/www/domain.com/system/classes/Kohana/Request.php:979