<?php
// Database credentials
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sensor_data";

// Create connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get data from POST request
$gas_level = $_POST['gas_level'];
$air_quality = $_POST['air_quality'];
$distance_cm = $_POST['distance_cm'];
$light_state = $_POST['light_state'];
$api_key = $_POST['api_key'];

// Validate the API key
$valid_api_key = "q1w2e3r4t5y6u7i8o9";
if ($api_key !== $valid_api_key) {
    die("Invalid API key");
}

// SQL query to insert the sensor data into the 'readings' table
$sql = "INSERT INTO readings (gas_level, air_quality, distance_cm, light_state) VALUES ('$gas_level', '$air_quality', '$distance_cm', '$light_state')";

// Execute the query and check if the insertion was successful
if ($conn->query($sql) === TRUE) {
    echo "New record created successfully";
} else {
    // Display an error message if the insertion failed
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Close the database connection
$conn->close();
?>
