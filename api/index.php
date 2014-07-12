<?php


$f3 = require('lib/base.php');
//$f3 = include('lib/db/sql.php');

//$db=new \DB\SQL('mysql:host=54.79.38.93;port=3306;dbname=peeonatree','root','passw0rd');
$db=new \DB\SQL('mysql:host=localhost;port=3306;dbname=peeonatree','root','passw0rd');

function validateUser($email, $pwd)
{
    $rows=$db->exec('SELECT * FROM users WHERE email=?', $email);
    foreach($rows as $row)
    {
        if $row['password'] == $pwd
        {
            return $row['id'];
        }
        else
        {
            return 0;
        }
    }
    
}

function userLoginRedirect()
{
    echo "User not logged on";
}

$f3->route('GET /',
    function() {
        echo 'Hello, world!';
        echo phpinfo();
    }
);

$f3->route('GET /tree/@trid',
    function() {
	global $db, $f3;

        $rows=$db->exec('SELECT * FROM trees WHERE trid=?', $f3->get('PARAMS.trid'));
	    echo json_encode($rows);

    }
);

$f3->route('GET /tree/edit/@trid',
    function()
    {
        global $db, $f3;
        $name = $f3->get('POST.name');

        $rows=$db->exec('SELECT * FROM trees WHERE trid=?', $f3->get('PARAMS.trid'));
        echo json_encode($rows);

    }
);


$f3->route('GET /tree/history/@trid',
    function() {
	    global $db, $f3;

        $rows=$db->exec('SELECT * FROM checkins WHERE trid=?', $f3->get('PARAMS.trid'));
	    echo json_encode($rows);
    }
);

//Checkin to a tree ('mark')
$f3->route('GET /tree/@trid/mark',
    function() 
    {
        session_start();
        global $db, $f3;
        
        //Validate user is logged on
        if(!isset($_SESSION['email'])) { userLoginRedirect(); return; }
        $uid = validateuser($_SESSION['email'], $_SESSION['pwd']);
        if($uid == 0)
        {
            userLoginRedirect();
            return;
        }

        $trid = $f3->get('PARAMS.trid');
        $sql = "INSERT INTO marks(uid, trid)
                VALUES ($uid,$trid)";

        $rows=$db->exec($sql);
        echo json_encode($rows);

    }
);

//Retrieve a list of all trees
$f3->route('GET /trees',
    function() {
        global $db, $f3;

        $sql = "Select *
                From trees";

        $rows=$db->exec($sql);
        echo json_encode($rows);

    }
);

//Retrieve a list of trees withing range of a lat/lon
//latlonran should be in the form of: latitude,longitude,range (in km)
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
                Where latitude Between $minLat And $maxLat
                And longitude Between $minLon And $maxLon";

        $rows=$db->exec($sql);
        echo json_encode($rows);

    }
);

$f3->route('GET /user/logon/@email',
    function()
    {
        session_start();
        global $db, $f3;
        $_SESSION['email'] = $f3->get('POST.email');
        $_SESSION['pwd'] = $f3->get('POST.password');

    }
);


$f3->run();
