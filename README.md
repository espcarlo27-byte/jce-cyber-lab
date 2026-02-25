# # JCE Enterprise-Modeled Security Operations Environment 🛡️

## Executive Summary

The JCE Enterprise-Modeled Security Operations Environment demonstrates the design, deployment, operation, and validation of a structured **Security Operations and Incident Response program** across network, endpoint, identity, and email layers.

This environment mirrors enterprise SOC workflows and emphasizes:

- **Alert lifecycle management**
- **Timeline reconstruction and root cause validation**
- **Cross-layer telemetry correlation (network, endpoint, identity, and email)**
- **Detection engineering aligned to MITRE ATT&CK**
- **Evidence documentation with chain-of-custody awareness**
- **Audit-ready control validation and governance traceability**

In addition to network and endpoint telemetry, the environment includes:

- 🔐 A structured **Active Directory Identity & Access Management (IAM) model**
- 📧 A **Zimbra mail server integrated with Active Directory**
- 📊 Identity-aware telemetry ingested into **Splunk SIEM**

Each simulation is structured as a defensible investigation, including reproducible attack execution, validated detection logic, structured evidence capture, and repeatable response workflows.  

The objective is not tool experimentation, but operational maturity — demonstrating how real-world security teams investigate, validate, document, and continuously improve detection coverage over time.

---

## 🏗 Enterprise Modeling Approach

This environment is intentionally designed to reflect enterprise Security Operations and governance structures rather than isolated tool experimentation.

Design principles include:

- Centralized identity-aware telemetry  
- Segmented network architecture  
- Stable detection identifiers  
- Alert lifecycle validation  
- Risk register maintenance  
- SOC 2–style evidence tracking  

The objective is not tool demonstration, but structured security operations maturity aligned to real-world enterprise practices.

---

## 🧠 Detection Coverage Philosophy

The JCE Enterprise-Modeled Security Operations Environment is structured as a **detection engineering program**, not a linear attack tutorial. Simulations are designed to collectively represent **how real breaches unfold**, and how SOC teams detect activity at different stages of attacker behavior.

### 🔗 Breach Lifecycle Mapping

Simulations are mapped to **attack progression stages** commonly observed in enterprise incident response:

| Breach Stage | Example Coverage in This Environment |
|--------------|------------------------------|
| Initial Access | SIM-001 (Phishing) |
| Execution | SIM-004, SIM-008 |
| Privilege Escalation | SIM-005 |
| Command & Control | SIM-002 |
| Lateral Movement | Planned future simulation |
| Impact / Data Interaction | SIM-006, SIM-007 |
| External Exploitation | SIM-003 |

This mapping allows detections to be understood in the context of **real incident investigation workflows**, rather than isolated environment tests.

---

### 🔢 About Simulation Numbering

Simulation IDs (**SIM-001 → SIM-XXX**) represent **chronological development of detections**, not the order of an attack chain.

In enterprise security programs:

- Detection IDs remain stable once created  
- New detections are added over time  
- Coverage is organized by **behavior and risk**, not numeric sequence  

For that reason, simulation numbers **do not imply attack order**. The authoritative view of detection coverage is the **Detection Validation Matrix**, which maps each simulation to attacker behavior stages.

---

## 🔐 Identity & Access Management (IAM) Layer

The environment includes a structured **enterprise-style Active Directory IAM model**
that provides identity context for detections and investigations.

The IAM design includes:

- Role-Based Access Control (RBAC)
- Department-based user structure
- Executive, IT, employee, and contractor separation
- Service account governance
- Privilege tier modeling

All human identities include **Title** and **Department** attributes, enabling:

| Use Case | IAM Value |
|----------|-----------|
| Incident response | Understand user role during investigation |
| Detection tuning | Role-based anomaly identification |
| Access reviews | Justify group memberships |
| Insider threat | Behavioral baseline by department |

📁 Full IAM documentation:  
**[Identity & Access Management Section](identity-access-management/)**

---

## 📧 Email Infrastructure & Identity Integration

The environment includes a **Zimbra mail server** integrated with Active Directory
to model email as a monitored attack surface.

This enables:

- Email authentication telemetry tied to AD user identities
- Detection of:
  - Failed mail logins
  - Password spraying against webmail
  - Account compromise patterns
  - Suspicious administrative access
- Cross-layer correlation:

| Attack Chain Example | Detection Flow |
|----------------------|----------------|
| Phishing credential reuse | Zimbra login → AD login → endpoint activity |
| Password spraying | Zimbra failures across many users → SIEM alert |
| Compromised mailbox | Mail login → PowerShell execution on endpoint |

Mail telemetry is forwarded to **Splunk**, making email activity part of the SOC visibility model.

This mirrors real enterprise SOC environments where **email, identity, endpoint, and network telemetry are correlated**.

---

## 🛡️ GRC Overlay (Audit-Ready Control Validation)

In addition to SOC detections, this repository includes a lightweight **GRC (Governance, Risk, and Compliance)** layer to ensure detections are not only functional, but also **documented, measurable, and auditable**.

Each simulation (SIM-001 → SIM-XXX) may also be treated as a **security control validation** with:
- Evidence IDs (audit traceability)
- Control ownership & test frequency
- Framework mapping (NIST CSF)
- Repeatable evidence collection (logs + screenshots)

📌 **GRC Program Folder:**  
- **[GRC Program](GRC-Program/)** – policies, risk register, control mapping, and audit evidence index

---

## 🧭 Repository Navigation

### 🔍 Core References
- 📊 **[Detection Validation Matrix](detection-matrix/detection-validation-matrix.md)** – Authoritative simulation status  
- 🚧 **[Issues & Resolutions](issues-and-resolutions/)** – Root causes, fixes, and lessons learned  
- 🛡️ **[GRC Program](GRC-Program/)** – Policies, risk register, control mapping, and audit evidence index

### 🧪 Validated Simulations
- ✅ **[SIM-001 – Phishing Email](simulations/SIM-001-Phishing-Email/)** – Initial Access  
- ✅ **[SIM-002 – DNS Tunneling](simulations/SIM-002-DNS-Tunneling/)** – Command & Control  
- ✅ **[SIM-003 – SQL Injection](simulations/SIM-003-SQL-Injection/)** – Application Exploitation  
- ✅ **[SIM-004 – Sysmon Process Create](simulations/SIM-004-Sysmon-Process-Create/)** – Execution Baseline  
- ✅ **[SIM-005 – Privilege Escalation](simulations/SIM-005-Privilege-Escalation/)** – Post-Exploitation  

---

## 🔧 Environment Topology (High-Level)

| System | Role |
|------|------|
| **pfSense** | Firewall, routing, NAT, DNS resolver, DHCP, traffic mirroring |
| **Windows Server 2025** | Active Directory (identity, authentication, domain services) |
| **Zimbra Mail Server** | Email services + identity-aware authentication telemetry |
| **Security Onion (Eval)** | Network Security Monitoring (Zeek, Suricata, limited PCAP) |
| **Windows 11** | User endpoint (Sysmon, Windows Security Logs) |
| **Kali Linux** | Attack simulation |
| **Ubuntu Server** | Splunk Enterprise SIEM |

> ℹ️ pfSense is intentionally used as the primary DNS resolver to centralize DNS visibility and support network-based detections (e.g., DNS tunneling).
> Active Directory provides identity and authentication services, but DNS resolution is handled at the network layer to keep detections portable and independent of domain-joined behavior.

> ⚠️ Security Onion is deployed in Evaluation mode, which imposes feature limitations (e.g., reduced or unavailable PCAP retention).
> As a result, detections prioritize parsed telemetry (Zeek logs, Suricata alerts, ECS-normalized events) rather than full packet capture, mirroring scenarios where SOC analysts operate with constrained visibility.

> End-user endpoints (e.g., Windows 11) use DHCP to reflect real-world environments, while core infrastructure components use static IPs; endpoint detections rely on hostname and telemetry context, not fixed IP > addresses.

---

### Simplified Network Topology

```mermaid
flowchart TB
    Internet["Internet"]
    pfSense["pfSense\nFirewall | DNS | DHCP"]

    AD["Windows Server 2025\nActive Directory"]
    Zimbra["Zimbra Mail Server"]
    SO["Security Onion (EVAL)\nZeek | Suricata"]
    Win11["Windows 11 Endpoint"]
    Kali["Kali Linux"]
    Splunk["Splunk Enterprise"]

    Internet --> pfSense
    pfSense --> AD
    pfSense --> Zimbra
    pfSense --> SO
    pfSense --> Win11
    pfSense --> Kali
    pfSense --> Splunk

    Zimbra --> Splunk
    AD --> Splunk
```

---

## 🏗 Architecture, Network Topology & Log Flow

This environment is intentionally designed to demonstrate enterprise-style network architecture, centralized visibility, and end-to-end log flow across network and endpoint layers.

### 📐 Architecture & Design Rationale
- **[View Architecture Documentation](architecture/)**

Explains design decisions, system roles, detection strategy, and tooling trade-offs (including Security Onion Eval constraints).

### 🌐 Network Topology
- **[View Network Topology Diagram](architecture/network-topology.md)**

Visual overview of pfSense-centered routing, VM placement, IP strategy, and traffic visibility paths.

### 📊 Network & Log Flow
- **[View Network & Log Flow](architecture/network-log-flow.md)**

Details how telemetry flows from endpoints and network devices into Security Onion and Splunk, including DNS, Zeek, Sysmon, and alert pipelines.

> ℹ️ These documents are referenced throughout individual simulations to ensure consistent architecture assumptions and reproducible detection results.

---

## 📊 Detection Validation Matrix (Authoritative)

This environment follows a **1:1 mapping** between detection scenarios and simulations.  
Each simulation folder contains complete technical evidence.

➡️ **[View the Detection Validation Matrix](detection-matrix/detection-validation-matrix.md)**

| Area | Coverage |
|------|----------|
| Initial Access | Phishing |
| Execution | Sysmon process telemetry |
| Privilege Escalation | UAC / elevated execution |
| Command & Control | DNS tunneling |
| Network Attacks | SQL injection |
| Detection Engineering | Queries, alerts, correlation |
| Validation Evidence | Logs + screenshots |

> 📌 **Authoritative Source:**  
> The Detection Validation Matrix is the **single source of truth** for simulation status.

---

## 🧪 Simulations (Hands-On Evidence)

Each simulation includes:
- `README.md` – Objective and scope
- `steps.md` – Reproducible execution
- `logs.md` – Real and symbolic evidence
- `queries.md` – Detection logic
- `alert-config.md` – Alert definition
- `screenshots/` – Proof of validation

---

## 📊 Detection & Response Capabilities

- **SIEM:** Splunk Enterprise  
- **Network Security Monitoring:** Zeek + Suricata (Security Onion)  
- **Endpoint Telemetry:** Sysmon, Windows Security Logs  
- **Threat Hunting:** Hunt, SPL searches  
- **Incident Response Framework:** NIST 800-61 lifecycle principles (Preparation, Detection & Analysis, Containment, Eradication, Recovery, Lessons Learned)
  
---

## 🧑‍💻 Skills Demonstrated

- Detection engineering (Zeek, Sysmon, SPL)
- SOC investigation workflows
- Windows & Active Directory security logging
- Network traffic analysis
- IDS alert validation
- MITRE ATT&CK mapping
- Alert design and testing
- Root cause analysis and documentation

---

## 📌 How to Replicate This Environment

1. Deploy VMs based on the topology
2. Configure log ingestion into Splunk
3. Enable traffic mirroring to Security Onion
4. Execute simulations (SIM-001 → SIM-004)
5. Capture logs and screenshots
6. Validate detections
7. Update the Detection Validation Matrix

---

## 🚧 Issues & Resolutions Log

All technical issues encountered during simulations are documented with:
- Root cause
- Resolution
- Analyst takeaway
- Final status

👉 **[View Issues & Resolutions](issues-and-resolutions/)**

---

## 📈 Next Steps

- Add Velociraptor for DFIR endpoint collection
- Expand credential access and lateral movement simulations
- Build SOC-style dashboards
- Scale to multi-host / ESXi environment

---

> “Every detection is validated. Every alert is defensible. Every scenario is reproducible.”  
> **— Carlo Espina**

