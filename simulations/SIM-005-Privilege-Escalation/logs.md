# SIM-005 – Privilege Escalation (T1055) – Log Evidence

This file contains **symbolic and representative log evidence** captured during
SIM-003, demonstrating **local privilege escalation** on a Windows 11 endpoint
(**Windows11Pro**).

The logs below reflect **actual telemetry observed during execution** and are
used to validate:
- Detection logic in `queries.md`
- Alerting logic in `alert-config.md`

Windows Security Event ID **4688** is treated as the **primary authoritative source**.
Sysmon telemetry is included as **supplemental enrichment when available**.

---

## 🧾 Log Sources Used

- **Windows Security Log (Event ID 4688)** – Primary process creation auditing
- **Sysmon (Event ID 1)** – Supplemental process creation + integrity level

> ⚠️ **Important Behavior Note**  
> During UAC elevation, Windows logs the process under the **effective
> Administrator or SYSTEM context**, not the originating standard user.
> This behavior is expected and is explicitly accounted for in detection logic.

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

Legacy or inconsistent aliases were excluded from final detections.

---

## 1. Baseline Process Creation (Non-Elevated User)

**Source:** Windows Security  
**Event ID:** 4688  
**User Context:** `local.lab\labuser`

```text
Time: 2025-03-05 14:05:11
Host: Windows11Pro
Account_Name: local.lab\labuser
New_Process_Name: C:\Windows\System32\cmd.exe
```
Interpretation:
- Normal standard-user execution
- No elevation present
- Establishes baseline behavior

---

## 2. Elevated Process Creation (UAC Privilege Escalation)

Source: Windows Security
Event ID: 4688
Account Context: administrator
```text
Time: 2025-03-05 14:18:42
Host: Windows11Pro
Account_Name: administrator
New_Process_Name: C:\Windows\System32\cmd.exe
Parent_Process_Name: C:\Windows\explorer.exe
Process_Command_Line: cmd.exe
```

Interpretation:
- UAC-approved privilege escalation
- Transition from standard user → administrator
- Primary detection signal

---

## 3. Privileged Child Process Spawned (Post-Escalation)

Source: Windows Security
Event ID: 4688
Account Context: administrator
```text
Time: 2025-03-05 14:19:07
Host: Windows11Pro
Account_Name: administrator
New_Process_Name: C:\Windows\System32\WindowsPowerShell\v1.0\powershell.exe
Parent_Process_Name: C:\Windows\System32\cmd.exe
Process_Command_Line: powershell.exe
```

Interpretation:
- Elevated parent spawning a child process
- High-risk behavior consistent with attacker tradecraft
- Confirms escalation persistence beyond initial elevation

---

## 4. Supplemental Sysmon Process Creation (If Available)

Source: Sysmon
Event ID: 1
Index: winlog
```text
Time: 2025-03-05 14:19:07
Host: Windows11Pro
User: administrator
Image: C:\Windows\System32\WindowsPowerShell\v1.0\powershell.exe
ParentImage: C:\Windows\System32\cmd.exe
CommandLine: powershell.exe
IntegrityLevel: High
```

Interpretation:
- Confirms elevated integrity level
- Reinforces Security log findings
- Used as enrichment, not dependency

> ⚠️ Sysmon telemetry is supplemental.
> Absence of Sysmon events does not invalidate the detection.

---

## 5. Correlated Privilege Escalation Timeline
```text
14:05:11 – labuser executes cmd.exe (Security 4688 – baseline)
14:18:42 – administrator executes cmd.exe (Security 4688 – elevation)
14:19:07 – administrator spawns powershell.exe (Security 4688 – post-escalation)
```

Conclusion:  
A standard user context transitioned to an elevated administrative context,
resulting in privileged process creation and abnormal parent → child execution.

---

## 🧠 Detection Relevance

These log events directly support:
- Detection queries in queries.md
- Alert logic in alert-config.md
- Symbolic ID: LAB-SIM-003-PRIVESC-ALERT

The presence of:
- Administrative execution context
- Abnormal process lineage
- Privileged process persistence

confirms a validated privilege escalation scenario.

---

## 🏁 Status
- [x] Logs captured and reviewed
- [x] Correlated across telemetry sources
- [x] Detection logic validated
- [x] Alert successfully triggered
- [x] Simulation complete
Parent_Process_Name: C:\Windows\explorer.exe
Process_Command_Line: cmd.exe
