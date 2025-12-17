# SIM-003 – Privilege Escalation (T1055)
## ⚠️ Issues Encountered & Resolutions

This document captures **real issues encountered during execution of SIM-003**
and the **exact steps used to diagnose and resolve them**.

The intent is to document troubleshooting methodology, not just final success.

---

## *Issue 1: No Sysmon Events Appearing in `winevent_sysmon` Index*

### Symptoms
- SPL searches returned no results:
  ```spl
  index=winevent_sysmon EventCode=1 host="Windows11Pro"
  ```
- Sysmon appeared to be running on Windows 11
- Baseline events were expected but missing

### Root Cause

The Splunk Universal Forwarder input was configured to ingest Sysmon logs
into the `winlog` index, not `winevent_sysmon`.   

Although `inputs.conf` was updated later, historical Sysmon events remained
in the original index.   

### Evidence Used
```spl
index=winlog host="Windows11Pro"
| stats count by EventCode
```

This query confirmed:
- Sysmon Event ID 1 was present
- Events existed under index=winlog

### Resolution
Detection queries were updated to use the actual data source:
```spl
index=winlog EventCode=1 host="Windows11Pro"
```
All SIM-003 SPL queries were revised to reflect this.

### Lesson Learned
> Detection logic must align with actual ingestion paths, not assumed index names.

---

## *Issue 2: Incorrect Host Filtering (WIN11* *vs Actual Hostname)*
### Symptoms
- Searches using:
   ```spl
   host=WIN11*
   ```
returned no results

### Root Cause
The Windows 11 endpoint hostname was:
```ngix
Windows11Pro
```
not WIN11-LAB or WIN11*.

### Evidence Used
```spl
index=winlog
| stats count by host
```

### Resolution
All queries were updated to:
```spl
host="Windows11Pro"
```

### Lesson Learned
> Always validate host values using live data before writing detection logic.

---

## *Issue 3: Expected Child Processes Not Appearing for Standard User*
### Symptoms
- Running `cmd.exe` and `notepad.exe` as `labuser` did not produce expected elevated events
- Sysmon results showed activity only after elevation

### Root Cause
SIM-003 focuses on privilege escalation.   
Child process creation under a non-admin context does not produce:
- High integrity level
- Elevated parent/child chains
This was expected behavior.

### Evidence Used
```spl
index=winlog EventCode=1 host="Windows11Pro"
| table User Image IntegrityLevel
```

### Resolution
Simulation steps were clarified:
- Baseline activity = non-elevated
- Detection = elevated execution only

### Lesson Learned
> Baseline noise is essential for contrast, but detection should focus on privileged transitions.

---

## *Issue 4: User Attribution Confusion After UAC Elevation*
### Symptoms
- Searches filtering on `labuser` returned no elevated events
- Elevated processes appeared under `administrator`

### Root Cause
Windows logs UAC-approved processes under the effective security context
(`administrator`), not the originating domain user.

### Evidence Used
```spl
index=winevent_security EventCode=4688 host="Windows11Pro"
| table SubjectUserName Account_Name New_Process_Name
```

### Resolution
Queries were updated to normalize user attribution:
```spl
| eval actor=lower(coalesce(User, Account_Name, SubjectUserName))
```

Detection logic now relies on:
- IntegrityLevel
- Process lineage
- Privilege context
  
### Lesson Learned
> Username alone is unreliable; integrity level + process lineage are authoritative.

---

## *Issue 5: Accidental Overwrite of sysmonconfig.xml*
### Symptoms
- Sysmon behavior changed unexpectedly
- Expected events were missing

### Root Cause
The active `sysmonconfig.xml` was accidentally overwritten instead of using
a simulation-specific configuration file.

### Evidence Used
```bat
sysmon -c
```

### Resolution
- Restored known-good Sysmon configuration
- Revalidated Event ID 1 generation
- Re-executed simulation steps

### Lesson Learned
> Maintain simulation-specific Sysmon configs to avoid global telemetry impact.

---

## *Issue 6: Disk Space Exhaustion Blocking Splunk Searches*
### Symptoms
- Splunk UI error:
```spl
Search not executed: minimum free disk space reached
```
- Saved searches and alerts would not run
- Only basic searches occasionally worked

### Root Cause
Splunk enforces a **minimum free disk space threshold** to protect index integrity.
When available disk space drops below this threshold, Splunk **blocks searches and indexing**.

In this lab environment, disk space was exhausted due to:
- Accumulated indexed data
- Log files
- Package caches
- Temporary files

### Evidence Used (Verification Commands)
```bash
df -h
```

### Purpose:
Displays disk usage by filesystem in human-readable format.

### What it showed:
- Root (/) filesystem below Splunk’s minimum free space requirement
- Confirmed the cause of search blocking

### Resolution Steps (Cleanup Commands Used)
### *1. Identify Large Directories*
```bash
du -h /opt/splunk | sort -hr | head -20
```
### What this does:
   - Scans Splunk directories
   - Sorts by largest disk usage
   - Helps identify which paths consume the most space

### *2. Clear Linux Package Cache*
```bash
sudo apt clean
```
### What this does:
   - Removes cached .deb packages
   - Frees space without impacting installed applications
   - Safe to run in all Ubuntu systems

### *3. Remove Old / Unused Log Files*
```bash
sudo journalctl --vacuum-time=7d
```
### What this does:
   - Removes system logs older than 7 days
   - Retains recent logs for troubleshooting
   - Frees disk space safely

### *4. Remove Temporary Files*
```bash
sudo rm -rf /tmp/*
```
### What this does:
   - Deletes temporary runtime files
   - These files are recreated automatically if needed

### *5. Restart Splunk Services*
```bash
sudo /opt/splunk/bin/splunk restart
```
### What this does:
   - Forces Splunk to re-evaluate available disk space
   - Re-enables searches and alert execution once space is sufficient

### Post-Resolution Verification
```bash
df -h
```
Confirmed:
   - Free disk space above Splunk threshold
   - Searches executed successfully
   - Alerts resumed normal operation

### Lesson Learned
> Disk capacity directly affects detection availability.
> Monitoring storage health is a core SOC operational responsibility, even in lab environments.

This issue reinforced the importance of:
   - Resource monitoring
   - Log retention awareness
   - Understanding SIEM safety mechanisms
     
---

## 🧠 Overall Takeaways
SIM-003 surfaced real-world detection engineering challenges, including:
- Index mismatches
- Field normalization
- User context confusion
- Telemetry assumptions
- Resource constraints

Each issue was:
- Identified using evidence
- Proven with commands or SPL
= Resolved systematically
- Documented transparently

---

## 🏁 Status
- Issues fully documented
- Resolutions applied
- Detection logic corrected
- Alert validated and firing

> SIM-003 remains marked as ✅ Validated
> in the Detection Validation Matrix.
