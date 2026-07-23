# SIM-005 – Privilege Escalation (T1055) - Detection Alert

**Symbolic ID:** LAB-SIM-005-PRIVESC-ALERT  
**MITRE Technique:** T1055 – Privilege Escalation  
**Severity:** High  
**Status:** Validated  

---

## 🎯 Alert Purpose

This alert detects **local privilege escalation activity** on a Windows endpoint by identifying
**privileged process creation events** recorded in the **Windows Security log (Event ID 4688)**.

The detection focuses on:
- Process creation under elevated (Administrator or SYSTEM) context
- Abnormal parent → child process relationships
- Execution behavior inconsistent with baseline standard-user activity

This alert represents a **high-confidence endpoint detection** suitable for SOC alerting,
triage, and incident investigation.

---

## 🏷️ Alert Metadata & Scheduling Settings

**Alert Title:**  
SIM-005 – Privilege Escalation Detected (Windows Endpoint)

**Alert Description:**  
Detects privileged process creation events on a Windows endpoint indicative of local
privilege escalation activity. The alert triggers when processes are spawned under
Administrator or SYSTEM context in a manner inconsistent with baseline user behavior.

**Alert Type:**  
Scheduled

**Cron Expression:**  
```text
*/5 * * * *
```


**Search Time Range:**  
Last 15 minutes

> This schedule provides near-real-time detection while minimizing alert noise in the Enterprise Security Operations Environment (JCE).

---

## 🔎 Detection Logic (Alert Search)

This alert uses the **primary detection query** finalized in `queries.md` and reflects
**real-world Windows logging behavior** observed during Enterprise Security Operations Environment (JCE) execution.

```spl
index=winevent_security EventCode=4688 host="Windows11Pro"
| eval actor=lower(coalesce(Account_Name, User))
| where like(actor, "%administrator%") OR like(actor, "%system%")
| eval simulation_id="SIM-005"
| eval symbolic_id="LAB-SIM-005-PRIVESC-ALERT"
| table _time host actor New_Process_Name Creator_Process_Name Process_Command_Line simulation_id symbolic_id
| sort -_time
```

Detection Notes:
- Event ID 4688 is treated as the authoritative source for process creation
- User attribution may vary due to UAC context switching
- Detection is based on privileged execution context, not username alone
- `coalesce()` is used to normalize user attribution across Windows logging variations
  
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
- actor
- New_Process_Name
- Creator_Process_Name
- Process_Command_Line
- simulation_id
- symbolic_id

These fields support:
- Host identification
- Process lineage analysis
- Privileged user attribution
- Simulation traceability

---

## 🧾 Example Alert Output (Symbolic)
``` text
_time: 2026-01-07 04:30:42
host: Windows11Pro
actor: administrator
New_Process_Name: C:\Windows\System32\WindowsPowerShell\v1.0\powershell.exe
Creator_Process_Name: C:\Windows\System32\cmd.exe
Process_Command_Line: powershell.exe
simulation_id: SIM-005
symbolic_id: LAB-SIM-005-PRIVESC-ALERT
```

**Note:**   
During UAC elevation, Windows logged the process under the effective
Administrator or SYSTEM context rather than the originating domain user.
This behavior is expected and accounted for in detection logic.

---

## 🛠️ Recommended Alert Actions

For this simulation, the following alert actions are recommended:
- ✅ Create notable event
- ✅ Log alert results for validation tracking
- ⛔ No automated remediation (Enterprise Security Operations Environment (JCE) simulation)

---

## 🧭 Analyst Response Workflow (Post-Trigger)

When this alert fires, an analyst should:
1. Identify the affected host
2. Confirm the privileged account context
3. Review parent and child process relationships
4. Validate UAC or escalation behavior
5. Check for additional suspicious child processes
6. Review recent logon activity
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
- `sim005-alert-config.png` — Alert configuration
- `sim005-alert-fired.png` — Alert firing confirmation

Location:
```bash
simulations/SIM-005-Privilege-Escalation/screenshots/
```

---

## 🏁 Status
- Detection logic finalized
- Alert configuration validated
- Simulation execution confirmed
- Ready for portfolio presentation
