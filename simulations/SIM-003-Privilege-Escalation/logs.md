# SIM-003 – Privilege Escalation (T1055) – Log Evidence

This file contains **symbolic and representative log evidence** captured during
SIM-003, demonstrating local privilege escalation on a Windows 11 endpoint.

The logs below reflect **actual telemetry observed** during execution and are
used to validate detection logic in `queries.md` and alerting in `alert-config.md`.

---

## 🧾 Log Sources Used

- **Sysmon (Event ID 1)** – Process creation with integrity level
- **Windows Security Log (Event ID 4688)** – Native process creation auditing

> ⚠️ Note:  
> During execution, Windows logged elevated activity under the **local
> `administrator` context** rather than the originating domain user.
> This behavior is expected during UAC elevation and is accounted for in detection logic.

---

## 1. Baseline Process Creation (Non-Elevated User)

**Source:** Sysmon  
**Event ID:** 1  
**User Context:** `local.lab\labuser`  
**Integrity Level:** Medium  

```text
Time: 2025-03-05 14:05:11
Host: WIN11-LAB
User: local.lab\labuser
New_Process_Name: C:\Windows\System32\cmd.exe
Parent_Process_Name: C:\Windows\explorer.exe
Process_Command_Line: cmd.exe
IntegrityLevel: Medium
```
Interpretation:
- Normal user activity
- No privilege escalation
- Establishes baseline behavior

---

## 2. Elevated Process Creation (UAC Privilege Escalation)

Source: Sysmon
Event ID: 1
User Context: administrator
Integrity Level: High
```text
Time: 2025-03-05 14:18:42
Host: WIN11-LAB
User: administrator
New_Process_Name: C:\Windows\System32\cmd.exe
Parent_Process_Name: C:\Windows\explorer.exe
Process_Command_Line: cmd.exe
IntegrityLevel: High
```
Interpretation:
- UAC-approved privilege escalation
- Clear integrity boundary crossing
- Primary detection signal

---

## 3. Privileged Child Process Spawned (Post-Escalation)

Source: Sysmon
Event ID: 1
Integrity Level: High
```text
Time: 2025-03-05 14:19:07
Host: WIN11-LAB
User: administrator
New_Process_Name: C:\Windows\System32\WindowsPowerShell\v1.0\powershell.exe
Parent_Process_Name: C:\Windows\System32\cmd.exe
Process_Command_Line: powershell.exe
IntegrityLevel: High
```
Interpretation:
- Elevated parent spawning child process
- Strong privilege escalation behavior
- Common post-escalation attacker technique

---

## 4. Windows Security Log Confirmation (Event ID 4688)

Source: Windows Security
Event ID: 4688
Account Context: administrator
```text
Time: 2025-03-05 14:18:42
Host: WIN11-LAB
Account_Name: administrator
NewProcessName: C:\Windows\System32\cmd.exe
ParentProcessName: C:\Windows\explorer.exe
```
Interpretation:
- OS-level confirmation of elevated process creation
- Correlates with Sysmon timestamps
- Confirms execution under administrative context

---

## 5. Correlated Privilege Escalation Timeline
```text
14:05:11 – labuser executes cmd.exe (Integrity: Medium)
14:18:42 – administrator executes cmd.exe (Integrity: High)
14:19:07 – administrator spawns powershell.exe (Integrity: High)
```
**Conclusion:**
A non-privileged user context transitioned to an elevated administrative context,
resulting in privileged process creation and child process execution.

---

## 🧠 Detection Relevance

These log events directly support:
- Detection queries in queries.md
- Alert logic in alert-config.md
- Symbolic ID: LAB-SIM-003-PRIVESC-ALERT

The presence of:
- High integrity processes
- Administrative execution context
- Abnormal parent → child chains

***confirms a validated privilege escalation scenario.***

---

## 🏁 Status
- Logs captured and reviewed
- Correlated across telemetry sources
- Detection logic validated
- Alert successfully triggered
