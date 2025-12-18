# SIM-001 – Phishing Email (T1566.002) – Spearphishing Link

## 🎯 Goal

Simulate a phishing email that delivers a suspicious URL to a user, then validate that:

- Network telemetry (Suricata/Zeek via Security Onion) observes the click,
- Endpoint telemetry (Windows Security Event 4688 with command line logging) records browser execution,
- Splunk correlates these signals into a detection and triggers a validated alert.

This simulation supports the **LAB-SIM-001** row in the `detection-validation-matrix.md`.

---

## 🧩 MITRE ATT&CK Mapping

- **Technique:** T1566.002 – Spearphishing Link  
- **Tactic:** Initial Access (TA0001)

---

## 🏗 Lab Components Used

- **Attacker:**  
  - Kali Linux (10.0.0.30) – Hosted phishing landing page via Python HTTP server

- **Victim Endpoint:**  
  - Windows 11 (10.0.0.50) with:
    - Windows Security Auditing (EventCode 4688)
    - Command-line process auditing enabled
    - Splunk Universal Forwarder

- **Security Stack:**
  - Security Onion (Suricata + Zeek)
  - Splunk Enterprise (Ubuntu – 10.0.0.60)
  - pfSense (10.0.0.1) for routing and visibility

---

## 📂 Files in This Simulation

- `steps.md` – Exact steps used to perform the simulation.
- `logs.md` – Symbolic sample logs (endpoint, network, alert).
- `queries.md` – SPL used to hunt and detect this activity.
- `alert-config.md` – Splunk alert configuration and symbolic ID.
- `screenshots/` – Evidence:
  - Email being viewed / phishing page loaded
  - Chrome execution evidence
  - URL detection in Splunk
  - Correlation query
  - Alert configuration
  - Alert firing

---

## ✅ Success Criteria (Validated)

- A user on the Windows 11 endpoint opens a **test phishing email** and clicks a suspicious URL ✅  
- Windows generates **EventCode 4688** for `chrome.exe` ✅  
- The phishing URL appears in **`Process_Command_Line`** ✅  
- Security telemetry confirms HTTP traffic to Kali ✅  
- Splunk correlates endpoint execution and URL ✅  
- Splunk triggers an alert with symbolic ID:

---

## LAB-SIM-001-PHISHING-ALERT


✅ **All success criteria met and validated with screenshots.**

---

## 🔍 Detection Summary

| Component | Evidence |
|----------|----------|
| Endpoint Execution | Windows Security EventCode 4688 |
| Command Line Capture | `Process_Command_Line` |
| Phishing URL | `http://10.0.0.30:8080` |
| SIEM Correlation | Splunk SPL |
| Alerting | `LAB-SIM-001-PHISHING-ALERT` |
| Validation | Screenshots + Symbolic Logs |

---

## 🧾 Status Checklist (Final)

- [x] Steps executed  
- [x] Logs captured and saved to `logs.md`  
- [x] SPL queries tested and refined  
- [x] Splunk alert configured and tested  
- [x] Screenshots captured and saved to `screenshots/`  
- [x] Detection matrix updated to “Validated”  

---

## ⚠️ Issues Encountered & Resolutions

During execution of this simulation, multiple real-world operational and
detection engineering challenges were encountered and resolved.

These issues include:
- Log ingestion failures
- Forwarder authentication problems
- Missing audit policies
- Endpoint execution gaps
- Network visibility constraints

Each issue was investigated, root-caused, and resolved using standard
SOC troubleshooting techniques.  
👉 **Full technical breakdown:**  
[SIM-001 – Issues & Resolutions](../../issues-and-resolutions/sim-001-phishing-email.md)

---

## ✅ FINAL STATUS

**SIM-001 – Phishing Email Detection is COMPLETE, VALIDATED, and PRODUCTION-READY.**

This simulation demonstrates a full real-world SOC workflow:

- Attack execution  
- Telemetry validation  
- Detection engineering  
- Alert tuning  
- Operational troubleshooting  
- Final alert confirmation  

It now serves as a **defensible, interview-ready detection project** in the JCE Cyber Lab.
