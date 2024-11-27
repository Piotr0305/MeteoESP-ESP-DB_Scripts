<?php

require './station_interface_config.php';

if(isset($_GET["station_id"]) && isset($_GET["api_key"])) {
   $station_id = $_GET["station_id"]; // Get station ID from HTTP GET
   $api_key = $_GET["api_key"]; // Get API key from HTTP GET
   if(isset($_GET["battery_voltage"])) {
      $battery_voltage = $_GET["battery_voltage"]; // Get battery_voltage value from HTTP GET
   }
   if(isset($_GET["battery_level"])) {
      $battery_level = $_GET["battery_level"]; // Get battery_level value from HTTP GET
   }
   if(isset($_GET["temperature"])) {
      $temperature = $_GET["temperature"]; // Get temperature value from HTTP GET
   }
   if(isset($_GET["humidity"])) {
      $humidity = $_GET["humidity"]; // Get humidity value from HTTP GET
   }
   if(isset($_GET["pressure"])) {
      $pressure = $_GET["pressure"]; // Get pressure value from HTTP GET
   }
   if(isset($_GET["wind_speed"])) {
      $wind_speed = $_GET["wind_speed"]; // Get wind_speed value from HTTP GET
   }
   if(isset($_GET["rain_level"])) {
      $rain_level = $_GET["rain_level"]; // Get rain_level value from HTTP GET
   }
   if(isset($_GET["pm100"])) {
      $pm100 = $_GET["pm100"]; // Get pm100 value from HTTP GET
   }
   if(isset($_GET["pm025"])) {
      $pm025 = $_GET["pm025"]; // Get pm025 value from HTTP GET
   }
   if(isset($_GET["pm010"])) {
      $pm010 = $_GET["pm010"]; // Get pm010 value from HTTP GET
   }

   // Create MySQL connection from PHP to MySQL server
   $connection = new mysqli($servername, $username, $password, $dbname);
   // Check connection
   if ($connection->connect_error) {
      die("Connection failed: " . $connection->connect_error);
   }

   $sql_query_first_part = "INSERT INTO actual_data (data_id, date_and_time, station_id";
   $sql_query_second_part = ") VALUES (NULL, NOW(), '$station_id'";

   if(isset($_GET["battery_voltage"])){
      $sql_query_first_part = $sql_query_first_part . ", battery_voltage";
      $sql_query_second_part = $sql_query_second_part . ", '$battery_voltage'";
   }
   if(isset($_GET["battery_level"])){
      $sql_query_first_part = $sql_query_first_part . ", battery_level";
      $sql_query_second_part = $sql_query_second_part . ", '$battery_level'";
   }
   if(isset($_GET["temperature"])){
      $sql_query_first_part = $sql_query_first_part . ", temperature";
      $sql_query_second_part = $sql_query_second_part . ", '$temperature'";
   }
   if(isset($_GET["humidity"])){
      $sql_query_first_part = $sql_query_first_part . ", humidity";
      $sql_query_second_part = $sql_query_second_part . ", '$humidity'";
   }
   if(isset($_GET["pressure"])){
      $sql_query_first_part = $sql_query_first_part . ", pressure";
      $sql_query_second_part = $sql_query_second_part . ", '$pressure'";
   }
   if(isset($_GET["wind_speed"])){
      $sql_query_first_part = $sql_query_first_part . ", wind_speed";
      $sql_query_second_part = $sql_query_second_part . ", '$wind_speed'";
   }
   if(isset($_GET["rain_level"])){
      $sql_query_first_part = $sql_query_first_part . ", rain_level";
      $sql_query_second_part = $sql_query_second_part . ", '$rain_level'";
   }
   if(isset($_GET["pm100"])){
      $sql_query_first_part = $sql_query_first_part . ", pm100";
      $sql_query_second_part = $sql_query_second_part . ", '$pm100'";
   }
   if(isset($_GET["pm025"])){
      $sql_query_first_part = $sql_query_first_part . ", pm025";
      $sql_query_second_part = $sql_query_second_part . ", '$pm025'";
   }
   if(isset($_GET["pm010"])){
      $sql_query_first_part = $sql_query_first_part . ", pm010";
      $sql_query_second_part = $sql_query_second_part . ", '$pm010'";
   }

   $whole_sql_query = $sql_query_first_part . $sql_query_second_part . ");";

   $sql_station_if_exists_query = "SELECT COUNT(*) AS count FROM stations WHERE station_id = '$station_id';";
   $sql_api_key_if_exists_query = "SELECT COUNT(*) AS count FROM station_api_keys WHERE api_key = '$api_key';";

   $sql_station_if_exists_result = $connection->query($sql_station_if_exists_query);
   $sql_api_key_if_exists_result = $connection->query($sql_api_key_if_exists_query);

   if($sql_api_key_if_exists_result->num_rows > 0 && $sql_station_if_exists_result->num_rows > 0){
      $sql_station_if_exists_result_rows = $sql_station_if_exists_result->fetch_assoc();
      $sql_api_key_if_exists_result_rows = $sql_api_key_if_exists_result->fetch_assoc();
      if($sql_station_if_exists_result_rows['count'] == 1 && $sql_api_key_if_exists_result_rows['count'] == 1){
        if ($connection->query($whole_sql_query) === TRUE) {
            echo "New record created successfully.";
      
         } else {
            die("Error: " . $sql . " => " . $connection->error);
         }
      }
      elseif($sql_station_if_exists_result_rows['count'] == 0){
        die("Error: Station doesn't exists.");
      }
      elseif($sql_api_key_if_exists_result_rows['count'] == 0){
        die("Error: API key doesn't exists.");
      }
   }
   else {
      die("Query error.");
   }

   $connection->close();
} else {
   die("Values are not set in the HTTP request.");
}
?>