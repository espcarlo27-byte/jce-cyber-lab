# SIM-003 – Privilege Escalation (T1055)
## ⚠️ Issues & Resolutions (Standardized Format)
This document captures real operational issues encountered during SIM-003 and the structured methodology used to identify, resolve, and validate each one.

---

### ***🧩 Issue 1: No Sysmon Events Appearing in winevent_sysmon Index***
**Description**  
During SIM-003 execution, Sysmon Process Creation events (Event ID 1) were expected to appear in the winevent_sysmon index but returned no results in Splunk searches.

**Impact**
- Detection queries failed to return results
- Screenshots could not be captured for Sysmon validation steps
- Alert logic appeared broken despite Sysmon running correctly
This blocked validation of privilege escalation telemetry.

**Root Cause**  
The Splunk Universal Forwarder was ingesting Sysmon logs into the winlog index, not winevent_sysmon.
Although inputs.conf was later modified, historical Sysmon data remained in the original index, causing queries to fail.

**Resolution**  
All SIM-003 SPL queries were updated to reference the actual ingestion index:
```spl
index=winlog EventCode=1 host="Windows11Pro"
```
Detection logic was aligned with observed ingestion behavior, not assumed index names.

**Validation**  
The following query confirmed Sysmon Event ID 1 data was present:
```spl
index=winlog host="Windows11Pro"
| stats count by EventCode
```
Results confirmed:
- Sysmon Event ID 1 existed
- Data ingestion was functioning correctly

**Lessons Learned**  
> Detection engineering must always align with real ingestion paths, not expected or planned index names.

---

### ***🧩 Issue 2: Incorrect Host Filtering (WIN11* vs Actual Hostname)***
**Description**  
Initial SPL searches used wildcard host filters (host=WIN11*) but returned no events.

**Impact** 
- All detection and validation queries failed
- Misleading indication that logs were missing
- Delayed troubleshooting and validation

**Root Cause**  
The actual hostname of the Windows 11 endpoint was:
```text
Windows11Pro
```
The wildcard pattern `WIN11*` did not match the host value used by Splunk.

**Resolution**  
All SIM-003 queries were updated to explicitly reference the correct hostname:
```spl
host="Windows11Pro"
```

**Validation**  
The following query verified the correct hostname value:
```spl
index=winlog
| stats count by host
```
Events were successfully returned for Windows11Pro.

**Lessons Learned**  
> Hostnames must always be validated from live data before finalizing detection logic.

---

### ***🧩 Issue 3: Expected Child Processes Not Appearing for Standard User***
**Description**  
Executing ***cmd.exe*** and ***notepad.exe*** as the standard user (labuser) did not produce elevated process events.

**Impact**
- Confusion during Sysmon validation steps
- Concern that Sysmon was misconfigured
- Delayed confirmation of expected behavior

**Root Cause**  
SIM-003 specifically detects privilege escalation, not standard process execution.
Processes launched under a non-admin context:
- Run at Medium integrity
- Do not generate elevated parent/child chains
- Should not trigger escalation detections

**Resolution**
Simulation steps were clarified to explicitly distinguish:
- Baseline activity (non-elevated)
- Detection activity (post-UAC elevation only)
Detection logic remained focused on High/System integrity transitions.

**Validation**  
Baseline Sysmon events were confirmed:
```spl
index=winlog EventCode=1 host="Windows11Pro"
| table User Image IntegrityLevel
```
Only elevated executions produced detection-relevant telemetry.

**Lessons Learned**  
> Baseline noise is essential for contrast, but detections must focus on privileged state transitions.

---

### ***🧩 Issue 4: User Attribution Confusion After UAC Elevation***
**Description**  
Filtering searches on labuser returned no elevated process events after UAC approval.

**Impact**
- Elevated activity appeared “missing”
- Confusion over user attribution
- Risk of incorrect detection assumptions

**Root Cause**  
Windows logs UAC-approved processes under the effective security context (administrator), not the originating domain user.
This is standard Windows security behavior.

**Resolution**  
Detection logic was updated to normalize user attribution using multiple fields:
```spl
| eval actor=lower(coalesce(User, Account_Name, SubjectUserName))
```
Detections now rely primarily on:
- IntegrityLevel
- Process lineage
- Privileged execution context

**Validation**  
Security Event ID 4688 confirmed elevated execution:
```spl
index=winevent_security EventCode=4688 host="Windows11Pro"
| table SubjectUserName Account_Name New_Process_Name
```
**Lessons Learned**
> Username alone is unreliable — integrity level and process lineage are authoritative.

---

### ***🧩 Issue 5: Accidental Overwrite of sysmonconfig.xml***
**Description**  
Sysmon behavior changed unexpectedly after modifying configuration files.

**Impact**
- Missing or altered Sysmon telemetry
- Required revalidation of event generation
- Risk of affecting other simulations

**Root Cause**  
The active sysmonconfig.xml was accidentally overwritten instead of using a simulation-specific configuration file.

**Resolution**
- Restored a known-good Sysmon configuration
- Verified active configuration with:
```bat
sysmon -c
```
- Re-executed SIM-003 steps to regenerate telemetry

**Validation**  
Sysmon Event ID 1 resumed normal generation and appeared in Splunk.

**Lessons Learned**
> Always maintain simulation-specific Sysmon configs to avoid global telemetry impact.

---

### ***🧩 Issue 6: Disk Space Exhaustion Blocking Splunk Searches***
**Description**  
Splunk searches failed with the error:
```text
Search not executed: minimum free disk space reached
```

**Impact**
- Searches and alerts were blocked
- Detection validation stalled
- Only limited basic searches executed

**Root Cause**  
Splunk enforces a minimum free disk space threshold to protect index integrity.
Disk exhaustion was caused by:
- Accumulated indexed data
- Log files
- Package caches
- Temporary system files

**Resolution**  
Disk space was reclaimed using the following commands:  
```bash
df -h
```
> Identified low disk space

```bash
du -h /opt/splunk | sort -hr | head -20
```
> Located largest directories

```bash
sudo apt clean
```
> Cleared package cache

```bash
sudo journalctl --vacuum-time=7d
```
> Removed old system logs

```bash
sudo rm -rf /tmp/*
```
> Deleted temporary files

```bash
sudo /opt/splunk/bin/splunk restart
```
> Re-enabled search execution

**Validation**
```bash
df -h
```
Confirmed:
- Free disk space above Splunk threshold
- Searches and alerts resumed normally

**Lessons Learned**
> SIEM availability depends on resource health.
> Disk monitoring is a core SOC operational responsibility.

---

## 🧠 Overall Takeaways
SIM-003 reinforced real-world detection engineering principles:
- Always validate ingestion paths
- Normalize fields defensively
- Separate baseline behavior from detection signals
- Expect OS security behaviors (UAC context switching)
- Monitor SIEM resource health continuously

---

## 🏁 Status
- Issues fully documented
- Resolutions validated
- Detection logic corrected
- Alert firing successfully

> SIM-003 remains marked as ✅ Validated
