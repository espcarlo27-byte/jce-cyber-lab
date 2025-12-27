# SIM-004 – Sysmon Process Create (T1059) – SPL Queries

This file documents all Splunk searches used to **observe, validate, and baseline**
process execution behavior on a Windows 11 endpoint (**Windows11Pro**).

This simulation reflects **real-world Windows logging behavior**, where
**Windows Security Event ID 4688** is the authoritative source for process creation,
and **Sysmon Event ID 1** provides **supplemental endpoint-level enrichment**
when available.

SIM-004 intentionally focuses on **execution visibility**, not malicious detection
or privilege escalation.

---

## 1. Baseline Process Creation (Standard User)

**Purpose:**  
Establish normal process execution activity for a standard (non-elevated) user.

```spl
index=winevent_security EventCode=4688 host="Windows11Pro"
| eval actor=lower(coalesce(Account_Name, SubjectUserName))
| table _time host actor New_Process_Name Parent_Process_Name Process_Command_Line
| sort -_time
```

What This Confirms:
- Execution attributed to a standard user (e.g. labuser)
- Normal parent → child relationships
- No elevated context
- Reliable baseline execution telemetry

---

## 2. Command Interpreter Execution (T1059 Focus)

**Purpose:**  
Identify execution of common command and scripting interpreters
associated with MITRE ATT&CK T1059.
```spl
index=winevent_security EventCode=4688 host="Windows11Pro"
(New_Process_Name="*cmd.exe" OR New_Process_Name="*powershell.exe")
| table _time host SubjectUserName New_Process_Name Parent_Process_Name Process_Command_Line
| sort -_time
```

What This Confirms:
- Command interpreters are visible in Security logs
- Command-line arguments are captured
- Execution context is attributable and reproducible

---

## 3. Parent → Child Execution Chains (Baseline)

**Purpose:**    
Observe normal parent → child execution behavior prior to escalation scenarios.
```spl
index=winevent_security EventCode=4688 host="Windows11Pro"
| where Parent_Process_Name!="null"
| table _time host SubjectUserName Parent_Process_Name New_Process_Name Process_Command_Line
| sort -_time
```

Why This Matters:
- Establishes expected execution flow
- Enables comparison against abnormal chains in later simulations
- Reduces false positives when escalation detection is introduced

---

## 4. Encoded or Scripted Execution (Benign)

**Purpose:**  
Confirm visibility into scripted or encoded execution patterns
without assuming malicious intent.
```spl
index=winevent_security EventCode=4688 host="Windows11Pro"
| where like(Process_Command_Line, "%EncodedCommand%")
| table _time host SubjectUserName New_Process_Name Parent_Process_Name Process_Command_Line
| sort -_time
```

What This Confirms:
- Encoded PowerShell execution is observable
- Command-line artifacts are preserved
- Baseline context is established for later abuse detection

## 5. Supplemental Sysmon Process Creation (If Available)

**Purpose:**  
Provide additional execution context using Sysmon Process Create events.

> ⚠️ Sysmon telemetry is validated at the endpoint for this simulation.
> SIEM ingestion is treated as a lab enhancement, not a prerequisite.
```spl
index=winevent_sysmon EventCode=1 host="Windows11Pro"
| table _time host User Image ParentImage CommandLine IntegrityLevel
| sort -_time
```

What This Adds:
- High-fidelity parent/child lineage
- Integrity level confirmation
- Command-line enrichment beyond Security logs

---

## 6. Cross-Source Execution Context (Security + Sysmon)

**Purpose:**  
Correlate Windows Security and Sysmon execution telemetry when both are available.
```spl
(
  index=winevent_security EventCode=4688 host="Windows11Pro"
)
OR
(
  index=winevent_sysmon EventCode=1 host="Windows11Pro"
)
| eval actor=lower(coalesce(User, Account_Name, SubjectUserName))
| eval simulation_id="SIM-004"
| table _time host actor New_Process_Name Image Parent_Process_Name ParentImage Process_Command_Line CommandLine IntegrityLevel simulation_id
| sort -_time
```

What This Proves:
- Execution activity is observable across telemetry layers
- Process lineage is consistent
- Baseline execution context is fully understood

---

## 7. Baseline Summary (Noise Awareness)

**Purpose:**  
Summarize execution frequency to inform alert tuning in later simulations.
```spl
index=winevent_security EventCode=4688 host="Windows11Pro"
| stats count by New_Process_Name
| sort -count
```

Used For:
- Identifying common binaries
- Understanding execution noise
- Supporting detection design decisions in SIM-005

---

## ✅ Interpretation Guide

| Observation | Meaning |
|------------|---------|
| Standard user execution | Normal baseline behavior |
| Interpreter usage (cmd / powershell) | Expected execution context |
| Parent → child chains present | Execution flow understood |
| Encoded command visible | Telemetry capable of detecting abuse |
| Sysmon enrichment present | Higher-confidence execution visibility |

> This file represents the finalized **execution baseline logic** for SIM-004  
> and directly supports escalation detection introduced in **SIM-005**.
