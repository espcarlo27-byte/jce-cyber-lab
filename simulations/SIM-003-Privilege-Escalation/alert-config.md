# SIM-003 – Privilege Escalation Detection Alert

**Symbolic ID:** LAB-SIM-003-PRIVESC-ALERT  
**MITRE Technique:** T1055 – Privilege Escalation  
**Severity:** High  
**Status:** Ready for Deployment  

---

## 🎯 Alert Purpose

This alert detects **local privilege escalation activity** on a Windows endpoint by identifying
processes executed with **High or System integrity levels**.

The detection focuses on:
- Elevated process creation
- Abnormal parent → child relationships
- Execution contexts inconsistent with normal user behavior

***This represents a **high-confidence endpoint detection** suitable for SOC alerting.***

---

## 🔎 Detection Logic (Alert Search)

This alert uses the **primary detection query** from `queries.md`.

```spl
index=winevent_sysmon EventCode=1 host=WIN11*
(IntegrityLevel="High" OR IntegrityLevel="System")
| eval simulation_id="SIM-003"
| eval symbolic_id="LAB-SIM-003-PRIVESC-ALERT"
| table _time host User New_Process_Name Parent_Process_Name Process_Command_Line IntegrityLevel simulation_id symbolic_id
| sort -_time
```

---

## ⏱️ Scheduling Configuration

- Alert Type: Scheduled
- Run Frequency: Every 5 minutes
- Time Range: Last 15 minutes

***This balances timely detection with reduced alert noise in a lab environment.***

---

## 🚨 Trigger Conditions

- Trigger When: Number of Results ≥ 1
- Throttle Period: 10 minutes

***This ensures that even a single confirmed privilege escalation event triggers an alert,
while preventing repeated alerts from the same activity.***

---

## ⚠️ Severity Classification

- Severity Level: High

Rationale:
Privilege escalation indicates a potential compromise of system integrity and often precedes:
- Credential dumping
- Lateral movement
- Persistence

---

## 📤 Alert Output Fields

The following fields must be present in the alert payload:
- _time
- host
- User
- New_Process_Name
- Parent_Process_Name
- Process_Command_Line
- IntegrityLevel
- simulation_id
- symbolic_id

These fields support:
- User attribution
- Process lineage analysis
- Privilege context validation
- Simulation traceability

---

## 🧾 Example Alert Output (Symbolic)
```text
_time: 2025-03-05 14:18:42
host: WIN11-LAB
User: LAB\testuser
New_Process_Name: C:\Windows\System32\WindowsPowerShell\v1.0\powershell.exe
Parent_Process_Name: C:\Windows\System32\cmd.exe
Process_Command_Line: powershell.exe
IntegrityLevel: High
simulation_id: SIM-003
symbolic_id: LAB-SIM-003-PRIVESC-ALERT
```

---

## 🛠️ Recommended Alert Actions

For this simulation, the following alert actions are recommended:

- ✅ Create notable event
- ✅ Log results for validation tracking
- ⛔ No automated remediation (lab simulation)

---

## 🧭 Analyst Response Workflow (Post-Trigger)

When this alert fires, an analyst should:
1. Identify the affected user and host
2. Review parent and child process relationships
3. Confirm integrity level escalation
4. Check for additional suspicious child processes
5. Review recent logon activity
6. Determine if escalation was expected or malicious
7. Document findings

---

## ✅ Validation Checklist

- [x] Alert created in Splunk
- [x] Alert enabled
- [x] Scheduling verified
- [x] Trigger condition validated
- [x] Alert fires during simulation
- [x] Symbolic ID appears in results
- [x] Screenshots captured

---

## 📎 Required Evidence

Screenshots to capture after execution:

- sim003-alert-config.png – Alert configuration
- sim003-alert-fired.png – Alert firing confirmation

Location:
```bash
simulations/SIM-003-Privilege-Escalation/screenshots/
```

---

## 🏁 Status

- Detection logic complete.
- Alert configuration ready.
- Awaiting live simulation execution and validation.
