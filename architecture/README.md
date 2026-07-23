# Architecture Overview

This section documents the **network topology, log flow, and design decisions**
used in the Enterprise Security Operations Environment (JCE).

The architecture is intentionally designed to mirror **real-world SOC
environments**, emphasizing:

- Behavioral detection over static assumptions  
- Clear separation of infrastructure vs endpoints  
- Reproducible detection engineering workflows  
- Identity, email, and authentication telemetry integration  

The Enterprise Security Operations Environment (JCE) now includes a **dedicated enterprise mail server** to simulate
realistic phishing, authentication abuse, and email-borne attack paths.

---

## 📐 Architecture Documents

- 🖧 **[Network Topology](network-topology.md)**  
  High-level network layout, system roles, and IP addressing strategy  

- 🔄 **[Network & Log Flow](network-log-flow.md)**  
  Detailed traffic paths, log ingestion flows, and detection visibility  

---

## 🏗 Core Infrastructure Components

| System | Role | Detection Value |
|--------|------|-----------------|
| **pfSense** | Firewall, DNS, DHCP | Network boundary, DNS telemetry, traffic control |
| **Active Directory (Windows Server)** | Identity provider | Authentication logs, account abuse detection |
| **Security Onion** | Network Security Monitoring (Zeek + Suricata) | IDS alerts, protocol analysis, network evidence |
| **Splunk Enterprise** | SIEM & detection platform | Log correlation, dashboards, alerting |
| **Ubuntu Mail Server (Zimbra)** | Enterprise email + directory integration | Phishing simulation, email authentication logs, user credential abuse telemetry |

---

## 📬 Why the Mail Server Matters

Adding the mail server enables simulation of:

- Phishing campaigns  
- Malicious attachment delivery  
- Credential harvesting scenarios  
- Account compromise via email access  
- Internal lateral movement following email-based intrusion  

This shifts the Enterprise Security Operations Environment (JCE) from **endpoint-only visibility** to a full **SOC detection chain**:

> Email ➜ Identity ➜ Endpoint ➜ Network ➜ SIEM Correlation

---

## 🧠 Design Philosophy

- Infrastructure systems use **static IPs** for stability  
- Endpoints and attacker systems use **DHCP** for realism  
- Detections rely on **telemetry and behavior**, not fixed addressing  
- Network visibility is passive and non-intrusive  
- Identity and email telemetry are treated as **first-class detection sources**

This mirrors modern SOC and detection engineering best practices where
**email is the #1 initial access vector**.

---

## 🔭 Detection Coverage Expansion

With the mail server, the Enterprise Security Operations Environment (JCE) now supports detection engineering across:

- Endpoint telemetry (Sysmon, Windows Security logs)  
- Network telemetry (Zeek, Suricata)  
- Identity telemetry (Active Directory)  
- **Email & authentication telemetry (Zimbra logs)**  
- SIEM correlation (Splunk)

---

**Result:** The Enterprise Security Operations Environment (JCE) now models a **complete enterprise attack surface** rather
than isolated host monitoring.
