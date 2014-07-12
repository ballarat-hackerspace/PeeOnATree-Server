<?php
/**
 * @file
 */

include('krumo/class.krumo.php');

include('backpress/includes/class.bpdb.php');
include('backpress/includes/functions.core.php');

ini_set('memory_limit', '1024M');

// Database connection
// @TODO - Settings in config file?
$db = new BPDB(array(
  'name'     => 'peeonatree',
  'user'     => 'root',
  'password' => 'root',
));

function import_trees($db) {
  $entries = array();

  //$file = file_get_contents('../data/geelong.geo.json');
  //$data = json_decode($file);
  //foreach ($data->features as $feature) {
  //  $entries[] = array(
  //    'trid'  => $feature->id,
  //    'pos_x' => $feature->geometry->coordinates[0],
  //    'pos_y' => $feature->geometry->coordinates[1]
  //  );
  //}

  // Ballarat data.
//  $file = file_get_contents('../data/ballarat.json');
//  $data = json_decode($file);
//  foreach ($data->features as $tree) {
//    //  krumo($feature);exit;
//    $entries[] = array(
//      'tridx'      => $tree->properties->central_as,
//      'longitude' => $tree->geometry->coordinates[0],
//      'latitude'  => $tree->geometry->coordinates[1],
//      //    'species'   => $tree->properties->tspecies == 'Not Assessed' ? NULL : $tree->properties->tspecies,
//      'source'    => 'ballarat',
//    );
//  }

  // Melbourne data.
  $file = file_get_contents('../data/melbourne.json');
  $data = json_decode($file);
  foreach ($data->data as $tree) {
    $entries[] = array(
      'trid'      => $tree[8],
      'longitude' => $tree[21],
      'latitude'  => $tree[20],
      //    'species'   => $tree->properties->tspecies == 'Not Assessed' ? NULL : $tree->properties->tspecies,
      'source'    => 'melbourne',
    );
  }

  foreach ($entries as $entry) {
    $db->insert('trees', $entry);
  }
}

//function import_events($db) {
//  $xml = simplexml_load_file('../data/events.xml');
//  foreach ($xml->channel->item as $item) {
//    krumo($item->{"myEvents:eventDate"}->__toString());
//    exit;
//  }
//}

import_trees($db);
//import_events($db);

print 'success?';
