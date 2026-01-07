# SIM-004 – Sysmon Process Create (T1059)

## 🎯 Goal

Simulate and detect **process execution activity** on a Windows 11 endpoint using
**Sysmon Process Create (Event ID 1)** telemetry to establish a **reliable execution baseline**
and validate detection logic for suspicious or abnormal command execution.

This simulation focuses on **execution visibility**, not privilege escalation.

Specifically, this simulation validates that:
- Normal user process execution is captured by Sysmon
- Command-line arguments are logged
- Parent → child process relationships are visible
- Sysmon telemetry is indexed and searchable in Splunk
- Baseline execution behavior can be reliably distinguished from higher-risk activity

This simulation validates the **Execution** row in the  
[Detection Validation Matrix](../../detection-matrix/detection-validation-matrix.md).

---

## 🧩 MITRE ATT&CK Mapping

- **Technique:** T1059 – Command and Scripting Interpreter  
- **Tactic:** Execution (TA0002)

---

## 🏗 Lab Components Used

| Component | Role |
|---------|-----|
| **Windows 11 Endpoint** (`Windows11Pro`) | Victim host |
| **Standard User** (`local.lab\labuser`) | Baseline execution |
| **Sysmon** | Process creation telemetry |
| **Splunk Enterprise (Ubuntu)** | SIEM / Detection |
| **Windows Server** | SOC console (Splunk UI access) |

> ❌ Kali and Security Onion are **not required** for this simulation.  
> ℹ️ pfSense is present in the lab as the **DHCP gateway**, but is **not used for detection or correlation**.

---

## 📂 Simulation Files

| File | Purpose |
|----|--------|
| `README.md` | Scenario overview and scope |
| `steps.md` | Reproducible execution steps |
| `queries.md` | SPL detection logic |
| `alert-config.md` | Alert definition (baseline-focused) |
| `logs.md` | Representative Sysmon telemetry |
| `screenshots/` | Evidence of execution and detection |

---

## 🧪 What Was Simulated

1. **Baseline execution**
   - Standard user launches common binaries:
     - `cmd.exe`
     - `powershell.exe`
     - `notepad.exe`
   - Execution occurs at **Medium integrity**

2. **Command-line visibility**
   - Commands executed with and without arguments
   - CommandLine field populated in Sysmon events

3. **Process lineage**
   - Parent → child relationships observed
   - Explorer → cmd → powershell chains visible

This simulation **intentionally avoids privilege escalation** to establish a clean execution baseline.

---

## 🔍 Detection Strategy

Detection is based on **Sysmon Process Create telemetry**, not Windows Security logs.

### Primary Signal
- **Sysmon Event ID 1 – Process Create**

### Key Fields Used
- `Image`
- `CommandLine`
- `ParentImage`
- `User`
- `IntegrityLevel`

### Detection Philosophy
- Establish what **normal execution looks like**
- Create a reusable baseline for:
  - Privilege escalation (SIM-005)
  - Script abuse
  - Living-off-the-land techniques

> Sysmon baseline analysis was validated at the endpoint; SIEM ingestion is treated
> as a lab enhancement rather than a prerequisite.

---

## 🚨 Alert Summary

- **Alert Name:** LAB-SIM-004-SYSMON-PROCESSCREATE
- **Severity:** Low
- **Trigger:** ≥ 1 matching execution event
- **Schedule:** Every 5 minutes (last 15 minutes)

This alert validates **telemetry availability**, not malicious intent.

---

## 📸 Evidence Captured

The following evidence was collected in `screenshots/`:

- `sim004-security-4688.png` – Windows Security Event ID 4688 baseline process creation
- `sim004-sysmon-processcreate.png` – Sysmon Event ID 1 enriched process creation details
- `sim004-correlation-results.png` – Correlated SPL results (Security + Sysmon)

---

## 🔗 Simulation Progression

This simulation establishes the **execution baseline** required for the next scenario:

➡️ **SIM-005 – Privilege Escalation (T1055)**  
Where elevated execution is detected by **contrasting against this baseline**.

---

## ✅ Success Criteria

| Requirement | Status |
|-----------|-------|
| Sysmon Event ID 1 captured | ✅ |
| Command-line visibility confirmed | ✅ |
| Parent/child relationships observed | ✅ |
| SPL queries validated | ✅ |
| Alert triggered | ✅ |
| Screenshots captured | ✅ |
| Detection matrix updated | ✅ |

---

## ⚠️ Issues & Resolutions

During execution, real-world issues were encountered related to:
- Sysmon index naming inconsistencies
- Forwarder ingestion gaps
- Hostname filtering assumptions
- Disk pressure affecting searches

All issues were **documented and resolved** as part of the lab workflow.

👉 **Full technical breakdown:**  
[SIM-004 – Issues & Resolutions](../../issues-and-resolutions/sim-004-sysmon-process-create.md)

---

## 🧠 Key Takeaways

- Sysmon provides **critical execution visibility** missing from default logs
- Command-line arguments are essential for context
- Baselines must exist **before** escalation detection
- Clean execution telemetry enables higher-confidence detections later

---

## 🏁 Status

**Simulation Status:** ✅ **Validated**

This simulation is complete, documented, and serves as the
**foundation for subsequent execution and privilege escalation detections**.
