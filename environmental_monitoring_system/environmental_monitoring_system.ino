/*********
  Name:Phiraphong A/L A Watt
  Matric.No:288584
  Midterm Assignment (ESP-based Environmental Monitoring System with Database Storage)
*********/
#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>

// WiFi credentials
const char* ssid = "V2027";
const char* password = "password";

// Server URL with correct IP and port
const char* serverUrl = "http://192.168.119.21:8080/sensor/insert_data.php";
const char* apiKey = "q1w2e3r4t5y6u7i8o9";

// Pin Definitions
#define MQ135_PIN A0    // MQ-135 gas sensor connected to analog pin A0
#define TRIG_PIN D5     // Ultrasonic sensor trigger pin connected to digital pin D5
#define ECHO_PIN D6     // Ultrasonic sensor echo pin connected to digital pin D6
#define LDR_PIN D7      // LDR sensor connected to digital pin D7

// Variables to store sensor readings
int gasLevel = 0;
String quality = "";
float duration_us, distance_cm;
int light_state = 0;

// WiFi client object
WiFiClient client;

void setup() {
  // Initialize serial communication at 115200 baud rate
  Serial.begin(115200);

  // Connect to WiFi
  WiFi.begin(ssid, password);
  Serial.print("Connecting to WiFi");
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println();
  Serial.println("Connected to WiFi");

  // Configure pins
  pinMode(MQ135_PIN, INPUT);
  pinMode(TRIG_PIN, OUTPUT);
  pinMode(ECHO_PIN, INPUT);
  pinMode(LDR_PIN, INPUT);

  Serial.println("Setup complete.");
}

// Function to read MQ-135 gas sensor
void readMQ135() {
  gasLevel = analogRead(MQ135_PIN); // Read analog value from MQ-135 sensor
  if (gasLevel < 400) {
    quality = "Very Good!";
  } else if (gasLevel >= 400 && gasLevel < 750) {
    quality = "Good for health";
  } else if (gasLevel >= 750 && gasLevel < 1200) {
    quality = "Take care";
  } else {
    quality = "Harmful to health";
  }
}

// Function to read Ultrasonic sensor
void readUltrasonic() {
  // Send a 10-microsecond pulse to the TRIG pin
  digitalWrite(TRIG_PIN, HIGH);
  delayMicroseconds(10);
  digitalWrite(TRIG_PIN, LOW);

  // Measure the pulse duration from the ECHO pin
  duration_us = pulseIn(ECHO_PIN, HIGH);

  // Calculate the distance in cm (speed of sound is 0.034 cm/us)
  distance_cm = 0.034 / 2 * duration_us;
}

// Function to read LDR sensor
void readLDR() {
  light_state = digitalRead(LDR_PIN); // Read digital value from LDR sensor
}

// Function to send data to the server
void sendData() {
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http; // Create HTTP client object
    http.begin(client, serverUrl); // Initialize HTTP client with server URL
    http.addHeader("Content-Type", "application/x-www-form-urlencoded"); // Add header

    // Create POST data string
    String postData = "gas_level=" + String(gasLevel) +
                      "&air_quality=" + quality +
                      "&distance_cm=" + String(distance_cm) +
                      "&light_state=" + (light_state == HIGH ? "No Light" : "Have Light") +
                      "&api_key=" + String(apiKey);

    // Send POST request
    Serial.println("Sending POST request...");
    int httpResponseCode = http.POST(postData);
    if (httpResponseCode > 0) {
      String response = http.getString(); // Get response
      Serial.println(postData); // Print POST data
      Serial.println("HTTP Response code: " + String(httpResponseCode)); // Print response code
      Serial.println("Response: " + response); // Print response
    } else {
      Serial.print("Error on sending POST: "); // Print error
      Serial.println(httpResponseCode);
      Serial.println(http.errorToString(httpResponseCode));
    }
    http.end(); // End HTTP client
  } else {
    Serial.println("WiFi not connected"); // Print WiFi not connected message
  }
}

void loop() {
  readMQ135(); // Read MQ-135 sensor
  readUltrasonic(); // Read Ultrasonic sensor
  readLDR(); // Read LDR sensor
  sendData(); // Send data to server
  delay(5000); // Wait for 5 seconds before next reading
}
