# SIM-005 – Privilege Escalation (T1055)

## 🎯 Goal

Simulate and detect **local privilege escalation** on a Windows 11 endpoint by validating that:

- A standard user executes processes normally (baseline)
- Privileged execution occurs via UAC elevation
- Elevated processes spawn child processes
- Telemetry is captured in **Windows Security (4688)** and **Sysmon**
- Splunk detects and alerts on elevated integrity execution

This simulation validates the **Privilege Escalation** row in the
[Detection Validation Matrix](../../detection-matrix/detection-validation-matrix.md).

---

## 🧩 MITRE ATT&CK Mapping

- **Technique:** T1055 – Privilege Escalation  
- **Tactic:** Privilege Escalation (TA0004)

---

## 🏗 Lab Components Used

| Component | Role |
|---------|-----|
| **Windows 11 Endpoint** (`Windows11Pro`) | Victim host |
| **Standard User** (`local.lab\labuser`) | Initial execution |
| **Administrator (UAC)** | Privileged execution |
| **Splunk Enterprise (Ubuntu)** | SIEM / Detection |
| **Windows Server** | SOC console (Splunk UI access) |

> ❌ Kali, Security Onion, and pfSense are **not required** for this simulation.

---

## 📂 Simulation Files

| File | Purpose |
|----|--------|
| `steps.md` | Exact, reproducible execution steps |
| `queries.md` | SPL detection and correlation logic |
| `alert-config.md` | Splunk alert definition |
| `logs.md` | Symbolic + representative log evidence |
| `screenshots/` | Visual proof of detection and alerting |

---

## 🧪 What Was Simulated

1. **Baseline execution**
   - `labuser` runs standard processes (cmd.exe)
   - Integrity level remains **Medium**

2. **Privilege escalation**
   - User launches Command Prompt via **Run as Administrator**
   - UAC elevation approved
   - Execution occurs under **administrator** context

3. **Post-escalation behavior**
   - Elevated `cmd.exe` spawns:
     - `powershell.exe`
     - `notepad.exe`
   - High-integrity child processes created

---

## 🔍 Detection Strategy

Detection is based on **behavior**, not username alone.

### Primary Signals
- **Windows Security Event ID 4688**
- **Sysmon Event ID 1**
- **IntegrityLevel = High or System**
- Abnormal parent → child relationships

### Key Detection Principles
- UAC elevation often logs activity under `administrator`
- Username alone is unreliable
- Integrity level and process lineage are authoritative

---

## 🚨 Alert Summary

- **Alert Name:** LAB-SIM-003-PRIVESC-ALERT
- **Severity:** High
- **Trigger:** ≥ 1 elevated process execution
- **Schedule:** Every 5 minutes (last 15 minutes)

The alert fired successfully during live execution.

---

## 📸 Evidence Captured

The following screenshots were collected and stored in `screenshots/`:

- `sim003-elevated-cmd.png` – Elevated Command Prompt
- `sim003-security-4688.png` – Windows Security process creation
- `sim003-sysmon-processcreate.png` – Sysmon elevated execution
- `sim003-correlation-results.png` – SPL detection results
- `sim003-alert-fired.png` – Alert firing confirmation

---

## ✅ Success Criteria

| Requirement | Status |
|-----------|-------|
| Baseline activity observed | ✅ |
| Privileged execution detected | ✅ |
| Sysmon telemetry captured | ✅ |
| Security 4688 events captured | ✅ |
| SPL queries validated | ✅ |
| Alert triggered | ✅ |
| Screenshots captured | ✅ |
| Detection matrix updated | ✅ |

---

## ⚠️ Issues & Resolutions

During execution of SIM-002, multiple **real-world operational and platform-related issues**
were encountered that affected full detection validation.

These issues included:
- Index mismatches (`winlog` vs `winevent_sysmon`)
- Hostname filtering errors
- UAC user context attribution
- Sysmon configuration overwrite
- Disk space exhaustion blocking Splunk searches

All issues were **investigated, documented, and resolved where possible** within
the constraints of the lab environment.

👉 **Full technical breakdown:**  
[SIM-003 – Issues & Resolutions](../../issues-and-resolutions/sim-003-privilege-escalation.md)

---

## 🧠 Key Takeaways

- Privilege escalation detection must rely on **integrity level**, not usernames
- Windows logs elevated activity under effective security context
- Sysmon + Security logs together provide **high-confidence detection**
- Real-world detection requires adapting to actual telemetry, not assumptions

---

## 🏁 Status

**Simulation Status:** ✅ **Validated**

This simulation is complete, documented, and suitable for
**SOC analyst / detection engineering portfolio presentation**.
