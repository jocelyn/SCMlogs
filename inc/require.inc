<?php

// Makes available those super global arrays that are made available
// in versions of PHP after v4.1.0.
if (isset ($HTTP_SERVER_VARS))
{
$_SERVER = &$HTTP_SERVER_VARS;
}

if (isset ($HTTP_GET_VARS))
{
$_GET = &$HTTP_GET_VARS;
}

if (isset ($HTTP_POST_VARS))
{
$_POST = &$HTTP_POST_VARS;
}

if (isset ($HTTP_COOKIE_VARS))
{
$_COOKIE = &$HTTP_COOKIE_VARS;
}

if (isset ($HTTP_POST_FILES))
{
$_FILES = &$HTTP_POST_FILES;
}

if (isset ($HTTP_ENV_VARS))
{
$_ENV = &$HTTP_ENV_VARS;
}

if (isset ($HTTP_SESSION_VARS))
{
$_SESSION = &$HTTP_SESSION_VARS;
}

@$PHP_SELF = $_SERVER['PHP_SELF'];
?>
