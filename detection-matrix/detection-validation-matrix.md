# 📊 Detection Validation Matrix  
### Enterprise Security Operations Environment (JCE) — Detection Engineering Program

This matrix tracks the **validation status of detection scenarios** built as part of a structured, evidence-driven detection engineering program. Each simulation represents a stage of attacker behavior and is validated through:

- Reproducible attack steps  
- Real telemetry collection  
- SPL detection logic  
- Alert configuration  
- Screenshot-based evidence  

> ✅ A scenario is considered **Validated** only when detection logic, alerting, and evidence are complete.

---

# 🧠 Detection Coverage by Breach Lifecycle Stage

Rather than isolated tests, simulations are mapped to **real attack progression** used in SOC investigations.

| Attack Stage | Description | Simulations Covering Stage |
|-------------|-------------|----------------------------|
| **Initial Access** | User compromise or entry vector | **SIM-001** (Phishing Email) |
| **Execution** | Malicious code execution on host | **SIM-004**, **SIM-008** |
| **Privilege Escalation** | Elevated rights gained | **SIM-005** |
| **Command & Control / Data Channel** | Covert communication methods | **SIM-002** |
| **Lateral Movement** | Internal spread between hosts | *(Planned – upcoming simulation)* |
| **Impact / Data Interaction** | File or system access activity | **SIM-006**, **SIM-007** |
| **External Exploitation Scenario** | Perimeter-facing vulnerability | **SIM-003** |

This structure mirrors **enterprise incident investigation workflows**, not standalone Enterprise Security Operations Environment (JCE) exercises.

---

## 🔐 Identity & Email Telemetry Integration

The Enterprise Security Operations Environment (JCE) detection program extends beyond traditional network and endpoint monitoring.  
The environment integrates:

- **Active Directory Identity & Access Management (IAM)**
- **Zimbra Mail Server authentication telemetry**

This enables detection workflows that incorporate **identity context and email activity** into correlation logic.

As a result, the Detection Validation Matrix represents coverage across **four telemetry layers**:

| Layer | Example Data Source |
|------|---------------------|
| Network | Zeek logs, Suricata alerts |
| Endpoint | Sysmon, Windows Security Events |
| Identity | Active Directory authentication, group membership changes |
| Email | Zimbra login, authentication, and administrative activity |

This multi-layer integration mirrors enterprise SOC environments where investigations span:

**Email → Identity → Endpoint → Network**

Detection scenarios may therefore include identity-aware or email-based evidence chains in addition to traditional telemetry.

---

# 🧪 Simulation Validation Matrix

| SIM ID | Scenario | MITRE ATT&CK | Data Sources | Detection Logic | Alert | Evidence | Status |
|------|---------|--------------|-------------|----------------|-------|----------|--------|
| **SIM-001** | Phishing Email (Link) | T1566.002 | Sysmon, Network | SPL Correlation | ✅ | Screenshots | **Validated** |
| **SIM-002** | DNS Tunneling | T1071.004 | DNS, Network | SPL + Pattern Analysis | ✅ | Screenshots | **Validated** |
| **SIM-003** | SQL Injection | T1190 | Network / IDS | Suricata + SPL | ✅ | Screenshots | **Validated** |
| **SIM-004** | Sysmon Process Create | T1059 | Windows Security, Sysmon | Baseline Execution Analysis | ⚠️ Optional | Screenshots | **Validated** |
| **SIM-005** | Privilege Escalation | T1055 | Windows Security, Sysmon | SPL Correlation | ✅ | Screenshots | **Validated** |
| SIM-006 | Unauthorized File Access | T1070 | Windows Logs | Planned | ⏳ | ⏳ | Planned |
| SIM-007 | Sysmon File Create | T1105 | Sysmon | Planned | ⏳ | ⏳ | Planned |
| SIM-008 | PowerShell Download | T1059.001 | Sysmon, Network | Planned | ⏳ | ⏳ | Planned |

---

# 🔗 Simulation References

| SIM ID | Scenario | Link |
|------|----------|------|
| **SIM-001** | Phishing Email | [SIM-001 – Phishing Email](../simulations/SIM-001-Phishing-Email/) |
| **SIM-002** | DNS Tunneling | [SIM-002 – DNS Tunneling](../simulations/SIM-002-DNS-Tunneling/) |
| **SIM-003** | SQL Injection | [SIM-003 – SQL Injection](../simulations/SIM-003-SQL-Injection/) |
| **SIM-004** | Sysmon Process Create | [SIM-004 – Sysmon Process Create](../simulations/SIM-004-Sysmon-Process-Create/) |
| **SIM-005** | Privilege Escalation | [SIM-005 – Privilege Escalation](../simulations/SIM-005-Privilege-Escalation/) |
| SIM-006 | Unauthorized File Access | [SIM-006 – Unauthorized File Access](../simulations/SIM-006-Unauthorized-File-Access/) |
| SIM-007 | Sysmon File Create | [SIM-007 – Sysmon FileCreate](../simulations/SIM-007-Sysmon-FileCreate/) |
| SIM-008 | PowerShell Download | [SIM-008 – PowerShell Download](../simulations/SIM-008-PowerShell-Download/) |

---

# 🧠 Status Definitions

| Status | Meaning |
|------|--------|
| **Validated** | Detection logic, alert, and evidence confirmed |
| **Partial** | Detection logic exists but blocked by environment constraints |
| **Planned** | Folder structure exists; execution pending |

---

# 📌 Program Notes

- **SIM-001 → SIM-005** demonstrate validated detections across **initial access, execution, and privilege escalation**.
- Upcoming simulations expand coverage into **post-compromise and impact-stage behaviors**.
- All simulations follow a **consistent detection engineering workflow** with symbolic IDs and reproducible evidence.

---

# 🏁 Validation Philosophy

> A detection is not complete until it is:  
> **Executed → Logged → Correlated → Alerted → Evidenced**

This matrix represents **operational detection engineering**, not theoretical ATT&CK coverage. Every “Validated” entry is backed by logs, queries, alerts, and evidence artifacts.
