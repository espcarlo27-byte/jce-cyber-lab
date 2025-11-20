# JCE Cyber Lab 🛡️

Welcome to my forensic-grade cybersecurity lab, built to simulate real-world SOC workflows and validate detection capabilities across network, endpoint, and identity layers.

This lab was designed to align with enterprise analyst roles, focusing on SIEM, threat detection, IAM, compliance, and incident response.

---

## 🔧 Lab Topology

- **pfSense** (10.0.0.1) – Firewall, NAT, VPN, and traffic mirroring
- **Windows Server 2025** (10.0.0.10) – Active Directory, GPO, MS SQL Server
- **Security Onion (Eval)** (10.0.0.20) – Suricata, Zeek, Wazuh, Syslog
- **Kali Linux** (10.0.0.30) – Red team simulations, packet crafting
- **Windows 11 Endpoint** (10.0.0.50) – Sysmon, Splunk Forwarder, test user activity

---

## 🧪 Symbolic Validation Matrix

| Scenario ID |          Scenario        |       Detection Tools      |               Outcome                 |
|-------------|--------------------------|----------------------------|---------------------------------------|
| LAB-SIM-001 | Phishing Email           | Suricata, Splunk           | Alert triggered, email headers parsed |
| LAB-SIM-002 | DNS Tunneling            | Zeek, Suricata             | Abnormal DNS queries detected         |
| LAB-SIM-003 | Privilege Escalation     | Sysmon, Wazuh              | Event ID 4688, alert fired            |
| LAB-SIM-004 | SQL Injection            | Splunk, Suricata           | SQL log anomaly, Suricata HTTP rule   |
| LAB-SIM-005 | Unauthorized File Access | Windows Event Logs, Splunk | Access report, policy violation note  |

Each scenario is documented in `/symbolic-validation/` with screenshots, rule IDs, and remediation steps.

---

## 📊 Detection & Response

- **SIEM**: Splunk, Security Onion
- **IDS/IPS**: Suricata, Zeek
- **HIDS**: Wazuh + Sysmon
- **Threat Hunting**: SPL dashboards, packet analysis
- **Incident Response**: NIST-based playbooks

---

## 🔐 Governance & Compliance

- Simulated audit trails
- File permission enforcement
- Information privacy policy enforcement
- Executive-style incident reports

---

## ⚙️ Automation

- n8n workflow to triage alerts and parse logs
- Python scripts for log parsing and anomaly detection

---

## 📈 Next Steps

- Expand to VMware ESXi for enterprise-scale simulation
- Integrate Velociraptor for endpoint forensics
- Prepare for CySA+ and Splunk Core Certified User

---

> “Every detection is documented. Every alert is validated. Every scenario is reproducible.”  
> — Carlo
