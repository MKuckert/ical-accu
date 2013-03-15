#!/usr/bin/php
<?php
$arg0=array_shift($argv);
array_unshift($argv, 'https://www.google.com/calendar/ical/CRAZYSHIT/basic.ics');
array_unshift($argv, $arg0);
$argc++;
require_once __DIR__.DIRECTORY_SEPARATOR.'ical-accu.php';
