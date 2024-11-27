<?php

require './station_interface_config.php';

$connection = new mysqli($servername, $username, $password, $dbname);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

if(isset($_GET["station_id"]) && isset($_GET["api_key"])) {
    $station_id = $_GET["station_id"];
    $api_key = $_GET["api_key"];

    $sql_station_in_blocked_list_query = "SELECT COUNT(*) AS count FROM blocked_stations WHERE station_id = '$station_id' AND organization_id = (SELECT organization_id FROM station_api_keys WHERE api_key = '$api_key');";
    $sql_station_if_exists_query = "SELECT COUNT(*) AS count FROM stations WHERE station_id = '$station_id';";
    $sql_can_add_new_stations_query = "SELECT can_add_new_stations FROM station_api_keys WHERE api_key = '$api_key'";

    $sql_station_in_blocked_list_result = $connection->query($sql_station_in_blocked_list_query);
    $sql_station_if_exists_result = $connection->query($sql_station_if_exists_query);
    $sql_can_add_new_stations_result = ($connection->query($sql_can_add_new_stations_query))->fetch_assoc();

    if($sql_station_in_blocked_list_result->num_rows > 0 && $sql_station_if_exists_result->num_rows > 0){
        $sql_station_in_blocked_list_result_rows = $sql_station_in_blocked_list_result->fetch_assoc();
        $sql_station_if_exists_result_rows = $sql_station_if_exists_result->fetch_assoc();
        if($sql_station_in_blocked_list_result_rows['count'] == 0 && $sql_station_if_exists_result_rows['count'] == 0 && $sql_can_add_new_stations_result['can_add_new_stations'] == 1){
            $sql_register_station_query = "INSERT INTO stations(station_id, organization_id) VALUES ('$station_id', (SELECT organization_id FROM station_api_keys WHERE api_key = '$api_key'));";
            if ($connection->query($sql_register_station_query) === TRUE) {
                echo "New station registered successfully.";
          
             } else {
                die("Error: " . $sql . " => " . $connection->error);
             }
        }
        elseif($sql_station_if_exists_result_rows['count'] > 0){
            die("Error: Cannot register a station: Station already exists.");
        }
        elseif($sql_station_in_blocked_list_result_rows['count'] > 0){
            die("Error: Cannot register a station: Station is in blocked list.");
        }
        elseif($sql_can_add_new_stations_result['can_add_new_stations'] == 0){
            die("Error: Cannot register a station: Permission denied.");
        }
    }
    else{
        die("Query error.");
    }
}
else{
    die("Values are not set in the HTTP request.");
}


?>