# SIM-001 – Phishing Email (T1566.002 – Spearphishing Link)

## 🎯 Goal

Simulate a phishing email that delivers a suspicious URL to a user, then validate that:
- Network telemetry (Suricata/Zeek via Security Onion) observes the click,
- Endpoint telemetry (Sysmon on Windows 11) records process activity,
- Splunk correlates these signals into a detection/alert.

This simulation supports the **LAB-SIM-001** row in the `detection-validation-matrix.md`.

---

## 🧩 MITRE ATT&CK Mapping

- **Technique:** T1566.002 – Spearphishing Link  
- **Tactics:** Initial Access (TA0001)  

---

## 🏗 Lab Components Used

- **Attacker:** Kali Linux (10.0.0.30)
- **Victim Endpoint:** Windows 11 (10.0.0.50) with:
  - Sysmon
  - Splunk Universal Forwarder
- **Security Stack:**
  - Security Onion (Suricata + Zeek)
  - Splunk Enterprise (Ubuntu – 10.0.0.60)
  - pfSense (10.0.0.1) for routing/visibility

---

## 📂 Files in This Simulation

- `steps.md` – Exact steps to perform the simulation.
- `logs.md` – Symbolic sample logs (HTTP, IDS, Sysmon, Splunk).
- `queries.md` – SPL used to hunt and detect this activity.
- `alert-config.md` – Splunk alert configuration and symbolic ID.
- `screenshots/` – Evidence:
  - Email being viewed
  - Network alert(s)
  - SPL results
  - Alert firing

---

## ✅ Success Criteria

- A user on the Windows 11 endpoint opens a **test phishing email** and clicks a suspicious URL.
- Suricata (Security Onion) generates an alert on the HTTP request.
- Sysmon records browser or process execution tied to the click.
- Splunk correlates the event and triggers an alert with a **symbolic ID**:
  - `LAB-SIM-001-PHISHING-ALERT`

---

## 🧾 Status

- [ ] Steps executed
- [ ] Logs captured and saved to `logs.md`
- [ ] SPL queries tested and refined
- [ ] Splunk alert configured and tested
- [ ] Screenshots captured and saved to `screenshots/`
- [ ] Detection matrix updated to “Validated”

Update this checklist as you complete the hands-on lab.
