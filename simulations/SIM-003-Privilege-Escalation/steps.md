# SIM-003 – Privilege Escalation (T1055) – Steps

## 1. Prerequisites

Before running this simulation, confirm the following components are online and healthy:

- **Windows 11 Endpoint (10.0.0.50)**
  - Logged in as a **standard (non-admin) domain user** (e.g. `labuser`)
  - Joined to domain: `local.lab`
  - Sysmon installed and running
  - Splunk Universal Forwarder running
  - Local system time synchronized

- **Splunk Enterprise (Ubuntu – 10.0.0.60)**
  - Receiving Windows Security logs
  - Receiving Sysmon logs
  - Disk space not blocking searches
  - Splunk Web UI accessible

- **Windows Server (SOC Console)**
  - Used only to access Splunk Web UI

> ❌ Security Onion, Kali, and pfSense are **not required** for SIM-003.

### Verify Log Flow Before Proceeding

In Splunk, confirm logs are coming in:

```spl
index=winevent_security OR index=winevent_sysmon
| stats count by index
```
***If results return counts, proceed.***

---

## 2. Baseline Process Execution (Non-Elevated User)

On Windows 11, logged in as the standard domain user:
   1. Open Command Prompt (do NOT run as administrator)
   2. Run:
      ```bat
      whoami
      ```
      Expected Output:
      ```lua
      local.lab\labuser
      ```
   3. Execute a baseline process:
      ```bat
      cmd.exe
      ```
      ***⚠️ Note: Running cmd.exe inside an existing command prompt will not open a new window.
This is expected behavior and still generates process telemetry.***

***This establishes normal, non-privileged process creation.***

---

## 3. Simulated Privilege Escalation (UAC Elevation)

While still logged in as labuser:
   1. Click Start
   2. Search for Notepad
   3. Right-click → Run as administrator
   4. Approve the UAC prompt
   5. Enter Administrator credentials

Inside the elevated Notepad session:
- Click File → Open
- Launch cmd.exe OR powershell.exe from the elevated context

This ensures:
- A new high-integrity process
- A clear privilege boundary crossing
- Reliable Security Event ID 4688

---

## 4. Generate Additional Privileged Telemetry (Forced)

Inside the elevated Command Prompt or PowerShell, run:
```powershell
Start-Process cmd.exe
Start-Process notepad.exe
```
This creates:
- Multiple privileged process creation events
- Clear parent → child relationships
- Reliable Security + Sysmon telemetry

---

## 5. Validate Sysmon Telemetry in Splunk

Run the following search:
```spl
index=winevent_sysmon EventCode=1 host=WIN11*
| table _time host User New_Process_Name Parent_Process_Name Process_Command_Line IntegrityLevel
| sort -_time
```

Confirm:
- IntegrityLevel = High or System
- Child processes such as:
   - cmd.exe
   - powershell.exe
   - notepad.exe

📸 Take screenshot: ***sim003-sysmon-processcreate.png***

---

## 6. Validate Windows Security Telemetry (Event ID 4688)

Run:
```spl
index=winevent_security EventCode=4688 host=WIN11*
| eval actor=lower(coalesce(Account_Name, SubjectUserName))
| table _time host actor NewProcessName ParentProcessName
| sort -_time
```

Confirm:
- Non-elevated events show labuser
- Elevated events show administrator
(this is expected even for domain-admin elevation)

📸 Take screenshot: ***sim003-security-4688.png***

---

## 7. Correlate Privilege Escalation Behavior

Run the correlation query from queries.md:
- Combine:
   - Sysmon Event ID 1
   - Security Event ID 4688
- Match by:
   - Host
   - Time window

Expected conclusion:
***“A privileged administrator process spawned child processes inconsistent with baseline user activity.”***

📸 Take screenshot: ***sim003-correlation-results.png***

---

## 8. Configure and Test Splunk Alert

Create the alert defined in ***alert-config.md.***

Alert settings:
- Trigger condition: Results ≥ 1
- Frequency: Every 5 minutes
- Time range: Last 15 minutes
- Severity: High
- Symbolic ID: ***LAB-SIM-003-PRIVESC-ALERT***

To force the alert:
- Repeat Step 3 (UAC elevation)

📸 Take screenshot: ***sim003-alert-fired.png***

---

## 9. Save Evidence

Add the following files to screenshots/:
- sim003-whoami-labuser.png
- sim003-nonadmin-cmd.png
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
      
