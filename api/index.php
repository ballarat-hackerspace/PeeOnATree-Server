<?php


$f3 = require('lib/base.php');
//$f3 = include('lib/db/sql.php');

$db=new \DB\SQL('mysql:host=localhost;port=3306;dbname=peeonatree','root','passw0rd');

$f3->route('GET /',
    function() {
        echo 'Hello, world!';
        echo phpinfo();
    }
);

$f3->route('GET /tree/@count',
    function() {
	global $db, $f3;

        $rows=$db->exec('SELECT * FROM trees WHERE trid=?', $f3->get('PARAMS.count'));
	echo count($rows);
	foreach($rows as $row)
  		echo $row['trid'];

    }
);

//Retrieve a list of trees withing range of a lat/lon
//latlonran should be in the form of: latitude,longitude,range
$f3->route('GET /trees/bylocation/@latlonran',
    function() {
        global $db, $f3;
	$R=6371; //Radius of the earth in km

	list($lat, $lon, $rad) = explode(",",$f3->get('PARAMS.latlonran'));

	// first-cut bounding box (in degrees)
	$maxLat = $lat + rad2deg($rad/$R);
	$minLat = $lat - rad2deg($rad/$R);
	// compensate for degrees longitude getting smaller with increasing latitude
	$maxLon = $lon + rad2deg($rad/$R/cos(deg2rad($lat)));
	$minLon = $lon - rad2deg($rad/$R/cos(deg2rad($lat)));

	$sql = "Select *
        	From trees
        	Where Lat Between $minLat And $maxLat
          And Lon Between $minLon And $maxLon";
	echo $sql;

        $rows=$db->exec($sql);
        echo count($rows);
	echo json_encode($rows); 

    }
);

$f3->run();
