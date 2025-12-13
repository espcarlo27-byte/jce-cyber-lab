# SIM-003 – Privilege Escalation (T1055) – Steps

## 1. Prerequisites

Before running this simulation, confirm the following components are online and healthy:

- **Windows 11 Endpoint (10.0.0.50)**
  - Logged in as a **standard (non-admin) user**
  - Sysmon installed and running
  - Splunk Universal Forwarder running
  - Local time synchronized

- **Splunk Enterprise (Ubuntu – 10.0.0.60)**
  - Receiving Windows Event Logs
  - Receiving Sysmon logs
  - Splunk Web UI accessible

- **Windows Server (SOC Console)**
  - Used only to access Splunk Web UI

> ❌ Security Onion, Kali, and pfSense are **not required** for SIM-003.

Before proceeding, confirm Splunk is receiving events:

```spl
index=winevent_sysmon OR index=winevent_security
| stats count by index
```

---

## 2. Baseline Process Execution (Non-Elevated)

- On Windows 11, log in as a standard user.
- Open Command Prompt (non-admin) and run:
  ```bat
  whoami
  ```
- Expected Output:
  ```php-template
  <HOSTNAME>\<standard_user>
  ```
- Now Run:
  ```bat
  cmd.exe
  ```
**This establishes baseline process creation under a non-privileged context.**

***This baseline helps differentiate normal execution from elevated execution later.***

---

## 3. Simulated Privilege Escalation (Safe Method)

On Windows 11, perform the following:
   1. Click Start
   2. Search for Command Prompt
   3. Right-click → Run as administrator
   4. Approve the UAC prompt

**Inside the elevated Command Prompt, run:**
```bat
whoami
```
Expected Output:
```perl
nt authority\system
OR
<HOSTNAME>\Administrator
```
Now execute a child process:
```bat
powershell.exe
```
This creates:
- A high-integrity parent process
- A privileged child process
- Clear privilege context change for detection

---

## 4. Generate Additional Privileged Telemetry (Forced)

Still inside the elevated PowerShell, run:
```powershell
Start-Process cmd.exe
Start-Process notepad.exe
```
These commands generate:
- Multiple privileged process creation events
- Clear parent/child relationships
- Reproducible Sysmon + Security logs

---

## 5. Validate Sysmon Telemetry in Splunk

From Splunk Web, run:
```spl
index=winevent_sysmon EventCode=1 host=WIN11*
| table _time host User New_Process_Name Parent_Process_Name Process_Command_Line IntegrityLevel
| sort -_time
```
Look for:
- **IntegrityLevel** = High or System
- Parent/child relationships involving:
    - cmd.exe
    - powershell.exe
    - notepad.exe

***Take a screenshot when results are visible.***

---

## 6. Validate Windows Security Telemetry in Splunk

Run:
```spl
index=winevent_security EventCode=4688 host=WIN11*
| table _time host SubjectUserName NewProcessName ParentProcessName
| sort -_time
```
Confirm:
- Elevated user context
- Matching timestamps with Sysmon events

***Take a screenshot when results are visible.***

---

## 7. Correlate Privilege Escalation Behavior

Run the correlation query from queries.md:
- Combine Sysmon process creation
- With Security 4688 events
- Based on host + time window

Expected conclusion:
**“A privileged process spawned child processes inconsistent with baseline user activity.”**

***Take a screenshot of the correlated results.***

---

## 8. Configure and Test Splunk Alert

Create the alert defined in **alert-config.md.**

Alert requirements:
- Trigger condition: Results ≥ 1
- Frequency: Every 5 minutes
- Time range: Last 15 minutes
- Severity: High
- Symbolic ID:
   **LAB-SIM-003-PRIVESC-ALERT**

Force the alert by repeating Step 3 if needed.
***Capture a screenshot of the alert firing.***

---

## 9. Save Evidence

Add the following to the screenshots/ folder:
- sim003-elevated-cmd.png
- sim003-sysmon-processcreate.png
- sim003-security-4688.png
- sim003-correlation-results.png
- sim003-alert-fired.png

---

## 10. Mark Simulation Completion

Update the SIM-003 checklist in README.md:
- ✅ Steps executed
- ✅ Sysmon telemetry captured
- ✅ Security logs captured
- ✅ Detection queries validated
- ✅ Alert triggered
- ✅ Screenshots saved
- ✅ Detection matrix updated
