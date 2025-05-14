document.addEventListener("DOMContentLoaded", function () {
    console.log("JavaScript ist geladen!");
  
    function ladeDaten() {
      fetch("https://giessmich.lucatimlauener.ch/php/unload.php")
        .then(res => res.json())
        .then(data => {
          const mess = data.messwert;
          const pflanze = data.aktive_pflanze;
  
          // 🌡️ Messwerte anzeigen
          document.getElementById("messwert").innerHTML = `
            <h2>📟 Letzter Messwert</h2>
            <p><strong>Zeitpunkt:</strong> ${mess.timestamp}</p>
            <p><strong>Feuchtigkeit:</strong> ${mess.feuchtigkeit} %</p>
            <p><strong>Licht:</strong> ${Math.round(mess.licht)} lx</p>
          `;
  
          // 🌱 Aktive Pflanze anzeigen
          document.getElementById("pflanzen").innerHTML = `
            <h2>🌱 Aktive Pflanze</h2>
            <p><strong>${pflanze.name}</strong> (${pflanze.botanischer_name})</p>
            <p>Feuchtigkeit: ${pflanze.feuchtigkeit_min}–${pflanze.feuchtigkeit_max} %</p>
            <p>Licht: ${pflanze.licht_min}–${pflanze.licht_max} lx</p>
          `;
  
          // 💧 Feuchtigkeitsstatus
          const feuchtStatus = document.getElementById("feucht-status");
          const f = mess.feuchtigkeit;
          const fmin = pflanze.feuchtigkeit_min;
          const fmax = pflanze.feuchtigkeit_max;
  
          if (f < fmin) {
            feuchtStatus.innerText = "Gießen nötig!";
            feuchtStatus.setAttribute("data-status", "warnung");
          } else if (f > fmax) {
            feuchtStatus.innerText = "Zu viel Wasser!";
            feuchtStatus.setAttribute("data-status", "zuviel");
          } else {
            feuchtStatus.innerText = "Gegossen!";
            feuchtStatus.setAttribute("data-status", "gut");
          }
  
          // 💡 Lichtstatus
          const lichtStatus = document.getElementById("licht-status");
          const l = mess.licht;
          const lmin = pflanze.licht_min;
          const lmax = pflanze.licht_max;
  
          if (l < lmin) {
            lichtStatus.innerText = "Pflanze steht zu dunkel";
            lichtStatus.setAttribute("data-status", "dunkel");
          } else if (l > lmax) {
            lichtStatus.innerText = "Pflanze steht zu hell";
            lichtStatus.setAttribute("data-status", "hell");
          } else {
            lichtStatus.innerText = "Licht perfekt!";
            lichtStatus.setAttribute("data-status", "gut");
          }
  
          // 🔁 Dropdown aktualisieren
          const select = document.getElementById("name");
          const vorher = select.value;
          select.innerHTML = "";
  
          data.alle_pflanzen.forEach(name => {
            const opt = document.createElement("option");
            opt.value = name;
            opt.textContent = name;
            if (name === pflanze.name) {
              opt.selected = true;
            }
            select.appendChild(opt);
          });
        })
        .catch(err => {
          console.error("Fehler beim Laden:", err);
          document.getElementById("messwert").innerHTML = "<p>Fehler beim Laden der Messwerte.</p>";
          document.getElementById("pflanzen").innerHTML = "<p>Fehler beim Laden der Pflanzenliste.</p>";
        });
    }
  
    // 🌱 Auswahl im Dropdown → direkt aktiv setzen
    document.getElementById("name").addEventListener("change", function () {
      const name = this.value;
  
      fetch("https://giessmich.lucatimlauener.ch/php/update.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ name: name })
      })
        .then(res => res.json())
        .then(data => {
          document.getElementById("status").innerText = data.message || "Aktualisiert.";
          ladeDaten(); // neu laden
        })
        .catch(err => {
          console.error("Fehler beim Speichern:", err);
          document.getElementById("status").innerText = "Fehler beim Speichern.";
        });
    });
  
    ladeDaten();
    setInterval(ladeDaten, 15000); // automatisch alle 15 Sekunden
  });
  