# SIM-001 – Phishing Email Detection Alert

**Symbolic ID:** LAB-SIM-001-PHISHING-ALERT  
**Technique:** T1566.002 – Phishing: Link  
**Severity:** Medium  
**Status:** ✅ Validated & Triggered  

---

## 🎯 Alert Purpose

This alert detects a user clicking a phishing hyperlink delivered through a simulated HR policy email.

Detection is based on:

- Windows Security **EventCode 4688**
- Google Chrome execution
- Presence of an **HTTP/HTTPS URL inside `Process_Command_Line`**
- Correlation and alerting via Splunk

This alert represents the **first-stage user interaction detection** in the simulated attack chain.

---

## 🔎 Detection Logic (FINAL WORKING ALERT SEARCH)

This query reflects the **actual working detection logic used in the lab** and matches the real field names ingested by Splunk.

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

---

## ⏱️ Scheduling Configuration

- Alert Type: Scheduled
- Run Frequency: Every 5 minutes
- Time Window: Last 15 minutes

***This configuration ensures near real-time detection while preventing excessive system load in a lab environment.***

---

## 🚨 Trigger Conditions

- Trigger When: Number of Results > 0
- Trigger Type: Per Result
- Throttle Period: 10 minutes
- Throttle Field: *

This ensures:
- A single phishing click triggers detection
- Repeated clicks do not cause alert flooding

---

## ⚠️ Severity Classification

- Severity Level: Medium
- Rationale:
    A phishing link was clicked by a user, indicating successful initial access via social engineering.
    No confirmed payload execution or privilege escalation is detected at this stage.

---

## 📤 Alert Output Fields

The following fields appear in the alert payload for SOC investigation and dashboard use:
- _time
- host
- user
- New_Process_Name
- Process_Command_Line
- simulation_id
- symbolic_id

These fields enable:
- User attribution
- Host identification
- Executed process visibility
- Command-line URL detection
- Simulation and detection tagging

---

## 🧾 Example Alert Output (Symbolic – Validated)

```yaml
_time: 2025-12-09 01:13:10
host: Windows11Pro
user: LAB\testuser
New_Process_Name: C:\Program Files\Google\Chrome\Application\chrome.exe
Process_Command_Line: "C:\Program Files\Google\Chrome\Application\chrome.exe" http://10.0.0.30:8080
simulation_id: SIM-001
symbolic_id: LAB-SIM-001-PHISHING-ALERT
```

---

## 🛠️ Alert Actions Configured

- ✅ Add to Triggered Alerts
- ✅ (Optional) Log event to lab_index
- ✅ Email notification supported (optional SOC workflow)

---

## 🧭 Analyst Response Workflow (Post-Trigger)

Once this alert fires, the responding analyst should:
1. Identify and validate the impacted user
2. Validate the affected endpoint
3. Investigate the phishing URL destination
4. Confirm execution source (Explorer / PowerShell)
5. Review follow-on activity for persistence or lateral movement
6. Run additional endpoint and network hunts
7. Document findings in the incident report

---

## ✅ Validation Checklist (Final)

- [x] Alert created in Splunk
- [x] Alert enabled
- [x] Alert scheduled correctly
- [x] Trigger condition validated
- [x] Alert fires on phishing click
- [x] Output fields confirmed
- [x] Symbolic ID appears in alert output

---

## Required Evidence (Captured)

- ✅ sim001-alert-config.png — Alert configuration screen
- ✅ sim001-alert-fired.png — Triggered alert confirmation-

Location: **** simulations/SIM-001-Phishing-Email/screenshots/***

---

## 🏁 Final Status

- ✅ Detection logic validated
- ✅ Alert fired successfully
- ✅ SIM-001 detection is production-ready





