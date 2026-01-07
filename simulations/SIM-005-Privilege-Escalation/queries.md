# SIM-005 – Privilege Escalation (T1055) – SPL Queries

This file documents all Splunk searches used to detect, validate, and correlate
**local privilege escalation behavior** on a Windows 11 endpoint (**Windows11Pro**).  

This simulation reflects **real-world Windows logging behavior**, where
**Windows Security Event ID 4688** is the authoritative source for process creation,
and **Sysmon telemetry is supplemental enrichment** when available.

---

## 1. Baseline Process Creation (Non-Elevated)

**Purpose:**  
Establish normal, non-privileged process execution for comparison.

```spl
index=winevent_security EventCode=4688 host="Windows11Pro"
| eval actor=lower(coalesce(Account_Name, User))
| table _time host actor New_Process_Name Creator_Process_Name Process_Command_Line
| sort -_time
```

What This Confirms:
- Standard user context (e.g. labuser)
- Normal parent/child relationships
- No elevated execution

---

## 2. Elevated Process Creation (PRIMARY DETECTION – Windows Security)

Purpose:
Detect privileged process execution via UAC elevation or administrator context.
```spl
index=winevent_security EventCode=4688 host="Windows11Pro"
| eval actor=lower(coalesce(Account_Name, User))
| where like(actor, "%administrator%") OR like(actor, "%system%")
| table _time host actor New_Process_Name Creator_Process_Name Process_Command_Line
| sort -_time
```

What This Confirms:
- Privileged execution context
- Clear privilege boundary crossing
- Reliable escalation signal independent of Sysmon

---

## 3. Suspicious Parent → Child Process Chains (Security Logs)

**Purpose:**  
Identify abnormal parent → child process relationships commonly observed during
privilege escalation scenarios.

```spl
index=winevent_security EventCode=4688 host="Windows11Pro"
| eval actor=lower(coalesce(Account_Name, User))
| eval parent_process=coalesce(Parent_Process_Name, Creator_Process_Name)
| where like(parent_process, "%cmd.exe")
| where like(New_Process_Name, "%powershell.exe") OR like(New_Process_Name, "%notepad.exe")
| table _time host actor parent_process New_Process_Name Process_Command_Line
| sort -_time
```
Why This Matters:  
- Elevated command shells spawning child processes is a high-risk behavioral pattern
- Parent → child process analysis reflects real attacker tradecraft
- Using process lineage rather than usernames reduces reliance on brittle identity fields
- Results in a low false-positive rate in baseline lab environments

---

## 4. Supplemental Sysmon Process Creation (If Available)

Purpose:
Provide additional context using Sysmon process creation events.

> ⚠️ Sysmon events are ingested into index=winlog in this lab environment.
```spl
index=winlog EventCode=1 host="Windows11Pro"
| table _time host User Image ParentImage CommandLine IntegrityLevel
| sort -_time
```

What This Adds:
- IntegrityLevel confirmation (High / System)
- Clear parent/child lineage
- Additional confidence when correlating with Security logs

---

## 5. Cross-Source Correlation (Security + Sysmon)

Purpose:
Correlate Windows Security process creation with Sysmon telemetry for higher confidence.
```spl
(
  index=winevent_security EventCode=4688 host="Windows11Pro"
)
OR
(
  index=winevent_sysmon EventCode=1 host="Windows11Pro"
)
| eval actor=lower(coalesce(Account_Name, User))
| eval process=coalesce(New_Process_Name, Image)
| eval parent_process=coalesce(Creator_Process_Name, ParentImage)
| eval command_line=coalesce(Process_Command_Line, CommandLine)
| eval simulation_id="SIM-004"
| table _time host actor process parent_process command_line IntegrityLevel simulation_id
| sort -_time
```

What This Proves:
- Privilege escalation occurred
- Confirmed at one or more telemetry layers
- Alert-ready detection signal

---

## 6. ✅ PRIMARY ALERT QUERY (FINAL)

Purpose:
This is the exact query used to trigger the Splunk alert for SIM-003.
```spl
index=winevent_security EventCode=4688 host="Windows11Pro"
| eval actor=lower(coalesce(Account_Name, User))
| where like(actor, "%administrator%") OR like(actor, "%system%")
| eval simulation_id="SIM-005"
| eval symbolic_id="LAB-SIM-005-PRIVESC-ALERT"
| table _time host actor New_Process_Name Creator_Process_Name Process_Command_Line simulation_id symbolic_id
| sort -_time
```

Expected Outcome:
- One or more results → alert fires
- Privileged execution confirmed
- Symbolic ID present: `LAB-SIM-005-PRIVESC-ALERT`

---

✅ Interpretation Guide   
| Result                      |	Meaning                     |
|-----------------------------|-----------------------------|
| Privileged actor detected	  | Escalation occurred         |
| Abnormal parent/child chain	| Suspicious behavior         |
| Security 4688 present       |	Authoritative confirmation  |
| Sysmon enrichment present	  | Higher confidence           |
| Alert fires	                | Detection validated         |

> This file represents the finalized detection engineering logic for SIM-003
> and accurately reflects the telemetry and constraints observed in the lab.
