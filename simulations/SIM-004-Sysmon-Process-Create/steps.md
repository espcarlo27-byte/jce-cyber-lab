# SIM-004 – Sysmon Process Create (T1059) – Steps

## 1. Prerequisites

Before running this simulation, confirm the following components are online and healthy:

- **Windows 11 Endpoint**
  - IP address assigned via **DHCP** (pfSense)
  - Detection relies on **hostname and process telemetry**, not IP
  - Logged in as a **standard (non-admin) domain user** (e.g. `labuser`)
  - Hostname: **Windows11Pro**
  - Joined to domain: `local.lab`
  - **Sysmon installed and running**
  - Splunk Universal Forwarder running
  - Local system time synchronized

- **Splunk Enterprise (Ubuntu – 10.0.0.60)**
  - Receiving **Windows Security logs**
  - Disk space not blocking searches
  - Splunk Web UI accessible

- **Windows Server (SOC Console)**
  - Used only to access Splunk Web UI

> ❌ Kali and Security Onion are **not required** for this simulation.  
> ℹ️ pfSense is present in the Enterprise Security Operations Environment (JCE) as the **DHCP gateway**, but is **not used for detection or correlation**.

---

### Verify Telemetry Before Proceeding

#### On Windows 11 (Sysmon)

Open **Event Viewer** and confirm:
```text
Applications and Services Logs
└─ Microsoft
└─ Windows
└─ Sysmon
└─ Operational
```

Confirm **Event ID 1 – Process Create** events are visible.

> 📸 Screenshot later: `sim004-sysmon-processcreate.png`

#### In Splunk (Security Logs)

```spl
index=winevent_security EventCode=4688
| stats count

```

***If results return counts, proceed.***

---

## 2. Baseline Process Execution (Standard User)

Log in to Windows 11 as a standard user (`labuser`).
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
   notepad.exe
   ```

***This establishes normal, non-elevated process creation.***

---

## 3. Simulated Execution Activity (Primary Detection Target)

Still logged in as `labuser`:  

Run the following commands sequentially:
```bat
powershell.exe
whoami
```

Then from PowerShell:
```powershell
Start-Process cmd.exe
Start-Process notepad.exe
```

This generates:
- Multiple process creation events
- Parent → child relationships
- Consistent telemetry in Security Event 4688
- Sysmon Event ID 1 at the endpoint

---

## 4. Validate Process Creation via Windows Security Logs (Primary)

In Splunk, run:
```spl
index=winevent_security EventCode=4688 host="Windows11Pro"
| eval actor=lower(coalesce(Account_Name, User))
| table _time host actor New_Process_Name Creator_Process_Name Process_Command_Line
| sort -_time
```

Confirm:
- cmd.exe, powershell.exe, notepad.exe
- Logical parent → child relationships
- Activity attributed to labuser

***📸 Save screenshot:***  
`sim004-security-4688.png`

---

## 5. Validate Sysmon Telemetry at Endpoint (Supplemental)

On Windows 11, open Sysmon → Operational log.  

Confirm Event ID 1 entries for:
- cmd.exe
- powershell.exe
- notepad.exe

Confirm fields:
- Image
- ParentImage
- IntegrityLevel
- CommandLine

***📸 Save screenshot:***  
`sim004-sysmon-processcreate.png`
> ℹ️ The Sysmon Process Create screenshot also demonstrates baseline execution volume,
> as multiple benign Event ID 1 entries are visible in the Operational log.

> ⚠️ Sysmon telemetry is validated at the endpoint.
> Sysmon ingestion into Splunk is not required for simulation completion.

---

## 6. Correlate Process Creation Behavior

Run the SPL correlation query from `queries.md.`  

Expected conclusion:

> “Multiple process creation events occurred consistent with command-line execution activity on a Windows endpoint.”

> ℹ️ Baseline noise analysis was performed directly at the endpoint using the
> Sysmon Operational log. Multiple benign Event ID 1 entries were observed,
> establishing normal execution volume.

> This correlation query demonstrates consistent process creation activity across Windows Security (4688) and Sysmon (Event ID 1), confirming reliable command-line execution visibility and establishing baseline execution > volume.

---

## 7. Optional Alert Validation

Alerting is **not required** for SIM-004.

This simulation focuses on establishing a reliable **execution baseline**.
Alerting is intentionally deferred to **SIM-005 – Privilege Escalation**, where
execution context is evaluated under elevated conditions.

---

## 8. Save Evidence

Add the following to the `screenshots/` directory:
- `sim004-security-4688.png`
- `sim004-sysmon-processcreate.png`
- `sim004-correlation-results.png`

---

## 9. Mark Simulation Completion

Update the SIM-004 checklist in README.md:

- ✅ Steps executed
- ✅ Windows Security process creation captured
- ✅ Sysmon Event ID 1 validated at endpoint
- ✅ Detection queries validated
- ✅ Alert triggered
- ✅ Screenshots saved
- ✅ Detection matrix updated



