# SIM-001 – Phishing Email (T1566.002) – Detection Alert

**Symbolic ID:** LAB-SIM-001-PHISHING-ALERT  
**Technique:** T1566.002 – Phishing: Link  
**Tactic:** Initial Access (TA0001)  
**Severity:** Medium  
**Status:** ✅ Validated & Triggered  

---

## 🎯 Alert Purpose

This alert detects a **user-driven phishing link click** by observing
authoritative **endpoint process execution telemetry**.

Detection is based on:

- Windows Security **EventCode 4688**
- Google Chrome execution
- Presence of a **URL in `Process_Command_Line`**
- Correlation and alerting via Splunk

> **Important:**  
> This alert is intentionally **endpoint-driven**.  
> Network telemetry (e.g., Security Onion) may provide **supplemental context**
> but is **not required** for detection or alert validity.

---

## 🔎 Detection Logic (FINAL WORKING ALERT SEARCH)

This query reflects the **validated detection logic** used in SIM-001 and
matches the actual field names ingested by Splunk.

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
- A single phishing click generates an alert
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
- Endpoint/Host identification
- Executed process visibility
- Command-line URL detection
- Simulation and detection traceability

---

## 🧾 Example Alert Output (Symbolic – Validated)

**Evidence ID:** `E-SIM001-005`
```yaml
_time: 2025-12-09 01:13:10
host: Windows11Pro
user: LAB\testuser
New_Process_Name: C:\Program Files\Google\Chrome\Application\chrome.exe
Process_Command_Line: "C:\Program Files\Google\Chrome\Application\chrome.exe" http://phish-sim.local/policy
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
3. Review the phishing URL context
4. Confirm execution source (Explorer / PowerShell)
5. Review follow-on activity for persistence or lateral movement
6. Perform additional endpoint or network hunts as needed
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

Location: `simulations/SIM-001-Phishing-Email/screenshots/`  

**Alert Configuration Evidence**  

**Evidence ID:** `E-SIM001-004`
- ✅ `sim001-evidence-004-alert-config.png` — Alert configuration screen

**Alert Trigger Evidence**  

**Evidence ID:** `E-SIM001-005`
- ✅ `sim001-evidence-005-alert-fired.png` — Triggered alert confirmation

---

## 🏁 Final Status

- ✅ Detection logic validated
- ✅ Alert fired successfully
- ✅ SIM-001 detection is production-ready

> This alert represents a realistic SOC detection for phishing link
> interaction based on authoritative endpoint telemetry.




