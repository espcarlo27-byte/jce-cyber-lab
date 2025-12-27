# SIM-004 – Sysmon Process Create (T1059) – Log Evidence

This file contains **symbolic and representative log evidence** captured during
SIM-004, demonstrating **process execution behavior** on a Windows 11 endpoint
(**Windows11Pro**).

The logs below reflect **actual telemetry observed during execution** and are
used to validate:
- Execution visibility queries in `queries.md`
- Baseline context used for later escalation detection

Windows Security Event ID **4688** is treated as the **primary authoritative source**.
Sysmon telemetry is included as **supplemental endpoint-level enrichment** when available.

---

## 🧾 Log Sources Used

- **Windows Security Log (Event ID 4688)** – Primary process creation auditing
- **Sysmon (Event ID 1)** – Supplemental process creation, lineage, and integrity level

> ⚠️ **Important Scope Note**  
> This simulation intentionally validates **non-elevated execution behavior**.
> No privilege escalation occurs in SIM-004. Elevated execution is introduced
> and detected in **SIM-005 – Privilege Escalation**.

---

## 🔄 Field Normalization Notes

The following field mappings were confirmed as reliable in this lab environment:

### Windows Security (Event ID 4688)
- `Account_Name` / `SubjectUserName` → normalized as **actor**
- `New_Process_Name`
- `Parent_Process_Name`
- `Process_Command_Line`

### Sysmon (Event ID 1 – Supplemental)
- `Image`
- `ParentImage`
- `CommandLine`
- `IntegrityLevel`

Legacy or inconsistent aliases were excluded from baseline analysis.

---

## 1. Baseline Process Creation (Standard User)

**Source:** Windows Security  
**Event ID:** 4688  
**User Context:** `local.lab\labuser`

```text
Time: 2025-03-06 13:02:14
Host: Windows11Pro
Account_Name: local.lab\labuser
New_Process_Name: C:\Windows\System32\cmd.exe
Parent_Process_Name: C:\Windows\explorer.exe
Process_Command_Line: cmd.exe
```

Interpretation:
- Standard user execution
- Expected parent → child relationship
- Establishes baseline execution behavior

---

## 2. Scripted Execution (cmd → powershell)

**Source:** Windows Security  
**Event ID:** 4688  
**User Context:** `local.lab\labuser`  
```text
Time: 2025-03-06 13:04:21
Host: Windows11Pro
Account_Name: local.lab\labuser
New_Process_Name: C:\Windows\System32\WindowsPowerShell\v1.0\powershell.exe
Parent_Process_Name: C:\Windows\System32\cmd.exe
Process_Command_Line: powershell.exe -Command "Get-Process | Select-Object -First 5"
```

Interpretation:
- Command interpreter chaining visible
- Command-line arguments captured
- Execution remains non-elevated

---

## 3. Encoded PowerShell Execution (Benign)

**Source:** Windows Security  
**Event ID:** 4688  
**User Context:** `local.lab\labuser`
```text
Time: 2025-03-06 13:06:02
Host: Windows11Pro
Account_Name: local.lab\labuser
New_Process_Name: C:\Windows\System32\WindowsPowerShell\v1.0\powershell.exe
Parent_Process_Name: C:\Windows\System32\cmd.exe
Process_Command_Line: powershell.exe -EncodedCommand SQBFAFgA
```

Interpretation:
- Encoded execution observable
- No malicious payload executed
- Confirms telemetry readiness for abuse detection

---

## 4. Supplemental Sysmon Process Creation (Endpoint Validation)

**Source:** Sysmon  
**Event ID:** 1  
**Log:** Microsoft-Windows-Sysmon/Operational
```text
Time: 2025-03-06 13:06:02
Host: Windows11Pro
User: local.lab\labuser
Image: C:\Windows\System32\WindowsPowerShell\v1.0\powershell.exe
ParentImage: C:\Windows\System32\cmd.exe
CommandLine: powershell.exe -EncodedCommand SQBFAFgA
IntegrityLevel: Medium
```

Interpretation:
- Sysmon captures detailed process lineage
- Integrity level confirms non-elevated execution
- Used as enrichment, not dependency

> ⚠️ Sysmon telemetry is validated at the endpoint.
> Absence of Sysmon ingestion in Splunk does not invalidate this simulation.

---

## 5. Correlated Execution Timeline
```text
13:02:14 – labuser executes cmd.exe (Security 4688 – baseline)
13:04:21 – labuser spawns powershell.exe (Security 4688 – scripted execution)
13:06:02 – labuser executes encoded PowerShell (Security 4688 – benign encoded execution)
```

Conclusion:  
A standard user executed multiple command interpreters with observable
parent → child relationships and command-line context, establishing a
clear and reliable execution baseline.

---

## 🧠 Detection Relevance

These log events directly support:
- Baseline execution queries in queries.md
- Execution context required for escalation detection
- Noise-aware alert tuning in later simulations

The presence of:
- Standard user execution context
- Interpreter chaining
= Encoded command visibility
- Consistent lineage across telemetry sources

confirms a validated execution baseline.

---

## 🏁 Status

- [x] Logs captured and reviewed
- [x] Baseline execution confirmed
- [x] Parent → child relationships validated
- [x] Sysmon enrichment validated at endpoint
- [x] Simulation complete

> This log evidence establishes the execution context
> required for SIM-005 – Privilege Escalation (T1055).
