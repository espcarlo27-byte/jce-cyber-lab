# SIM-001 – Phishing Email (T1566.002) – Alert Configuration

This document describes how the Splunk alert was configured to detect
phishing link interaction simulated in SIM-001.

The alert is based on **multi-layer correlation**:

- Endpoint execution telemetry (Event ID 4688)
- Outbound network communication to attacker infrastructure
- Identity attribution (user context)

Command-line URL visibility is NOT required for detection.

---

## 🎯 Detection Logic Overview

The alert identifies when:

- A browser process executes under the simulated user
- An outbound connection to attacker infrastructure occurs
- Events occur within a short correlation window

Detection Model:

Browser Execution  
AND  
Outbound Network Communication  
Within 15-Minute Window  

---

## 🔍 Correlation Query (Alert Search)

```spl
(
    index=winevent_security EventCode=4688
    user="it.helpdesk1"
    new_process_name="*chrome.exe"
)
OR
(
    index=network_logs dest_ip="<KALI_IP>"
)
| eval simulation_id="SIM-001"
| eval symbolic_id="LAB-SIM-001-PHISHING-ALERT"
| table _time host user dest_ip new_process_name simulation_id symbolic_id
| sort - _time
```

This query captures:

- Endpoint execution signal  
- Network confirmation signal  
- User attribution context  

---

## ⚙️ Alert Configuration Settings

| Setting           | Value                          |
|-------------------|--------------------------------|
| Alert Name        | LAB-SIM-001-PHISHING-ALERT     |
| App               | Search & Reporting             |
| Schedule          | Every 5 minutes                |
| Time Range        | Last 15 minutes                |
| Trigger Condition | Number of Results > 0          |
| Trigger Type      | Per Result                     |
| Throttle          | 10 minutes                     |
| Severity          | Medium                         |

---

## 🧠 Detection Rationale

This alert is based on layered evidence rather than single-field matching.

It avoids brittle logic such as:
```ini
Process_Command_Line="*http*"
```

### Why?

Webmail-based phishing clicks typically occur within an existing browser session and may not expose URL parameters in process creation logs.

Reliable detection therefore requires:

- Execution confirmation  
- Network confirmation  
- User context  

This mirrors enterprise SOC methodology.

---

## 🧾 Example Alert Output (Validated)

📌 **Evidence ID:** `E-SIM001-008`
```yaml
_time: 2026-02-16 23:57:58
host: WIN11Pro
user: LAB\it.helpdesk1
new_process_name: chrome.exe
dest_ip: <KALI_IP>
simulation_id: SIM-001
symbolic_id: LAB-SIM-001-PHISHING-ALERT
```

---

## 📎 Evidence References

### Alert Configuration Evidence

📌 `sim001-evidence-alert-config.png`

### Alert Trigger Evidence

📌 `sim001-evidence-alert-fired.png`

---

## 🏁 Final Validation Statement

The alert successfully detected phishing interaction through:

- Endpoint execution telemetry  
- Network correlation  
- Identity attribution  

**Detection Authority:** Multi-Layer Correlation  

This reflects enterprise SOC detection design and avoids reliance on
command-line URL string matching.




