# Simulation 4 – Sysmon Process Create (T1059)
## ⚠️ Issues & Resolutions (Standardized Format)

This document captures real operational issues encountered during **SIM-004**
and the structured methodology used to identify, resolve, and validate each one.

SIM-004 focuses on **baseline execution visibility**, not alert-driven detection.

---

### ***🧩 Issue 1: No Sysmon Events Appearing in Splunk Searches***
**Description**  
During SIM-004 execution, Sysmon Process Create events (Event ID 1) were expected
to appear in Splunk searches but returned **no results**.

**Impact**
- Sysmon validation queries returned empty results
- Screenshots could not initially be captured from Splunk
- Uncertainty whether Sysmon was functioning correctly

**Root Cause**  
Sysmon was:
- Installed and running correctly on the Windows 11 endpoint
- Writing events to the **Sysmon Operational log**
- **Not ingested into Splunk** as part of the baseline lab configuration

This behavior was expected, as Sysmon ingestion was not required for SIM-004.

**Resolution**  
- Validated Sysmon functionality **directly at the endpoint** using Event Viewer
- Confirmed multiple **Event ID 1 (Process Create)** events were present
- Documented Sysmon as **supplemental endpoint validation**, not a SIEM dependency
- Relied on **Windows Security Event ID 4688** as the authoritative execution source

**Validation**  
Endpoint validation confirmed active Sysmon telemetry:
```text
Applications and Services Logs
→ Microsoft
→ Windows
→ Sysmon
→ Operational
```

**Lessons Learned**  
> Endpoint-level validation is sometimes more appropriate than forcing SIEM ingestion,
> especially when establishing execution baselines.

---

### ***🧩 Issue 2: Ambiguity Between Baseline Visibility and Alert Expectations***

**Description**  
There was initial uncertainty whether SIM-004 should:
- Generate alerts, or
- Remain a visibility-only baseline simulation

**Impact**
- Risk of forcing artificial alerting
- Potential confusion between SIM-004 and SIM-005 objectives
- Documentation scope ambiguity

**Root Cause**  
SIM-004 precedes **SIM-005 – Privilege Escalation**, which introduces alert-driven
detection. This proximity can blur simulation intent if not clearly documented.

**Resolution**
- Explicitly scoped SIM-004 as **baseline execution analysis**
- Documented alerting as **optional and informational**
- Deferred high-confidence alerting to **SIM-005**
- Updated README, steps, queries, logs, and alert-config accordingly

**Validation**  
Documentation across all SIM-004 files consistently reflects baseline intent.

**Lessons Learned**  
> Visibility must be established before detection logic and alerting can be reliable.

---

### ***🧩 Issue 3: Screenshot Redundancy for Sysmon Evidence***

**Description**  
There was uncertainty whether separate screenshots were required for:
- Sysmon Process Create validation
- Baseline execution noise analysis

**Impact**
- Potential for redundant or unnecessary evidence
- Risk of over-documentation

**Root Cause**  
Both requirements were satisfied by the **same Sysmon Operational log view**
showing multiple benign Process Create events.

**Resolution**
- Reused a single Sysmon screenshot
- Added documentation clarifying that the screenshot demonstrates:
  - Sysmon functionality
  - Baseline execution volume
- Avoided redundant screenshots

**Validation**  
The single screenshot clearly shows multiple **Event ID 1** entries.

**Lessons Learned**  
> Evidence should demonstrate intent and sufficiency, not volume for its own sake.

---

### ***🧩 Issue 4: DHCP-Based Endpoint Addressing vs Detection Context***

**Description**  
The Windows 11 endpoint was using **DHCP**, raising questions about whether
IP-based filtering should be used in detection queries.

**Impact**
- Risk of unstable or misleading detections if IPs changed
- Potential confusion during query design

**Root Cause**  
Endpoints in real environments frequently use DHCP, while infrastructure
components remain statically addressed.

**Resolution**
- Standardized all SIM-004 detection logic on **hostname-based correlation**
- Documented DHCP design rationale in the root README
- Avoided IP-based assumptions in SPL queries

**Validation**  
All queries reliably returned results using:
```spl
host="Windows11Pro"
```

**Lessons Learned**  
> Hostname-based correlation is more reliable than IP-based filtering  
> in DHCP-driven endpoint environments.

---

## 🧠 Overall Takeaways

SIM-004 reinforced core detection engineering principles:
- Visibility must precede detection
- Not all telemetry should immediately trigger alerts
- Endpoint validation is sometimes preferable to SIEM correlation
- Baseline behavior is essential for meaningful contrast
- Honest documentation of lab constraints increases credibility

Establishing a clean execution baseline significantly improves
the accuracy of downstream detections.

---

## 🏁 Status

- Issues fully documented
- Resolutions validated
- Baseline execution confirmed
- Alerting intentionally optional
- Simulation complete

> **SIM-004 is marked as:** ✅ **Validated**
