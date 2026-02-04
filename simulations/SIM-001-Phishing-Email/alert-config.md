# SIM-001 – Phishing Email (T1566.002) – Alert Configuration

This document describes how the **Splunk alert** was configured to detect the
phishing link execution simulated in SIM-001.

The alert is based on **endpoint process telemetry** (Windows Security Event ID 4688).
Network telemetry is supplemental and not required for alert logic.

---

## 🎯 Detection Logic

The alert identifies when:

- Google Chrome executes  
- A URL is present in the command line  
- The activity aligns with user-driven phishing interaction

---

## 🔍 Correlation Query (Alert Search)

```spl
(index=winevent_security OR index=winevent_system)
EventCode=4688
New_Process_Name="*\\chrome.exe"
Process_Command_Line="*http*"
| eval simulation_id="SIM-001"
| eval symbolic_id="LAB-SIM-001-PHISHING-ALERT"
| table _time, host, user, New_Process_Name, Process_Command_Line, simulation_id, symbolic_id
| sort - _time
```

## ⚙️ Alert Configuration Settings

| Setting | Value |
|--------|------|
| Alert Name | LAB-SIM-001-PHISHING-ALERT |
| App | Search & Reporting |
| Schedule | Every 5 minutes |
| Time Range | Last 15 minutes |
| Trigger Condition | Number of Results > 0 |
| Trigger Type | Per Result |
| Throttle | 10 minutes |
| Severity | Medium |

---

## 🧾 Example Alert Output (Symbolic – Validated)

📌 **Evidence ID:** `E-SIM001-008`

📸 **Screenshot Reference:**

- `sim001-evidence-004-splunk-correlation.png`  
- `sim001-evidence-005-alert-fired.png`

```yaml
_time: 2026-01-30 01:13:10
host: WIN11-LAB
user: LAB\it.helpdesk1
New_Process_Name: C:\Program Files\Google\Chrome\Application\chrome.exe
Process_Command_Line: "C:\Program Files\Google\Chrome\Application\chrome.exe" http://<KALI_IP>:8080
simulation_id: SIM-001
symbolic_id: LAB-SIM-001-PHISHING-ALERT
```

## 🧩 Detection Rationale

This alert is based on **process creation telemetry**, which:

- Is reliable across Windows environments  
- Captures user-driven execution  
- Preserves command-line artifacts (including URLs)  

Email delivery context (Zimbra) provides realism but is not required for detection.

---

## 📎 Evidence References

### Alert Configuration Evidence

📌 **Evidence ID:** `E-SIM001-009`  
- `sim001-evidence-004-alert-config.png` — Alert configuration screen  

### Alert Trigger Evidence

📌 **Evidence ID:** `E-SIM001-010`  
- `sim001-evidence-005-alert-fired.png` — Triggered alert confirmation  

---

## 🏁 Final Validation Statement

The alert successfully detected the phishing link execution and demonstrates:

- Endpoint-driven detection  
- Correlation of execution artifacts  
- Reliable alerting behavior  
- Alignment with MITRE ATT&CK T1566.002  

SIM-001 detection logic is now fully operational.

---

🎯 **SIM-001 is now fully synchronized across all files.**

Next SIM to modernize like this will take half the time because your structure is now enterprise-grade.


