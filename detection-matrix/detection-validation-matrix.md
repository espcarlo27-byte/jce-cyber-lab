# 📊 Detection Validation Matrix

This matrix tracks the **validation status of all detection scenarios** in the JCE Cyber Lab.  
Each simulation listed below has a **1:1 mapped folder** containing:

- Reproducible steps
- Symbolic + real logs
- SPL detection queries
- Alert configurations
- Screenshots as evidence

> ✅ A scenario is considered **Validated** only when detection logic, alerting, and evidence are complete.

---

## 🧪 Simulation Coverage Matrix

| SIM ID | Scenario | MITRE ATT&CK | Data Sources | Detection Logic | Alert | Evidence | Status |
|------|---------|--------------|-------------|----------------|-------|----------|--------|
| **SIM-001** | Phishing Email (Link) | T1566.002 | Sysmon, Network | SPL Correlation | ✅ | Screenshots | **Validated** |
| **SIM-002** | DNS Tunneling | T1071.004 | DNS, Network | SPL + Pattern Analysis | ✅ | Screenshots | **Validated** |
| **SIM-003** | SQL Injection | T1190 | Network / IDS | Suricata + SPL | ✅ | Screenshots | **Validated** |
| **SIM-004** | Sysmon Process Create | T1059 | Windows Security (4688), Sysmon (1) | Baseline Execution Analysis | ⚠️ Optional | Screenshots | **Validated** |
| **SIM-005** | Privilege Escalation | T1055 | Windows Security (4688), Sysmon (1) | SPL Correlation | ✅ | Screenshots | **Validated** |
| SIM-006 | Unauthorized File Access | T1070 | Windows Logs | Planned | ⏳ | ⏳ | Planned |
| SIM-007 | Sysmon File Create | T1105 | Sysmon | Planned | ⏳ | ⏳ | Planned |
| SIM-008 | PowerShell Download | T1059.001 | Sysmon, Network | Planned | ⏳ | ⏳ | Planned |

---

## 🔗 Simulation References

| SIM ID | Scenario | Link |
|------|----------|------|
| **SIM-001** | Phishing Email | [SIM-001 – Phishing Email](../simulations/SIM-001-Phishing-Email/) |
| **SIM-002** | DNS Tunneling | [SIM-002 – DNS Tunneling](../simulations/SIM-002-DNS-Tunneling/) |
| **SIM-003** | SQL Injection | [SIM-003 – SQL Injection](../simulations/SIM-003-SQL-Injection/) |
| **SIM-004** | Sysmon Process Create | [SIM-004 – Sysmon Process Create](../simulations/SIM-004-Sysmon-Process-Create/) |
| **SIM-005** | Privilege Escalation | [SIM-005 – Privilege Escalation](../simulations/SIM-005-Privilege-Escalation/) |
| SIM-006 | Unauthorized File Access | [SIM-006 – Unauthorized File Access](../simulations/SIM-006-Unauthorized-File-Access/) |
| SIM-007 | Sysmon Process Create (File) | [SIM-007 – Sysmon FileCreate](../simulations/SIM-007-Sysmon-FileCreate/) |
| SIM-008 | PowerShell Download | [SIM-008 – PowerShell Download](../simulations/SIM-008-PowerShell-Download/) |

---

## 🧠 Status Definitions

| Status | Meaning |
|------|--------|
| **Validated** | Detection logic, alert, and evidence confirmed |
| **Partial** | Detection logic exists but blocked by environment constraints |
| **Planned** | Folder structure exists; execution pending |

---

## 📌 Notes

- **SIM-001**,  **SIM-002**, **SIM-003**, and **SIM-005** are fully validated with alerts firing and screenshots captured.
- **SIM-004** establishes execution telemetry and serves as a foundation for privilege escalation detection.
- All future simulations will follow the **same evidence-driven workflow**.

---

## 🔗 How to Use This Matrix

- **Recruiters / Hiring Managers**
  - Start here to see which detections were **actually validated**
  - Click into any simulation folder for full evidence

- **Detection Engineers / SOC Analysts**
  - Trace detections from MITRE ATT&CK → telemetry → SPL → alert
  - Review real-world field mappings and edge cases

- **Lab Maintenance**
  - Update status after each simulation
  - Add symbolic IDs once alerts are finalized

---

## 🏁 Validation Philosophy

> A detection is not complete until it is:
> - Executed  
> - Logged  
> - Correlated  
> - Alerted  
> - Evidenced  

> **This matrix represents real detection engineering work, not theoretical coverage.**  
> Each “Validated” entry is backed by logs, queries, alerts, and screenshots.

---
