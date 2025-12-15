# SIM-003 – Privilege Escalation (T1055) – Steps

## 1. Prerequisites

Before running this simulation, confirm the following components are online and healthy:

- **Windows 11 Endpoint (10.0.0.50)**
  - Logged in as a **standard (non-admin) domain user** (e.g. `labuser`)
  - Hostname: **Windows11Pro**
  - Joined to domain: `local.lab`
  - Sysmon installed and running (used as supplemental telemetry)
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

On Windows 11, log in as a standard (non-admin) user.
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
>      ⚠️ Note: Running cmd.exe inside an existing command prompt will not open a new window.   
>      This is expected behavior and still generates process telemetry.

***This establishes normal, non-privileged process creation.***

---

## 3. Simulated Privilege Escalation (UAC Elevation)

While still logged in as labuser:
   1. Click Start
   2. Search for Command Prompt
   3. Right-click → Run as administrator
   4. Approve the UAC prompt
   5. Enter Administrator credentials

Inside the elevated Command Prompt, run:
```bat
whoami
```
Expected output:
```pgsql
NT AUTHORITY\SYSTEM
OR
LOCAL.LAB\Administrator
```
Now execute:
```bat
powershell.exe
```
This creates a privileged parent → child process chain.

---

## 4. Generate Additional Privileged Activity (Forced)

Still inside the elevated PowerShell, run:
```powershell
Start-Process cmd.exe
Start-Process notepad.exe
```
This creates:
- Multiple privileged process creation events
- Clear parent → child relationships
- Reliable Security + Sysmon telemetry
- Clear escalation context
- Reproducible Security telemetry

---

## 5. Validate Privilege Escalation via Windows Security Logs (PRIMARY)

Run the following search:
```spl
index=winevent_security EventCode=4688 host="Windows11Pro"
| eval actor=lower(coalesce(Account_Name, SubjectUserName))
| table _time host actor New_Process_Name Parent_Process_Name Process_Command_Line
| sort -_time
```

Confirm:
- Actor shows an elevated account
- New processes include:
   - cmd.exe
   - powershell.exe
   - notepad.exe
- Parent/child relationships reflect escalation

📸 Take screenshot: `sim003-security-4688.png`

---

## 6. Validate Sysmon Telemetry (Supplemental – If Available)

If Sysmon telemetry is available, run:
```spl
index=winlog EventCode=1 host="Windows11Pro"
| table _time host User Image ParentImage CommandLine IntegrityLevel
| sort -_time
```

Confirm:
- IntegrityLevel = High or System
- Child processes match privileged activity

📸 Optional screenshot: `sim003-sysmon-processcreate.png`

> ⚠️ If no Sysmon events appear, proceed without this step.
> Security Event 4688 is the authoritative validation source for this simulation.

---

## 7. Correlate Privileged Activity

Run the correlation query from `queries.md`.

Expected conclusion:
> “A privileged account spawned child processes inconsistent with baseline user activity.”

📸 Take screenshot: `sim003-correlation-results.png`

---

## 8. Configure and Test Splunk Alert

Create the alert defined in `alert-config.md`.

Alert setting requirements:
- Trigger condition: Results ≥ 1
- Frequency: Every 5 minutes
- Time range: Last 15 minutes
- Severity: High
- Symbolic ID: `LAB-SIM-003-PRIVESC-ALERT`

***Re-run Steps 3–4 to force the alert if needed.***

📸 Take screenshot: `sim003-alert-fired.png`

---

## 9. Save Evidence

Add the following to the screenshots/ folder:
- `sim003-security-4688.png`
- `sim003-correlation-results.png`
- `sim003-alert-fired.png`
- `sim003-sysmon-processcreate.png (optional)`

---

## 10. Mark Simulation Completion

Update the SIM-003 checklist in README.md:
- ✅ Steps executed
- ✅ Security logs captured (4688)
- ⚠️ Sysmon telemetry (supplemental)
- ✅ Detection queries validated
- ✅ Alert triggered
- ✅ Screenshots saved
- ✅ Detection matrix updated
