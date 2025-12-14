# SIM-003 – Privilege Escalation Detection Alert

**Symbolic ID:** LAB-SIM-003-PRIVESC-ALERT  
**MITRE Technique:** T1055 – Privilege Escalation  
**Severity:** High  
**Status:** Validated  

---

## 🎯 Alert Purpose

This alert detects **local privilege escalation activity** on a Windows endpoint by identifying
processes executed with **High or System integrity levels**.

The detection focuses on:
- Elevated process creation
- Abnormal parent → child relationships
- Execution contexts inconsistent with baseline user behavior

This alert represents a **high-confidence endpoint detection** suitable for SOC alerting and
incident triage.

---

## 🔎 Detection Logic (Alert Search)

This alert uses the **primary detection query** finalized in `queries.md` and reflects
real-world Windows logging behavior observed during execution.

```spl
index=winevent_sysmon EventCode=1 host=WIN11*
| where IntegrityLevel="High" OR IntegrityLevel="System"
| eval simulation_id="SIM-003"
| eval symbolic_id="LAB-SIM-003-PRIVESC-ALERT"
| table _time host User New_Process_Name Parent_Process_Name Process_Command_Line IntegrityLevel simulation_id symbolic_id
| sort -_time
```

Detection Notes:
- Sysmon Event ID 1 provides the most reliable signal for integrity level elevation
- User attribution may vary due to UAC context switching
- Elevated activity is validated by integrity level rather than username alone

---

## ⏱️ Scheduling Configuration
- Alert Type: Scheduled
- Run Frequency: Every 5 minutes
- Time Range: Last 15 minutes

This configuration provides near-real-time detection while minimizing alert noise
in a lab environment.

---

## 🚨 Trigger Conditions
- Trigger When: Number of Results ≥ 1
- Throttle Period: 10 minutes

This ensures:
- A single confirmed privilege escalation triggers the alert
- Repeated alerts from the same activity are suppressed

---

## ⚠️ Severity Classification
- Severity Level: High   
Rationale:
Privilege escalation represents a compromise of system integrity and is commonly associated with:
- Credential access
- Lateral movement
- Persistence establishment

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
- Host identification
- Process lineage analysis
- Privilege context validation
- Simulation traceability

---

## 🧾 Example Alert Output (Symbolic)
``` text
_time: 2025-03-05 14:18:42
host: WIN11-LAB
User: administrator
New_Process_Name: C:\Windows\System32\WindowsPowerShell\v1.0\powershell.exe
Parent_Process_Name: C:\Windows\System32\cmd.exe
Process_Command_Line: powershell.exe
IntegrityLevel: High
simulation_id: SIM-003
symbolic_id: LAB-SIM-003-PRIVESC-ALERT
```

**Note:**   
During UAC elevation, Windows logged the process under the effective
administrator context rather than the originating domain user.
This behavior is expected and accounted for in detection logic.

---

## 🛠️ Recommended Alert Actions

For this simulation, the following alert actions are recommended:
- ✅ Create notable event
- ✅ Log alert results for validation tracking
- ⛔ No automated remediation (lab simulation)

---

## 🧭 Analyst Response Workflow (Post-Trigger)

When this alert fires, an analyst should:
1. Identify the affected host
2. Review elevated process and parent process
3. Confirm integrity level escalation
4. Identify spawned child processes
5. Review recent logon and UAC activity
6. Assess whether escalation was expected or malicious
7. Document findings in the incident record

---

## ✅ Validation Checklist
- [x] Alert created in Splunk
- [x] Alert enabled
- [x] Scheduling verified
- [x] Trigger condition validated
- [x] Alert fired during simulation
- [x] Symbolic ID present in results
- [x] Screenshots captured

---

## 📎 Required Evidence

Screenshots captured after execution:
- ***sim003-alert-config.png*** — Alert configuration
- ***sim003-alert-fired.png*** — Alert firing confirmation

Location:
```bash
simulations/SIM-003-Privilege-Escalation/screenshots/
```

---

## 🏁 Status
- Detection logic finalized
- Alert configuration validated
- Simulation execution confirmed
- Ready for portfolio presentation
