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
What This Confirms
- Normal user context
- Standard integrity level
- Expected parent/child relationships

---

## 2. Elevated Process Creation (Primary Detection)

Purpose:
Detect processes launched with High or System integrity, indicating potential privilege escalation.
```spl
index=winevent_sysmon EventCode=1 host=WIN11*
(IntegrityLevel="High" OR IntegrityLevel="System")
| table _time host User New_Process_Name Parent_Process_Name Process_Command_Line IntegrityLevel
| sort -_time
```
What This Confirms
- Elevated execution context
- Privilege boundary crossing
- Clear escalation signal

---

## 3. Suspicious Parent → Child Process Chains

Purpose:
Identify abnormal parent/child relationships commonly seen during escalation.
```spl
index=winevent_sysmon EventCode=1 host=WIN11*
Parent_Process_Name="*cmd.exe"
(New_Process_Name="*powershell.exe" OR New_Process_Name="*notepad.exe")
| table _time host User Parent_Process_Name New_Process_Name Process_Command_Line IntegrityLevel
| sort -_time
```
Why This Matters:
- Admin shell spawning children is high-risk
- Matches real attacker tradecraft
- Low false-positive rate

---

## 4. Windows Security Log Validation (Event ID 4688)

Purpose:
Confirm privileged execution at the Windows Security auditing layer.
```spl
index=winevent_security EventCode=4688 host=WIN11*
| table _time host SubjectUserName NewProcessName ParentProcessName
| sort -_time
```
What This Confirms
- OS-level acknowledgement of process creation
- Elevated subject context
- Timeline correlation with Sysmon

---

## 5. Cross-Source Correlation (Sysmon + Security)

Purpose:
Correlate Sysmon process creation with Security log confirmation.
```spl
(
  index=winevent_sysmon EventCode=1 host=WIN11*
  (IntegrityLevel="High" OR IntegrityLevel="System")
)
OR
(
  index=winevent_security EventCode=4688 host=WIN11*
)
| eval simulation_id="SIM-003"
| eval symbolic_id="LAB-SIM-003-PRIVESC-ALERT"
| table _time host User SubjectUserName New_Process_Name NewProcessName Parent_Process_Name ParentProcessName IntegrityLevel simulation_id symbolic_id
| sort -_time
```
What This Proves
- Privilege escalation occurred
- Confirmed at multiple telemetry layers
- Strong alert-ready signal

---

## 6. ✅ PRIMARY ALERT QUERY (FINAL)

Purpose:
This is the exact query used to trigger the Splunk alert.
```spl
index=winevent_sysmon EventCode=1 host=WIN11*
(IntegrityLevel="High" OR IntegrityLevel="System")
| eval simulation_id="SIM-003"
| eval symbolic_id="LAB-SIM-003-PRIVESC-ALERT"
| table _time host User New_Process_Name Parent_Process_Name Process_Command_Line IntegrityLevel simulation_id symbolic_id
| sort -_time
```
Expected Outcome:
- One or more results = Alert should fire
- Fields populated consistently
- Symbolic ID present:
  **LAB-SIM-003-PRIVESC-ALERT**

---

✅ Interpretation Guide
```text
|---------------------------|----------------------|
|  Result                   | 	Meaning            |
|---------------------------|----------------------|
| High/System integrity	    | Privilege escalation |
| Abnormal parent chain     | Suspicious behavior  |
| Sysmon + Security match	| High confidence      |
| Alert fires	            | Detection validated  |
|---------------------------|----------------------|
```
This file represents the core detection engineering artifact for SIM-003.
