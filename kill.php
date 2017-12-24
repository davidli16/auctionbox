<?php # kill.php - kills MySQL connections

# from http://theinformationbox.net/?p=17
?>

<center>
<h3> Kill MySQL Connections </h3> 

<?php
if($db ) echo "Connected<br />";
$result = $db->query( "SHOW PROCESSLIST", $con );
while($row = $result->fetch_assoc())
{

//Retrieve the process ID
$process_id = $row["Id"];

//Retrieve the process time (not really needed)
$proccess_time = $row["Time"];

//Query to run
$sql = "KILL $process_id";

print '<div class="message">';
//Executing the query
$res = $db->query( "$sql", $con );
if( $res )
{
echo "Mysql Process ID $process_id has been killed<br />";
}
print '</div>';

/*
else
{
$proc_id = $row["Id"];
$proc_time = $row["Time"];
echo “Error: Did not kill ID $proc_id with sleep time $proc_time”;
}
*/
}

?>

</center>