# SIM-003 – Privilege Escalation (T1055) – SPL Queries

This file documents all Splunk searches used to detect, validate, and correlate
**local privilege escalation behavior** on a Windows 11 endpoint.

The detection is based on:
- Elevated process creation
- Abnormal parent/child relationships
- High-integrity execution contexts
- Correlation between Sysmon and Windows Security logs

---

## 1. Baseline Process Creation (Non-Elevated)

**Purpose:**  
Establish normal, non-privileged process execution for comparison.

```spl
index=winevent_sysmon EventCode=1 host=WIN11*
| table _time host User New_Process_Name Parent_Process_Name Process_Command_Line IntegrityLevel
| sort -_time
```

What This Confirms:
- Normal user context (e.g. labuser)
- Standard integrity level (Medium)
- Expected parent/child relationships

---

## 2. Elevated Process Creation (Primary Detection – Sysmon)

Purpose:
Detect processes launched with High or System integrity, indicating potential privilege escalation.
```spl
index=winevent_sysmon EventCode=1 host=WIN11*
| where IntegrityLevel="High" OR IntegrityLevel="System"
| table _time host User New_Process_Name Parent_Process_Name Process_Command_Line IntegrityLevel
| sort -_time
```

What This Confirms:
- Elevated execution context
- Privilege boundary crossing
- Reliable escalation signal independent of user naming

---

## 3. Suspicious Parent → Child Process Chains

Purpose:
Identify abnormal parent/child relationships commonly observed during privilege escalation.
```spl
index=winevent_sysmon EventCode=1 host=WIN11*
| where Parent_Process_Name="*cmd.exe"
| where New_Process_Name="*powershell.exe" OR New_Process_Name="*notepad.exe"
| table _time host User Parent_Process_Name New_Process_Name Process_Command_Line IntegrityLevel
| sort -_time
```

Why This Matters:
- Administrative shells spawning child processes are high-risk
- Matches real-world attacker tradecraft
- Low false-positive rate in baseline environments

---

## 4. Windows Security Log Validation (Event ID 4688)

Purpose:
Confirm process creation at the native Windows Security auditing layer.

**⚠️ Field Mapping Note:**   
***Event ID 4688 does not reliably populate the user field.
Windows instead records the executing account under fields such as:
Account_Name and SubjectUserName.***
```spl
index=winevent_security EventCode=4688 host=WIN11*
| eval actor=lower(coalesce(Account_Name, SubjectUserName))
| table _time host actor NewProcessName ParentProcessName
| sort -_time
```
What This Confirms:
- OS-level acknowledgement of process creation
- Elevated execution context
- Timeline alignment with Sysmon events

---

## 5. Cross-Source Correlation (Sysmon + Security)

Purpose:
Correlate elevated Sysmon process creation with Windows Security confirmation.
```spl
(
  index=winevent_sysmon EventCode=1 host=WIN11*
  | where IntegrityLevel="High" OR IntegrityLevel="System"
  | eval source="sysmon"
)
OR
(
  index=winevent_security EventCode=4688 host=WIN11*
  | eval actor=lower(coalesce(Account_Name, SubjectUserName))
  | where actor="administrator"
  | eval source="security"
)
| eval simulation_id="SIM-003"
| eval symbolic_id="LAB-SIM-003-PRIVESC-ALERT"
| table _time host source actor User New_Process_Name NewProcessName Parent_Process_Name ParentProcessName IntegrityLevel simulation_id symbolic_id
| sort -_time
```

What This Proves:
- Privilege escalation occurred
- Confirmed at multiple telemetry layers
- Strong, alert-ready detection signal

---

## 6. ✅ PRIMARY ALERT QUERY (FINAL)

Purpose:
This is the exact query used to trigger the Splunk alert.
```spl
index=winevent_sysmon EventCode=1 host=WIN11*
| where IntegrityLevel="High" OR IntegrityLevel="System"
| eval simulation_id="SIM-003"
| eval symbolic_id="LAB-SIM-003-PRIVESC-ALERT"
| table _time host User New_Process_Name Parent_Process_Name Process_Command_Line IntegrityLevel simulation_id symbolic_id
| sort -_time
```

Expected Outcome:
- One or more results → alert fires
- Elevated integrity level present
- Symbolic ID populated:  ***LAB-SIM-003-PRIVESC-ALERT***

✅ Interpretation Guide   
|---------------------------|----------------------------|   
| Result                    | Meaning                    |   
|---------------------------|----------------------------|   
| High/System integrity     | Privilege escalation       |    
| Abnormal parent chain     | Suspicious behavior        |   
| Sysmon + Security match   | High-confidence detection  |   
| Alert fires               | Detection validated        |   
|---------------------------|----------------------------|   

***This file represents the finalized detection engineering logic for SIM-003 and
reflects real-world Windows logging behavior observed during execution.***
