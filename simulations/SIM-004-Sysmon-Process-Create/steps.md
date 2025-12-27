# SIM-004 – Sysmon Process Create (T1059) – Steps

## 1. Prerequisites

Before running this simulation, confirm the following components are online and healthy:

- **Windows 11 Endpoint (10.0.0.50)**
  - Logged in as a **standard (non-admin) domain user** (e.g. `labuser`)
  - Hostname: **Windows11Pro**
  - Joined to domain: `local.lab`
  - **Sysmon installed and running**
  - Sysmon configured to log **Event ID 1 (Process Create)**
  - Splunk Universal Forwarder running
  - Local system time synchronized

- **Splunk Enterprise (Ubuntu – 10.0.0.60)**
  - Receiving Sysmon logs
  - Index for Sysmon events confirmed (e.g. `winevent_sysmon` or `winlog`)
  - Disk space not blocking searches
  - Splunk Web UI accessible

- **Windows Server (SOC Console)**
  - Used only to access Splunk Web UI

> ❌ Security Onion, Kali, and pfSense are **not required** for SIM-004.

---

### Verify Log Flow Before Proceeding

In Splunk, confirm Sysmon logs are arriving:

```spl
index=winevent_sysmon OR index=winlog EventCode=1
| stats count by index
```

***If results return counts, proceed.***

---

## 2. Baseline Command Execution (cmd.exe)

On Windows 11, while logged in as a standard user:
1. Open Command Prompt (do NOT run as administrator)
2. Run:
   ```bat
   whoami
   ```
   Expected output:  
   ```lua
   local.lab\labuser
   ```
3. Launch a new command interpreter:
   ```bat
   cmd.exe
   ```

> ⚠️ Note: Launching cmd.exe from an existing Command Prompt will not open a new window.
> This is expected and still generates a Sysmon process creation event.

***This establishes baseline execution telemetry for cmd.exe.***

---

## 3. Scripted Execution (cmd.exe → powershell.exe)

From inside the same Command Prompt session, run:
```bat
powershell.exe -Command "Get-Process | Select-Object -First 5"
```

This generates:
- Parent process: cmd.exe
- Child process: powershell.exe
- Full command-line visibility
- Medium integrity execution

***This validates parent → child process relationships.***

---

## 4. Encoded PowerShell Execution (Benign)

Still inside Command Prompt, run:
```bat
powershell.exe -EncodedCommand SQBFAFgA
```

> ⚠️ This encoded string does not execute a payload.
> It exists solely to generate realistic encoded execution telemetry.

This step produces:
- PowerShell execution with -EncodedCommand
- Observable encoded command-line artifact
- No malicious behavior

***This simulates attacker-like execution patterns without triggering alerts.***

---

## 5. Validate Sysmon Process Creation Telemetry (PRIMARY)

In Splunk, run:
```spl
index=winevent_sysmon EventCode=1 host="Windows11Pro"
| table _time host User Image ParentImage CommandLine IntegrityLevel
| sort -_time
```

Confirm the following events exist:
- `cmd.exe` executed by `labuser`
- `powershell.exe` spawned by `cmd.exe`
- Encoded PowerShell command visible in `CommandLine`
- `IntegrityLevel` remains Medium

***📸 Take screenshots:***
- `sim004-sysmon-cmd-baseline.png`
- `sim004-sysmon-parent-child.png`
- `sim004-sysmon-encoded-command.png`
  
---

## 6. Noise Review and Baseline Analysis

Run a broader baseline query:
```spl
index=winevent_sysmon EventCode=1 host="Windows11Pro"
| stats count by Image ParentImage
| sort -count
```

Observe:
- Frequency of common processes
- Relative rarity of encoded PowerShell
- Expected execution noise levels

***This step informs future alert tuning in SIM-005.***

---

## 7. Save Evidence

Add the following files to the screenshots/ folder:
- `sim004-sysmon-cmd-baseline.png`
- `sim004-sysmon-parent-child.png`
- `sim004-sysmon-encoded-command.png`
- `sim004-spl-execution-results.png`

---

## 8. Mark Simulation Completion

Update the SIM-004 checklist in README.md:
- ✅ Sysmon Event ID 1 captured
- ✅ Parent → child execution validated
- ✅ Command-line logging verified
- ✅ Encoded execution observed
- ✅ SPL queries validated
- ✅ Screenshots saved
- ⏳ Detection matrix update (after validation review)

---

## 9. Simulation Progression Note

This simulation intentionally does not configure alerts.  

Alerting and elevated execution detection are introduced in:  

➡️ SIM-005 – Privilege Escalation (T1055)  

Where execution telemetry validated here becomes the baseline for escalation detection.
