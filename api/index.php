<?php

$BASEURL = "http://54.79.38.93/";
$CLIENTURL = $BASEURL."PeeOnATree-Client/web/";
$APIURL = $BASEURL."PeeOnATree-Server/";

$f3 = require('lib/base.php');
//$f3 = include('lib/db/sql.php');

//$db=new \DB\SQL('mysql:host=54.79.38.93;port=3306;dbname=peeonatree','root','passw0rd');
$db=new \DB\SQL('mysql:host=localhost;port=3306;dbname=peeonatree','root','passw0rd');

function validateUser($email, $pwd)
{
    global $db;
    //$sql = 'SELECT * FROM users WHERE email=?', $email;
    //echo $sql;
    $rows=$db->exec('SELECT * FROM users WHERE email=?', $email);
    foreach($rows as $row)
    {
        if($row['password'] == $pwd)
        {
            return $row['id'];
        }
        else
        {
            return 0;
        }
    }
    return 0;
}

function userLoginRedirect()
{
    global $f3;
    
    $f3->error(403);
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

$f3->route('GET /tree/@trid/pic',
    function() 
    {
	    global $db, $f3;

        $rows=$db->exec('SELECT * FROM trees WHERE trid=?', $f3->get('PARAMS.trid'));
        foreach($rows as $row)
        {
            if($row['pic'] == "") {return;}
            switch(substr($row['pic'], -3))
            {
                case 'gif':
                    header('Content-Type: image/gif');
                    break;
                case 'jpg':
                    header('Content-Type: image/jpeg');
                    break;
                case 'png':
                    header('Content-Type: image/png');
                    break;
            }
            echo readfile('../media/' . $row['pic']);
        }

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


$f3->route('GET /tree/@trid/history',
    function() {
	    global $db, $f3;

        $rows=$db->exec('SELECT * FROM marks WHERE trid=?', $f3->get('PARAMS.trid'));
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

//Upload a new pic
$f3->route('POST /tree/@trid/uploadpic',
    function() 
    {
        $web = \Web::instance();
        session_start();
        global $db, $f3;
        
        $f3->set('UPLOADS','../media/');
        $overwrite = false; // set to true, to overwrite an existing file; Default: false
        $slug = true; // rename file to filesystem-friendly version
        
        //Validate user is logged on
        if(!isset($_SESSION['email'])) { userLoginRedirect(); return; }
        $uid = validateuser($_SESSION['email'], $_SESSION['pwd']);
        if($uid == 0)
        {
            userLoginRedirect();
            return;
        }

        $trid = $f3->get('PARAMS.trid');
        $files = $web->receive(function($file,$formFieldName)
                {
                    var_dump($file);
                    /* looks like:
                      array(5) {
                          ["name"] =>     string(19) "csshat_quittung.png"
                          ["type"] =>     string(9) "image/png"
                          ["tmp_name"] => string(14) "/tmp/php2YS85Q"
                          ["error"] =>    int(0)
                          ["size"] =>     int(172245)
                        }
                    */
                    echo $file['name'] already contains the slugged name now
                    echo $file['tmp_name'] already contains the slugged name now

                    // maybe you want to check the file size
                    if($file['size'] > (8 * 1024 * 1024)) // if bigger than 8 MB
                        return false; // this file is not valid, return false will skip moving it

                    // everything went fine, hurray!
                    return true; // allows the file to be moved from php tmp dir to your defined upload dir
                },
                $overwrite,
                $slug
        );
        var_dump($files);
        
        
        
        //$sql = "UPDATE trees 
        //        SET pic =  $files[0]
        //        WHERE trid=$trid";

        //$rows=$db->exec($sql);
        //echo $files;

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
        session_start();
        global $db, $f3;
        $R=6371; //Radius of the earth in km

        //Validate user is logged on
        if(!isset($_SESSION['email'])) { userLoginRedirect(); return; }
        $uid = validateuser($_SESSION['email'], $_SESSION['pwd']);

        list($lat, $lon, $rad) = explode(",",$f3->get('PARAMS.latlonran'));

        // first-cut bounding box (in degrees)
        $maxLat = $lat + rad2deg($rad/$R);
        $minLat = $lat - rad2deg($rad/$R);
        // compensate for degrees longitude getting smaller with increasing latitude
        $maxLon = $lon + rad2deg($rad/$R/cos(deg2rad($lat)));
        $minLon = $lon - rad2deg($rad/$R/cos(deg2rad($lat)));

        $sql = "Select Distinct t.trid, t.latitude, t.longitude, m.datetime
                From trees t
                Left Join marks m ON m.trid = t.trid AND ( (m.datetime = (SELECT max(datetime) FROM marks where uid = $uid AND trid = t.trid)) OR (m.datetime is null) )
                Where t.latitude Between $minLat And $maxLat
                And t.longitude Between $minLon And $maxLon";

        $rows=$db->exec($sql);
        echo json_encode($rows);

    }
);

$f3->route('POST /user/logon',
    function()
    {
        session_start();
        global $db, $f3;
        $_SESSION['email'] = $f3->get('POST.email');
        //echo $f3->get('POST.email');
        $_SESSION['pwd'] = $f3->get('POST.password');
        
        $uid = validateUser($_SESSION['email'], $_SESSION['pwd']);
        if($uid == 0)
        {
            echo json_encode(False);
            return;
        }
        else
        {
            echo json_encode(True);
        }

    }
);

$f3->route('GET /user/logoff',
    function()
    {
        global $f3;
        
        session_start();
        $_SESSION = array();
        session_destroy();
        
        $f3->error(200);
    }
);

$f3->route('GET /user/status',
    function()
    {
        session_start();
        global $db, $f3;
        
        //Validate user is logged on
        if(!isset($_SESSION['email'])) { userLoginRedirect(); return; }
        $uid = validateUser($_SESSION['email'], $_SESSION['pwd']);
        if($uid == 0)
        {
            return;
        }
        else
        {
            echo $_SESSION['email'];
        }
    }
);

$f3->route('GET /user/profile',
  function()
  {
    session_start();
    global $db, $f3;

    //Validate user is logged on
    if(!isset($_SESSION['email'])) { userLoginRedirect(); return; }
    $uid = validateUser($_SESSION['email'], $_SESSION['pwd']);
    if($uid == 0)
    {
      return;
    }
    else
    {
      $sql = "Select email, team
                From users
                Where id = $uid";
      $result = $db->exec($sql);
      $user = $result[0];
      $user['gravatar'] = md5($user['email']);

      $sql = "SELECT trid, datetime FROM marks WHERE uid = $uid";
      $results = $db->exec($sql);

      foreach ($results as $result) {
        $user['marks'][] = $result;
      }
      
      // Get statistics for display
      // Total marks by this user
      $user['user_marks'] = $db->exec("SELECT total FROM user_totals WHERE uid = $uid")[0]['total'];
      // Total marks by this user's team
      $user['team_marks'] = $db->exec("SELECT total FROM team_totals WHERE team = \"{$user['team']}\"")[0]['total'];

      echo json_encode($user);
    }
  }
);

$f3->run();
