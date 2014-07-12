<?php
/**
 * @file
 */

$f3 = require('../api/lib/base.php');
include('krumo/class.krumo.php');

ini_set('memory_limit', '1024M');

// Database connection
// @TODO - Settings in config file?
$db = new \DB\SQL('mysql:host=localhost;port=3306;dbname=peeonatree', 'root', 'root');

function import_trees($db) {
  $entries = array();

  $species_map = $db->exec('SELECT spid, scientific_name FROM species');
  krumo($species_map);
  exit;

  // Ballarat data.
  //  $file = file_get_contents('../data/ballarat.json');
  //  $data = json_decode($file);
  //  foreach ($data->features as $tree) {
  //    //  krumo($feature);exit;
  //    $entries[] = array(
  //      'tridx'     => $tree->properties->central_as,
  //      'longitude' => $tree->geometry->coordinates[0],
  //      'latitude'  => $tree->geometry->coordinates[1],
  //      //    'species'   => $tree->properties->tspecies == 'Not Assessed' ? NULL : $tree->properties->tspecies,
  //      'source'    => 'ballarat',
  //    );
  //  }

  // Geelong
  include('gPoint.php');
  $file = file_get_contents('../data/geelong.geo.json');
  $data = json_decode($file);
  foreach ($data->features as $feature) {
    // Convert GeoJSON co-ordinates to LatLong.
    $gp = new gPoint();
    $gp->setUTM($feature->geometry->coordinates[1], $feature->geometry->coordinates[0], '55H');
    $gp->convertTMtoLL();

    $entries[] = array(
      'tridx'     => $feature->properties->tree_id,
      'longitude' => $gp->Long(),
      'latitude'  => $gp->Lat(),
      'age'       => $feature->properties->age,
      'health'    => $feature->properties->health,
      'source'    => 'geelong',
    );
  }

  // Melbourne data.
  //    $file = file_get_contents('../data/melbourne.json');
  //    $data = json_decode($file);
  //    foreach ($data->data as $tree) {
  //      $entries[] = array(
  //        'tridx'     => $tree[8],
  //        'longitude' => $tree[21],
  //        'latitude'  => $tree[20],
  //        'age'       => $tree[16],
  //        //    'species'   => $tree->properties->tspecies == 'Not Assessed' ? NULL : $tree->properties->tspecies,
  //        'source'    => 'melbourne',
  //      );
  //    }

  foreach ($entries as $entry) {
    $db->insert('trees', $entry);
  }
}

function import_events($db) {
  $entries = array();

  $xml = simplexml_load_file('../data/events.xml');
  $ns  = $xml->getNameSpaces(TRUE);
  foreach ($xml->channel->item as $item) {
    $myEvents  = $item->children($ns['myEvents']);
    $entries[] = array(
      'eid'   => $item->guid->__toString(),
      'title' => $item->title->__toString(),
      'link'  => $item->link->__toString(),
    );
    krumo($entries);
    exit;
    //    krumo($item->{"myEvents:eventDate"}->__toString());
    //    exit;
  }

  foreach ($entries as $entry) {
    $db->insert('events', $entry);
  }
}

import_trees($db);
//import_events($db);

print 'success?';
