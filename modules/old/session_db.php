<?php 
/*
Date: 2009-06-19
Author: Han Lin Yap
Website: www.zencodez.net
File Description: Session handler - Save Session to database.

Independent: Database connection
*/

require("db_class.php");
  $db = new zcDatabase($c['db']);
// The global variable that holds the table name
$session_table = "PHPSESSION";

// Returns current time as a number.
// Used for recording the last session access.   

function getMicroTime( )
{
  // microtime( ) returns the number of seconds
  // since 0:00:00 January 1, 1970 GMT as a
  // microsecond part and a second part.
  // e.g.: 0.08344800 1000952237
  // Convert the two parts into an array
  $mtime = explode(" ", microtime( ));

  // Return the addition of the two parts 
  // e.g.: 1000952237.08344800
  return($mtime[1] + $mtime[0]);
}  

// The session open handler called by PHP whenever
// a session is initialized. Always returns true.

function sessionOpen()
{
  return true;
}

// This function is called whenever a session_start( )
// call is made and reads the session variables
// Returns "" when a session is not found
//         (serialized)string - session exists

function sessionRead($sess_id)
{
  // Access the DBMS connection
  global $db;
   
  // Access the global variable that holds the name
  // of the table that holds the session variables
  global $session_table;

  // Formulate a query to find the session
  // identified by $sess_id
  $search_query =
    "SELECT * FROM $session_table
      WHERE session_id = '$sess_id'";

  // Execute the query
  if (!($data = $db->query($search_query))) {}

  if($db->getRows() == 0)
    // No session found - return an empty string
    return "";
  else
  {
    // Found a session - return the serialized string
    return $data[0]["session_variable"];
  }
}

// This function is called when a session is initialized
// with a session_start( ) call, when variables are
// registered or unregistered, and when session variables
// are modified. Returns true on success.

function sessionWrite($sess_id, $val)
{
  global $db;
  global $session_table;

  $time_stamp = getMicroTime( );

  $search_query =
    "SELECT session_id FROM $session_table
       WHERE session_id = '$sess_id'";
    global $c;
  $db = new zcDatabase($c['db']);
  // Execute the query
  $db->query($search_query);

  if($db->getRows() == 0)
  {
     // No session found, insert a new one
     $insert_query =
       "INSERT INTO $session_table
       (session_id, session_variable, last_accessed)
       VALUES ('$sess_id', '$val', $time_stamp)";

     if (!$db->executeQuery($insert_query)) {}
  }
  else
  {
     // Existing session found - Update the
     // session variables
     $update_query =
       "UPDATE $session_table
        SET session_variable = '$val',
            last_accessed = $time_stamp
        WHERE session_id = '$sess_id'";

     if (!$db->executeQuery($update_query)) {}
  }
  return true;
}

function sessionClose()
{
    return true;
}

// This is called whenever the session_destroy( ) 
// function call is made. Returns true if the session  
// has successfully been deleted.

function sessionDestroy($sess_id)
{
  global $db;
  global $session_table;

  $delete_query = 
    "DELETE FROM $session_table 
      WHERE session_id = '$sess_id'";

  if (!($result = $db->executeQuery($delete_query))) {}

  return true;
}

// This function is called on a session's start up with
// the probability specified in session.gc_probability.
// Performs garbage collection by removing all sessions
// that haven't been updated in the last $max_lifetime
// seconds as set in session.gc_maxlifetime.
// Returns true if the DELETE query succeeded.

function sessionGC($max_lifetime)
{
  global $db;
  global $session_table;

  $time_stamp = getMicroTime( );
 
  $delete_query =
    "DELETE FROM $session_table
      WHERE last_accessed < ($time_stamp - $max_lifetime)";

  if (!($result = @ $db->executeQuery($delete_query))) {}

  return true;
}

session_set_save_handler("sessionOpen", 
                         "sessionClose", 
                         "sessionRead", 
                         "sessionWrite", 
                         "sessionDestroy", 
                         "sessionGC");

session_start( );

  // If this is a new session, then the variable
  // $count is not registered
  if (!isset($_SESSION["count"])) 
  {
    $_SESSION["count"] = 0;
	$_SESSION["start"] = time( );
  } 
  else 
  {
    $_SESSION["count"]++;
  }

  $sessionId = session_id( );
session_regenerate_id(true);
?>
<!DOCTYPE HTML PUBLIC 
   "-//W3C//DTD HTML 4.0 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd" >
<html>
  <body>
    <p>This page points at a session 
        (<?=$sessionId ?>)
    <br>count = <?=$_SESSION["count"] ?>.
    <br>start = <?=$_SESSION["start"] ?>.
    <p>This session has lasted 
      <?php 
        $duration = time( ) - $_SESSION["start"]; 
        echo "$duration"; 
      ?> 
      seconds.
  </body>

</html>