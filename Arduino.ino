#include <WiFi.h>
#include <HTTPClient.h>
#include <Arduino_JSON.h>
#include <Adafruit_NeoPixel.h>

// 📶 WLAN-Zugang
const char* ssid     = "tinkergarden";
const char* pass     = "strenggeheim";

// 🌐 Server-URLs
const char* loadURL   = "https://giessmich.lucatimlauener.ch/php/load.php";
const char* statusURL = "https://giessmich.lucatimlauener.ch/php/status.php";

// ⚙️ Sensor-Pins
const int feuchtePin = 4; // Feuchtigkeitssensor AOUT
const int lichtPin   = 6; // Lichtsensor AOUT

// 🔴 LED-Ring
#define RINGPIN 2
#define NUMPIXELS 12
Adafruit_NeoPixel ring = Adafruit_NeoPixel(NUMPIXELS, RINGPIN, NEO_GRB + NEO_KHZ800);

// 🕒 Intervall
unsigned long lastTime = 0;
unsigned long timerDelay = 30000; // alle 30 Sekunden senden

void setup() {
  Serial.begin(115200);
  WiFi.begin(ssid, pass);

  Serial.print("🔌 WLAN verbindet sich");
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("\n✅ WLAN verbunden");
  Serial.print("IP-Adresse: ");
  Serial.println(WiFi.localIP());

  pinMode(lichtPin, INPUT);
  ring.begin();
  ring.setBrightness(50);
  ring.show(); // LEDs aus
}

void loop() {
  if ((millis() - lastTime) > timerDelay) {
    lastTime = millis();

    // 🌱 Feuchtigkeit messen
    int feuchtRaw = analogRead(feuchtePin);
    float feuchtigkeit = map(feuchtRaw, 2753, 1121, 0, 100);
    feuchtigkeit = constrain(feuchtigkeit, 0, 100);

    // 💡 Licht messen (in Lux)
    int lichtRaw = analogRead(lichtPin);
    float lux = map(lichtRaw, 3297, 241, 0, 1000);
    lux = constrain(lux, 0, 1000);

    Serial.printf("📊 Feuchtigkeit: %.1f %% | Licht: %.1f lx\n", feuchtigkeit, lux);

    // 📤 JSON erstellen
    JSONVar data;
    data["feuchtigkeit"] = feuchtigkeit;
    data["licht"] = lux;
    String jsonString = JSON.stringify(data);
    Serial.println("📦 JSON: " + jsonString);

    // 📡 an load.php senden
    if (WiFi.status() == WL_CONNECTED) {
      HTTPClient http;
      http.begin(loadURL);
      http.addHeader("Content-Type", "application/json");

      int httpResponseCode = http.POST(jsonString);
      if (httpResponseCode > 0) {
        Serial.printf("load.php: %d\n", httpResponseCode);
        Serial.println("Antwort: " + http.getString());
      } else {
        Serial.printf("Fehler bei load.php: %d\n", httpResponseCode);
      }
      http.end();

      // 🔄 status.php abrufen
      HTTPClient statusHttp;
      statusHttp.begin(statusURL);
      int statusCode = statusHttp.GET();

      if (statusCode == 200) {
        String response = statusHttp.getString();
        Serial.println("🧠 status.php Antwort: " + response);

        if (response.indexOf("\"ok\":false") > -1) {
          ringLichtAn(); 
        } else {
          ringLichtAus(); 
        }
      } else {
        Serial.printf("Fehler bei status.php: %d\n", statusCode);
      }
      statusHttp.end();
    } else {
      Serial.println("WLAN getrennt");
