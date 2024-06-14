<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sensor Readings</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e8f5e9; /* Light Green */
            color: #4e342e; /* Dark Brown */
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        h1 {
            color: #1b5e20; /* Dark Green */
            margin-top: 20px;
        }
        .sensor-display {
            display: grid;
            grid-template-columns: 1fr 1fr;
            grid-template-rows: 1fr 1fr;
            gap: 10px;
            margin-bottom: 20px;
            width: 80%;
            max-width: 600px;
        }
        .sensor-box {
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5em;
            color: white;
            height: 100px;
            text-align: center;
            border-radius: 8px;
        }
        .gas-level { background-color: #8d6e63; } /* Brown */
        .air-quality { background-color: #a5d6a7; } /* Light Green */
        .distance { background-color: #fff176; } /* Yellow */
        .light-state { background-color: #558b2f; } /* Green */
        table {
            border-collapse: collapse;
            width: 80%;
            max-width: 800px;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #4e342e; /* Dark Brown */
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #8d6e63; /* Brown */
            color: white;
        }
        tbody tr:nth-child(even) {
            background-color: #f1f8e9; /* Very Light Green */
        }
        tbody tr:nth-child(odd) {
            background-color: #c8e6c9; /* Light Green */
        }
    </style>
</head>
<body>
    <h1>Environmental Readings</h1>
    <!-- Display latest sensor readings in a 2x2 grid -->
    <div class="sensor-display">
        <div class="sensor-box gas-level">Gas Level: <span id="gas-level">0</span></div>
        <div class="sensor-box air-quality">Air Quality: <span id="air-quality">N/A</span></div>
        <div class="sensor-box distance">Distance: <span id="distance">0</span> cm</div>
        <div class="sensor-box light-state">Light State: <span id="light-state">N/A</span></div>
    </div>
    <!-- Display the timestamp of the latest reading -->
    <h5>Update on: <span id="timestamp"></span></h5>
    <!-- Heading for the recent data table -->
    <h2>10 Recent Data</h2>
    <!-- Table to display recent sensor readings -->
    <table>
        <thead>
            <tr>
                <th>Gas Level</th>
                <th>Air Quality</th>
                <th>Distance (cm)</th>
                <th>Light State</th>
                <th>Timestamp</th>
            </tr>
        </thead>
        <tbody id="readings-table">
        </tbody>
    </table>

    <script>
        // Function to fetch the latest sensor readings from the server
        function fetchLatestReadings() {
            $.ajax({
                url: 'get_latest_readings.php', // URL to get the latest readings
                method: 'GET',
                success: function(data) {
                    var readings = JSON.parse(data); // Parse the JSON data received from the server
                    var tableBody = $('#readings-table');
                    tableBody.empty(); // Clear the current table contents

                    if (readings.length > 0) {
                        // Display the latest reading in the sensor display grid
                        var latestReading = readings[0];
                        $('#gas-level').text(latestReading.gas_level);
                        $('#air-quality').text(latestReading.air_quality);
                        $('#distance').text(latestReading.distance_cm);
                        $('#light-state').text(latestReading.light_state == 'Have Light' ? 'Light Present' : 'No Light');
                        $('#timestamp').text(latestReading.timestamp);

                        // Populate the table with the recent readings
                        readings.forEach(function(reading) {
                            var row = '<tr>' +
                                '<td>' + reading.gas_level + '</td>' +
                                '<td>' + reading.air_quality + '</td>' +
                                '<td>' + reading.distance_cm + '</td>' +
                                '<td>' + reading.light_state + '</td>' +
                                '<td>' + reading.timestamp + '</td>' +
                                '</tr>';
                            tableBody.append(row);
                        });
                    }
                }
            });
        }

        // Fetch the latest readings when the page is ready
        $(document).ready(function() {
            fetchLatestReadings();
            setInterval(fetchLatestReadings, 1000); // Fetch new readings every 1 second
        });
    </script>
</body>
</html>
