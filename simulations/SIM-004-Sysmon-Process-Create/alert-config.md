# SIM-004 – Sysmon Process Create (T1059) – Detection Alert (Optional)

**Symbolic ID:** LAB-SIM-004-PROCESS-CREATE  
**MITRE Technique:** T1059 – Command and Scripting Interpreter  
**Severity:** Informational  
**Status:** Documented (Alert Optional)

---

## 🎯 Alert Purpose

This alert is designed to **surface process execution activity**
associated with **command and scripting interpreters** on a Windows endpoint.

SIM-004 focuses on **execution visibility and baseline establishment**.
Alerting is **not required** for simulation validation, but is documented here
to demonstrate how execution telemetry *could* be operationalized in a SOC.

The alert highlights:
- Process creation events (Event ID 4688)
- Command interpreter execution (cmd.exe, powershell.exe)
- Parent → child execution context
- Command-line visibility

---

## 🔎 Detection Logic (Alert Search)

This alert uses the baseline execution queries defined in `queries.md`
and reflects **real-world Windows logging behavior**.

```spl
index=winevent_security EventCode=4688 host="Windows11Pro"
(New_Process_Name="*cmd.exe" OR New_Process_Name="*powershell.exe")
| eval actor=lower(coalesce(Account_Name, SubjectUserName))
| eval simulation_id="SIM-004"
| eval symbolic_id="LAB-SIM-004-PROCESS-CREATE"
| table _time host actor New_Process_Name Parent_Process_Name Process_Command_Line simulation_id symbolic_id
| sort -_time
```

### Detection Notes

- Event ID **4688** is the authoritative process creation source
- This alert does **not** assume malicious intent
- Results are informational and intended for visibility
- Command-line context is preserved for analyst review

---

## ⏱️ Scheduling Configuration (If Enabled)

- **Alert Type:** Scheduled  
- **Run Frequency:** Every 15 minutes  
- **Time Range:** Last 15 minutes  

This relaxed schedule minimizes noise while still providing
execution visibility if the alert is enabled.

---

## 🚨 Trigger Conditions

- **Trigger When:** Number of Results ≥ 1  
- **Throttle Period:** 30 minutes  

> ⚠️ **Note**  
> Frequent triggering is expected in normal environments.  
> This alert should remain **informational** and **non-paging**.

---

## ⚠️ Severity Classification

- **Severity Level:** Informational  

**Rationale:**
- Process execution alone is not inherently malicious
- This alert supports visibility, hunting, and baseline analysis
- Escalation and high-confidence alerting are introduced in **SIM-005**

---

## 📤 Alert Output Fields

The following fields should be included in the alert payload:

- `_time`
- `host`
- `actor`
- `New_Process_Name`
- `Parent_Process_Name`
- `Process_Command_Line`
- `simulation_id`
- `symbolic_id`

These fields support:
- Execution tracking
- Parent → child analysis
- Analyst triage and context building

---

## 🧾 Example Alert Output (Symbolic)
```text
_time: 2025-03-06 13:04:21
host: Windows11Pro
actor: local.lab\labuser
New_Process_Name: C:\Windows\System32\WindowsPowerShell\v1.0\powershell.exe
Parent_Process_Name: C:\Windows\System32\cmd.exe
Process_Command_Line: powershell.exe -Command "Get-Process"
simulation_id: SIM-004
symbolic_id: LAB-SIM-004-PROCESS-CREATE
```

---

## 🛠️ Recommended Alert Actions

For this simulation:
- ✅ Log alert results for baseline tracking
- ⛔ No automated remediation
- ⛔ No paging or escalation

This alert is intended to support:
- Threat hunting
- Baseline understanding
- Analyst familiarity with execution telemetry

---

## 🧭 Analyst Response Guidance (If Reviewed)

If an analyst reviews this alert:
1. Confirm execution context (standard vs elevated)
2. Review parent and child process lineage
3. Examine command-line arguments
4. Compare activity against known baseline behavior
5. Determine if further investigation is required

---

## ✅ Validation Checklist

- [ ] Alert created in Splunk (optional)
- [ ] Alert enabled (optional)
- [x] Detection logic documented
- [x] Alert logic aligned with `queries.md`
- [x] Execution behavior understood
- [x] Simulation documentation complete

---

## 🏁 Status

- Alert logic documented for reference
- Alerting intentionally optional
- Baseline execution validated
- Simulation complete

> This alert configuration provides **contextual execution visibility**
> and directly supports escalation detection introduced in  
> **SIM-005 – Privilege Escalation (T1055)**.

