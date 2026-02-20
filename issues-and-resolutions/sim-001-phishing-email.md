# SIM-001 – Issues & Resolutions  
## Phishing Email (T1566.002)

This document records technical challenges encountered during SIM-001
and the corrective actions taken.

SIM-001 is designed as a **multi-layer detection simulation**.

Detection authority is established through correlated evidence across:

- Identity telemetry (Active Directory authentication events)
- Endpoint telemetry (Windows Security Event ID 4688 / Sysmon)
- Network telemetry (Outbound HTTP confirmation)
- SIEM-based correlation logic

---

## Issue 1 – Event ID 4688 Not Appearing in Splunk

### Problem
Windows Event Viewer displayed Event ID 4688 locally,
but Splunk searches returned no results.

### Root Cause
The required `winevent_security` index did not exist in Splunk.

### Resolution
Created the missing index:  
`Settings → Indexes → New Index → winevent_security`

Validated ingestion:
```spl
| tstats count where index=winevent_security by host
```

### Verification

Event ID 4688 successfully appeared in Splunk.

📎 **Evidence Reference:**  
`sim001-A-evidence-002-index-validation.png`

🎓 **Lessons Learned:**  
SIEM ingestion failures often originate from index misconfiguration rather than log generation issues. Always validate index existence before troubleshooting forwarders.

### Overall Takeaway

Log generation does not equal log ingestion. SIEM configuration must align with forwarder configuration.

### Status

Resolved

---

## Issue 2 – Incorrect Assumption: URL Must Appear in Process_Command_Line

### Problem

Detection logic initially expected the phishing URL to appear in:
```nginx
Process_Command_Line
```

No URL was present in Event ID 4688.

### Root Cause

Phishing link was opened within an existing browser session via webmail.  
Modern browsers do not expose navigation URLs in process creation logs.

### Resolution

Removed dependency on:
```ini
`Process_Command_Line="*http*"
```

Updated detection model to rely on:

- Browser execution confirmation  
- Outbound network confirmation  
- Identity attribution  

### Verification

Correlation queries returned valid detection results without URL string matching.

📎 **Evidence Reference:**  
`sim001-A-evidence-008-4688-browser.png`

🎓 **Lessons Learned:**  
Command-line string matching is unreliable for browser-based phishing detection. Detection must account for real-world browser process behavior and rely on correlated telemetry layers.

### Overall Takeaway

Detection design must reflect system behavior, not assumptions.

### Status

Resolved (Detection Model Improved)

---

## Issue 3 – Apache Not Logging Click Events

### Problem

Phishing redirect worked, but no entries appeared in `phish_log.txt`.

### Root Cause

Improper file ownership and permissions prevented Apache from writing to the log file.

### Resolution

Set correct ownership and permissions:
```bash
sudo chown www-data:www-data /var/www/html/phish_log.txt
sudo chmod 664 /var/www/html/phish_log.txt
```

Restarted Apache service.

### Verification

Log entries appeared after clicking the phishing link.

📎 **Evidence Reference:**  
`sim001-A-evidence-009-apache-access-log.png`

🎓 **Lessons Learned:**  
Web server telemetry depends on correct file permissions. Infrastructure misconfiguration can silently break detection validation workflows.

### Overall Takeaway

Detection simulations require secure and properly configured supporting infrastructure.

### Status

Resolved

---

## Issue 4 – Network Telemetry Initially Treated as Optional

### Problem

Original documentation framed network telemetry as supplemental,  
with endpoint telemetry treated as authoritative.

### Root Cause

Detection architecture was initially endpoint-centric.

### Resolution

Revised detection philosophy to require:

- Endpoint execution confirmation  
- Network confirmation (Apache or firewall logs)  
- Identity context  

Network telemetry now serves as authoritative click validation.

### Verification

Correlation query confirmed endpoint + network timestamp alignment.

📎 **Evidence Reference:**  
`sim001-A-evidence-009-apache-access-log.png`

🎓 **Lessons Learned:**  
Single-event detection models are brittle. Reliable phishing detection requires layered evidence across identity, endpoint, and network sources.

### Overall Takeaway

Layered correlation strengthens detection reliability.

### Status

Resolved (Architecture Upgraded)

---

## Issue 5 – Time Window Correlation Drift

### Problem

Events appeared individually valid but correlation queries were inconsistent.

### Root Cause

Correlation window was too narrow and minor clock drift existed between systems.

### Resolution

Standardized correlation window to 15 minutes.  
Verified system time synchronization across Windows, Kali, and Splunk.

### Verification

Correlation queries consistently returned aligned results.

📎 **Evidence Reference:**  
`sim001-A-evidence-010-correlation-results.png`

🎓 **Lessons Learned:**  
Time synchronization is critical in multi-source detection environments. Small clock differences can break correlation logic.

### Overall Takeaway

Correlation window design must consider system timing realities.

### Status

Resolved

---

## Issue 6 – Alert Not Triggering

### Problem

Manual searches returned results, but the alert did not trigger.

### Root Cause

Alert schedule and time range misalignment with correlation window.

### Resolution

Configured alert with:

- Schedule: Every 5 minutes  
- Time Range: Last 15 minutes  
- Trigger: Results > 0  
- Throttle: 10 minutes  

### Verification

Alert successfully triggered during phishing simulation.

📎 **Evidence Reference:**  
`sim001-A-evidence-011-alert-fired.png`

🎓 **Lessons Learned:**  
Alert configuration must align with detection time windows. Operational tuning is as important as detection logic.

### Overall Takeaway

Detection engineering includes both query design and operational configuration.

### Status

Resolved

---

## 🔎 Evidence Summary

Key evidence artifacts generated during SIM-001:

- AD Logon Event (4624)  
- Browser Execution Event (4688)  
- Apache Access Log Entry  
- `phish_log.txt` Entry  
- Correlation Query Results  
- Alert Trigger Confirmation  

All evidence stored in:
```bash
SIM-001/screenshots/
```

---

## 🏁 Final Architectural Takeaway

SIM-001 evolved from an endpoint-driven model  
into a multi-layer correlation framework.  

Final detection authority:  

`Identity → Endpoint Execution → Network Confirmation → SIEM Correlation`  

This approach reflects enterprise SOC methodology  
and avoids brittle single-field matching.

---

## Status

SIM-001 – COMPLETE & VALIDATED



