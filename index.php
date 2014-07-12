<?php
/**
 * @file
 * Core functions for PeeOnA
 */

include('backpress/includes/class.bpdb.php');
include('backpress/includes/functions.core.php');

// Database connection
// @TODO - Settings in config file?
$db = new BPDB(array(
  'name'     => 'peeonatree',
  'user'     => 'root',
  'password' => 'root',
));
