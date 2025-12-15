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
| **SIM-002** | DNS Tunneling | T1071.004 | DNS, Network | SPL + Pattern Analysis | ⚠️ Partial | Screenshots | **Partial** |
| **SIM-003** | Privilege Escalation | T1055 | Sysmon, Security | SPL Integrity Analysis | ✅ | Screenshots | **Validated** |
| SIM-004 | SQL Injection | T1190 | Web / HTTP Logs | Planned | ⏳ | ⏳ | Planned |
| SIM-005 | Unauthorized File Access | T1070 | Windows Logs | Planned | ⏳ | ⏳ | Planned |
| SIM-006 | Sysmon ProcessCreate | T1059 | Sysmon | Planned | ⏳ | ⏳ | Planned |
| SIM-007 | Sysmon FileCreate | T1105 | Sysmon | Planned | ⏳ | ⏳ | Planned |
| SIM-008 | PowerShell Download | T1059.001 | Sysmon, Network | Planned | ⏳ | ⏳ | Planned |

---

## 🔗 Simulation References

| SIM ID | Link |
|-----|-----|
| SIM-001 | [SIM-001 – Phishing Email](../simulations/SIM-001-Phishing-Email/) |
| SIM-002 | [SIM-002 – DNS Tunneling](../simulations/SIM-002-DNS-Tunneling/) |
| SIM-003 | [SIM-003 – Privilege Escalation](../simulations/SIM-003-Privilege-Escalation/) |
| SIM-004 | [SIM-004 – SQL Injection](../simulations/SIM-004-SQL-Injection/) |
| SIM-005 | [SIM-005 – Unauthorized File Access](../simulations/SIM-005-Unauthorized-File-Access/) |
| SIM-006 | [SIM-006 – Sysmon ProcessCreate](../simulations/SIM-006-Sysmon-ProcessCreate/) |
| SIM-007 | [SIM-007 – Sysmon FileCreate](../simulations/SIM-007-Sysmon-FileCreate/) |
| SIM-008 | [SIM-008 – PowerShell Download](../simulations/SIM-008-PowerShell-Download/) |

---

## 🧠 Status Definitions

| Status | Meaning |
|------|--------|
| **Validated** | Detection logic, alert, and evidence confirmed |
| **Partial** | Detection logic exists but blocked by environment constraints |
| **Planned** | Folder structure exists; execution pending |

---

## 📌 Notes

- **SIM-002** is marked **Partial** due to Security Onion Elastic limitations in evaluation mode.
- **SIM-001** and **SIM-003** are **fully validated** with alerts firing and screenshots captured.
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
