# SIM-003 – Privilege Escalation (T1055)

## 🎯 Simulation Overview

This simulation demonstrates **local privilege escalation detection** on a Windows 11 endpoint by observing
process creation under elevated (Administrator or SYSTEM) context.

The objective is to validate that:
- A standard user can elevate privileges via UAC
- Windows Security logs record the escalation accurately
- Splunk detects and alerts on the privileged process creation
- The activity is documented with reproducible evidence

This simulation aligns with **enterprise SOC detection workflows**, where Windows Security logs
(Event ID 4688) serve as the primary authoritative telemetry source.

---

## 🧩 MITRE ATT&CK Mapping

- **Technique:** T1055 – Privilege Escalation  
- **Tactic:** Privilege Escalation (TA0004)

---

## 🏗 Lab Components Used

- **Windows 11 Endpoint**
  - Hostname: **Windows11Pro**
  - Standard domain user (`labuser`)
  - UAC-enabled privilege escalation
  - Windows Security auditing enabled
  - Sysmon installed (supplemental telemetry)

- **Splunk Enterprise (Ubuntu – 10.0.0.60)**
  - Primary SIEM
  - Ingests Windows Security logs
  - Generates alerts

- **Windows Server (SOC Console)**
  - Used to access Splunk Web UI

> ❌ Security Onion, Kali, and pfSense are **not required** for SIM-003.

---

## 📂 Files in This Simulation

| File | Purpose |
|----|----|
| `steps.md` | Step-by-step execution of the privilege escalation |
| `queries.md` | SPL detection and correlation queries |
| `alert-config.md` | Splunk alert definition |
| `logs.md` | Symbolic and representative log evidence |
| `screenshots/` | Visual proof of detection and alerting |

---

## 🔍 Detection Strategy

### Primary Detection
- **Windows Security Event ID 4688**
- Detects process creation under elevated account context
- Reliable across Windows environments
- Independent of Sysmon availability

### Supplemental Enrichment
- **Sysmon Event ID 1** (if available)
- Provides integrity level and detailed parent/child relationships
- Used for additional context, not dependency

---

## 📸 Evidence Collected

### Required Screenshots
- **sim003-security-4688.png** – Privileged process creation
- **sim003-correlation-results.png** – Correlated escalation activity
- **sim003-alert-config.png** – Alert configuration
- **sim003-alert-fired.png** – Alert successfully triggered

### Optional Screenshot
- **sim003-sysmon-processcreate.png** – Supplemental Sysmon telemetry

All screenshots are stored in:  'simulations/SIM-003-Privilege-Escalation/screenshots/'

---

## 🚨 Alert Details

- **Alert Name:** LAB-SIM-003-PRIVESC-ALERT
- **Severity:** High
- **Trigger Condition:** ≥ 1 privileged process creation event
- **Schedule:** Every 5 minutes (last 15 minutes)
- **Telemetry Source:** Windows Security (Event ID 4688)

---

## 🧠 Analyst Takeaways

This simulation demonstrates:
- Detection of privilege escalation without reliance on Sysmon
- Proper handling of UAC context switching
- Field normalization for reliable user attribution
- Alerting based on behavior, not assumptions
- Troubleshooting ingestion vs detection-layer issues

These are **real-world SOC analyst skills**, not lab-only techniques.

---

## ✅ Simulation Status

- [x] Steps executed successfully
- [x] Windows Security logs captured (4688)
- [x] Detection queries validated
- [x] Alert triggered and verified
- [x] Evidence screenshots collected
- [x] Logs documented
- [x] Detection matrix updated

---

## 🏁 Final Status

**SIM-003 is complete, validated, and portfolio-ready.**

This simulation accurately reflects how privilege escalation is detected and investigated
in enterprise SOC environments.

