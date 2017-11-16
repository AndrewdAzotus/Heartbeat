$hostUpLongest = "";
$hostUpFor = 0;
$listOn = "";
$listLen = 0;
$mysqli = new mysqli($DBSrvr, '<username=root>', '<password>', 'information_schema');
if ($mysqli->connect_error) {
    die('Connect Error (' .
        $mysqli->connect_errno . ') ' .
        $mysqli->connect_error
       );
}
$mysqlj = new mysqli($DBSrvr, '<username=select only>', '<password>', 'Primes');
if ($mysqlj->connect_error) {
    die('Connect Error (' .
        $mysqlj->connect_errno . ') ' .
        $mysqlj->connect_error);
}
print("<p>Highest Primes Found since processing started:");
$sqltxt = "Select TABLE_NAME from Tables where TABLE_SCHEMA='Primes' order by TABLE_NAME;";
print("<table border='2' cellpadding='2' cellspacing='2'>");
print("<tr>");
print("<th>Host Name</th>");
print("<th>Largest Prime</th>");
print("<th>Max. Time between restarts</th>");
print("<th>Checked</th>");
print("<th>Time since last restart</th>");
print("</tr>");
$hostList = $mysqli->query($sqltxt);
while($hostData = $hostList->fetch_assoc()){
  print("<tr>");
  $hostName = $hostData["TABLE_NAME"];
  print("<td align='center'>".$hostName."</td>");
  $SQLText = "Select Factors from ".strtolower($hostName)." Where Idx=0";
  $primeInfo = $mysqlj->query($SQLText);
  $primeData = $primeInfo->fetch_assoc();
  $primeMax = $primeData["Factors"];
  print("<td align='center'>".$primeMax."</td>");
  print("<td align='center'>".xpndMins(floor($primeMax/2))."</td>");

  $SQLText = "Select Factors from ".strtolower($hostName)." Where Idx=2";
  $primeInfo = $mysqlj->query($SQLText);
  $primeData = $primeInfo->fetch_assoc();
  $whenUpdtd = $primeData["Factors"];
  $SQLText = "Select Factors from ".strtolower($hostName)." Where Idx=1";
  $primeInfo = $mysqlj->query($SQLText);
  $primeData = $primeInfo->fetch_assoc();
  $primeMax = $primeData["Factors"];
  if ($primeMax > $hostUpFor) {
    $hostUpFor = $primeMax;
    $hostUpLongest = $hostName;
  }
  $howLong = "Off-Line";
  if ($whenUpdtd != null) {
    $adjNow = date("Y-m-d H:i",time() - (12 * 60));
    $howLong = ($adjNow>$whenUpdtd ? "Off-Line":"On-Line");
  }
  if ($howLong == "On-Line") {
    if ($primeMax > $listLen) {
      $listLen = $primeMax;
      $listOn  = $hostName;
    }
    $howLong=xpndMins(floor($primeMax/2));
  }
  print("<td align='center'>".$primeMax."</td>");
  print("<td align='center'>".$howLong."</td>");

  print("</tr>");
}
print("</table>");
$mysqli->close();
$result   = $mysqlj->query("Select Factors from ".strtolower($hostUpLongest)." Where Idx=1");
$row      = $result->fetch_assoc();
$maxPrime = $row["Factors"];
print("<p>Highest prime found on ".$hostUpLongest." since it was first started was ".$maxPrime);
print(", so that means the longest time between reboots for ".$hostUpLongest." was ");
print(xpndMins(floor($maxPrime/2)));
print(". The list of primes generated are as follows:</p>");
print("<p>2");
$result = $mysqlj->query("Select Idx from ".strtolower($hostUpLongest)." Where Idx > 2 and Factors is Null Order By Idx");
while($row = $result->fetch_assoc()){
  print ", ".$row["Idx"];
}
?>
...</p>
<?php
$result = $mysqlj->query("Select Factors from ".strtolower($listOn)." Where Idx=1");
$answer = $result->fetch_assoc();
print("<p>It has been ");
print(xpndMins(floor($answer["Factors"]/2)));
$mysqlj->close();
print(" since ".$listOn);
?>
