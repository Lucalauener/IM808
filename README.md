# 🌿 GießMich – IoT-Pflanzenüberwachung

---

## Überblick

**GießMich** ist ein smartes IoT-Projekt zur Überwachung der Bodenfeuchtigkeit von Zimmerpflanzen. Es wurde mit dem Ziel entwickelt, das Gießen zu automatisieren bzw. rechtzeitig daran zu erinnern – insbesondere in Wohngemeinschaften, im Büro oder bei längerer Abwesenheit.
Ein ESP32 liest analoge Sensoren aus und kommuniziert über WLAN mit einem Server. Das Projekt kombiniert Hardware, Webentwicklung, Datenbankanbindung und visuelles Feedback über einen LED-Ring.
Die Weboberfläche zeigt aktuelle Werte, Pflanzendetails und den Gießstatus. Die Hardware gibt ein optisches Signal, wenn gegossen werden muss.

---

## Modell (visuelles & funktionales Verhalten)

* **Normalzustand**: Der LED-Ring ist ausgeschaltet.
* **Zu trocken**: Der Ring leuchtet dauerhaft **rot**.
* **Zu nass**: Kein Ringlicht – aber Hinweis im Web.
* **Im Web**: Der Feuchtigkeitsstatus wird farblich codiert (grün / rot / violett), ebenso der Lichtstatus (blau / orange / grün).

---

## Inhalt

1. UX Dokumentation
2. Inspirationen
3. Designentscheidungen
4. Prozess und Vorgehensweise
5. Technische Dokumentation
6. Verbindungsschema
7. Kommunikationsprozess
8. Umsetzungsprozess
9. Known Bugs
10. Lernerfolg
11. Aufgabenaufteilung
12. Learning & Herausforderungen

---

## UX Dokumentation

### Inspirationen

Das Design der Weboberfläche orientiert sich an modernen App-Interfaces, die auf Übersichtlichkeit und schnelle Verständlichkeit ausgelegt sind. Klare Farben und große Schaltflächen ermöglichen eine intuitive Bedienung auch ohne Anleitung.

### Designentscheidungen

* **Farbbasierte Statusanzeige** statt langer Textbeschreibungen
* **Dropdown-Menü** zur Auswahl der Pflanze (statt Eingabefeld)
* **Mobile-freundliche Gestaltung** durch ein zentriertes Layout mit max. 420 px Breite
* **Autonomes System** – keine Cloud-Dienste oder Login nötig

### Beispiel Screenshots 
![Uploading Bildschirmfoto 2025-05-20 um 18.46.51.png…]()

* Dashboard mit Messwertanzeige
* Farbliche Statusanzeige (rot / grün / violett)
* Dropdown-Auswahl aktive Pflanze
* Screenshot Datenbank-Tabellenansicht

---

## Prozess und Vorgehensweise

**Ausgangspunkt:** Die Idee entstand aus dem realen Bedürfnis, bei Abwesenheit keine Pflanze zu vergessen – ideal z. B. für WGs oder Wochenaufenthalter.

**Schritte:**

1. Erste Tests mit dem Feuchtigkeitssensor
2. Umstieg auf analoges Mapping zur Kalibrierung
3. Entwicklung der Serverlogik mit PHP
4. Aufbau der Datenbankstruktur
5. Erstellen des Webinterfaces mit JavaScript
6. Einbindung des LED-Rings mit Statuslogik
7. Erstellung der Dokumentation & Screenshots

---

## Technische Dokumentation

### Komponenten

| Komponente          | Beschreibung                              |
| ------------------- | ----------------------------------------- |
| ESP32-C6            | WLAN-fähiger Mikrocontroller              |
| Feuchtigkeitssensor | Analoger Sensor, kalibriert               |
| Lichtsensor (LM393) | Analoger Sensor für Lichtintensität       |
| WS2812B LED-Ring    | Zeigt Gießstatus optisch an               |
| Webserver (PHP)     | Nimmt Messdaten an und gibt Status zurück |
| MySQL-Datenbank     | Speichert Messwerte & Pflanzendaten       |
| HTML/JS Frontend    | Zeigt Daten und Status im Browser an      |

---

### Verbindungsschema *(Platzhalter für Steckplan-Bild)*

* GPIO 4 → Feuchtigkeitssensor (analog)
* GPIO 6 → Lichtsensor (analog)
* GPIO 2 → WS2812B LED-Ring
* 3.3V / 5V → Spannungsversorgung
* GND → gemeinsame Masse

---

## Kommunikationsprozess

```plaintext
Feuchtigkeits- & Lichtsensor
        ↓
      ESP32
        ↓ POST (alle 30s)
   Server / load.php
        ↓
    MySQL-Datenbank
        ↑
      Web-UI (unload.php)
        ↑
ESP32 ← GET status.php
```

---

## Umsetzungsprozess

### Hardware-Einrichtung:

* Sensoren auf Breadboard getestet
* Wertebereiche der Sensoren ermittelt:

  * trocken: \~2753
  * nass: \~1121
* LED-Ring an externe 5 V Versorgung angebunden
* Stromversorgung über USB

### Software-Entwicklung:

* ESP32 sendet JSON-POST an `load.php`
* `status.php` gibt bei Untergrenze `"ok": false` zurück
* `unload.php` liefert Daten für Web-Dashboard
* `main.js` verarbeitet Daten & aktualisiert Anzeige

### Kalibrierung:

* `map(Rohwert, 2753, 1121, 0, 100)` für Feuchtigkeit
* `map(Rohwert, 3297, 241, 0, 1000)` für Licht (Lux)

### Pairing & Steuerung:

* Dropdown im Frontend setzt `aktiv = TRUE` für genau eine Pflanze in der Datenbank (automatisch per JS)

---

## Known Bugs

* Bei starkem WLAN-Ausfall keine Speicherung von Messwerten (kein Buffering)
* Kein Schutz vor versehentlich mehrfacher Aktivierung gleicher Pflanze
* `status.php` reagiert nur auf letzte Messung – historische Werte fehlen

---

## Lernerfolg

* Erstes Projekt mit vollständigem **IoT-Stack** (Sensor → Server → Web → Rückmeldung)
* Erfahrung mit:

  * Mapping & Kalibrierung analoger Sensoren
  * Datenbankstruktur für Sensordaten
  * JavaScript-Datenbindung
  * Kommunikationsprotokollen (HTTP, JSON)
* Verbesserung von:

  * HTML/CSS-Gestaltung
  * Problemidentifikation über serielle Konsole
  * Testbarkeit durch Modularisierung

---

## Aufgabenaufteilung

| Aufgabe                   | Wer          |
| ------------------------- | ------------ |
| Hardware-Setup            | Teamarbeit   |
| Arduino-Programmierung    | Luca         |
| PHP Backend (API)         | Luca         |
| Web-Dashboard (JS + HTML) | Luca         |
| Kalibrierung Sensoren     | Fausto       |
| Dokumentation & Design    | Fausto       |

---

## Learnings & Herausforderungen

* Kalibrierung war aufwendig, da Sensordaten sehr variabel sind → Messwerte mehrfach validiert
* WLAN-Probleme bei Nutzung mit Hotspot → Lösung: feste IP & Retry-Logik
* Fehlende 5V-Stabilität für LED-Ring → separate Spannungsquelle nötig
* Debugging von PHP war unerwartet aufwändig (v. a. bei JSON und DB)

---

## Credits

Dieses Projekt wurde mit Hilfe von:

* **ChatGPT** (Konzeption, Code, Doku)
* **Arduino Doku**
* **Open Source Bibliotheken**
* **GitHub Copilot** (Teilweise für Frontend-Snippets)

entwickelt.
Alle Teile wurden dokumentiert und kommentiert, sodass eine Weiterentwicklung möglich ist.
