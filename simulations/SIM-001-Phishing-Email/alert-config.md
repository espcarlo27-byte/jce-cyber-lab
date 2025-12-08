# SIM-001 – Phishing Email Detection Alert

**Symbolic ID:** LAB-SIM-001-PHISHING-ALERT  
**Technique:** T1566.002 – Phishing: Link  
**Severity:** Medium  
**Status:** Ready for Deployment  

---

## 🎯 Alert Purpose

This alert detects a user clicking a phishing hyperlink delivered via a simulated HR policy email.  
The detection is based on correlating Google Chrome execution with the presence of an HTTP/HTTPS URL in the command line.

This alert represents the **first-stage user interaction detection** in the attack chain.

---

## 🔎 Detection Logic (Alert Search)

This query is copied directly from the primary correlation query in `queries.md` and is used as the alert trigger.

```spl
(index=winevent_system OR index=winevent_security OR index=lab_index)
(Image="*\\chrome.exe" OR NewProcessName="*\\chrome.exe")
("http://" OR "https://")
| eval simulation_id="SIM-001"
| eval symbolic_id="LAB-SIM-001-PHISHING-ALERT"
| table _time, host, user, Image, ParentImage, CommandLine, simulation_id, symbolic_id
| sort - _time
```

---

## ⏱️ Scheduling Configuration
- Alert Type: Scheduled
- Run Frequency: Every 5 minutes
- Time Window: Last 15 minutes

***This ensures near real-time detection while preventing excessive system load in a lab environment.***

---

## 🚨 Trigger Conditions
- Trigger When: Number of Results ≥ 1
- Throttle Period: 10 minutes (prevents multiple alerts from the same user repeatedly clicking)

***This condition ensures that even a single phishing click will trigger the alert.***

---

## ⚠️ Severity Classification
- Severity Level: Medium
- Rationale: ***A phishing link was clicked by a user, but no confirmed payload execution or privilege escalation is detected at this stage.***

---

## 📤 Alert Output Fields

The following fields must appear in the alert payload for proper investigation and dashboard integration:
- _time
- host
- user
- Image
- ParentImage
- CommandLine
- simulation_id
- symbolic_id

These fields allow:
- User attribution
- Host identification
- Process lineage tracking
- Simulation tagging

🧾 Example Alert Output (Symbolic)
```
_time: 2025-02-19 14:22:11
host: WIN11-LAB
user: LAB\testuser
Image: C:\Program Files\Google\Chrome\Application\chrome.exe
ParentImage: C:\Program Files\Microsoft Office\root\Office16\OUTLOOK.EXE
CommandLine: chrome.exe http://10.0.0.30:8080
simulation_id: SIM-001
symbolic_id: LAB-SIM-001-PHISHING-ALERT
```

---

## 🛠️ Recommended Alert Actions

The following alert actions are recommended for this simulation:
- ✅ Create notable event
- ✅ Write alert result to lab_index for validation tracking
- ✅ Email notification (optional, for SOC-style workflows)

---

## 🧭 Analyst Response Workflow (Post-Trigger)

Once this alert fires, the responding analyst should:
1. Validate the impacted user account
2. Validate the affected endpoint
3. Confirm the legitimacy of the phishing URL
4. Review the parent process (email client vs explorer)
5. Run PowerShell follow-on detection queries
6. Check for lateral movement or persistence
7. Document findings in the incident report

---

## ✅ Validation Checklist

- Alert created in Splunk
- Alert enabled
- Alert scheduled correctly
- Trigger condition validated
- Alert fires on phishing click
- Output fields confirmed
- Symbolic ID appears in alert output

---

## 📎 Required Evidence

Screenshots to collect after live execution:
- sim001-alert-config.png — Alert configuration screen
- sim001-alert-fired.png — Triggered alert confirmation

Location:
simulations/SIM-001-Phishing-Email/screenshots/

---

## 🏁 Status

Detection logic prepared.
Alert configuration ready.
Awaiting live phishing execution and validation.

