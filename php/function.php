<?php
/**************************
*
* Author: DongMing Hu
* Date: Feb. 11, 2019
* Course: CPRG 210 PHP
* Description: include two generic functions, insert object into database table, insert array into database table.
*
**************************/

// ---- Generic Function: insert object into database (OOP) ----

function insertObjIntoDBTable(object $obj, $database, $tableName) {

  $fieldsArray = get_class($obj)::$fields;
  $fields = implode(',',$fieldsArray);  // Note: $fields include AgentId !
  $vPlaceholders = implode(',', array_fill(0,count($fieldsArray),'?'));  // get ?,?,?,?,?,?,?,?
  $fieldsType = get_class($obj)::$fieldsType;  // get 'ssiissss'
  $values = array_values(get_object_vars($obj));  // put $obj properties values into a num array, * only public properties

  $insertSQL = "INSERT INTO $tableName ($fields) VALUES ($vPlaceholders)";

  $stmt =  $database->stmt_init();

   if ($stmt->prepare($insertSQL)){
     $stmt->bind_param($fieldsType,...$values);
     $bool = $stmt->execute();
     $stmt->close();
   };

  $database->close();

  return $bool;
}

// ---- Query data from database ----

function queryDataArrayFromDatabase($sql,$database){
  $rowArray = array();
  if (!$result = $database->query($sql)){
      echo "<h1>Query has failed.</h1>";
  } else {
    while ($row = $result->fetch_assoc()){
      $rowArray[] = $row;
    }
  }
  return $rowArray;
}



// ---- Old Function: insert array into database (Procedural) ----

function insertArrayIntoDBTable(array $agentsArray, $database, $tableName){

  foreach ($agentsArray as $k => $v) {
    $fields[] = $k;
    $values[] = "'".$v."'";  // have to wrap value for insertion in single quote ''
  }
  $fields = implode(",", $fields);
  $values = implode(",", $values);

  $insertSQL = "INSERT INTO $tableName ($fields) VALUES ($values)";
  $result = mysqli_query($database, $insertSQL);
  mysqli_close($database);

  return $result;
}


// connect to database
function ConnectDB(){
  $link = new mysqli("localhost", "admin", "P@ssw0rd", "travelexperts");
  if ($link->connect_errno){
      print("There was an error connecting:". $link->connect_errno . " -- " . $link->connect_error);
      exit;
  }
  return $link;
}
// close the datebase
function CloseDB($link){
  mysqli_close($link);
}
////////////Liming////////////////
// function to get
function GetPackage() {
  include_once("classes.php");
  $dbh = ConnectDB();

  $sql = "SELECT * FROM packages";

  if (!$result = $dbh->query($sql)){
      echo "ERROR: the sql failed to execute. <br>";
      echo "SQL: $sql <br>";
      echo "Error #: ". $dbh->errono. "<br>";
      echo "Error msg: ". $dbh->error ." <br>";
  }

  if ($result === 0 ){
      echo "There were no results<br>";
  }
  // initializing array for all packages
  $packages = array();
  // looping through result for each package($pack)
  while ($pack = $result->fetch_assoc()){
      // Constructing a singe package object
      $package = new Package(
          $pack["PackageId"],
          $pack["PkgName"],
          $pack["PkgStartDate"],
          $pack["PkgEndDate"],
          $pack["PkgDesc"],
          $pack["PkgBasePrice"]);
      // adding the package object to array of package
      $packages[] = $package;
  } // end of While

  CloseDB($dbh);

  return $packages; // this is an array of package objects

}
////////////Liming////////////////
 ?>
