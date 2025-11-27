# JCE Cyber Lab 🛡️

## Executive Summary
This lab demonstrates my ability to design, deploy, and operate a full detection engineering and SOC workflow, including SIEM ingestion, endpoint telemetry, IDS alerting, detection tuning, and incident response documentation. It serves as both a technical learning environment and a professional cybersecurity portfolio.

---

## 🔧 Lab Topology

![Lab Topology](diagrams/lab-topology.png)

- **pfSense** (10.0.0.1) – Firewall, NAT, VPN, traffic mirroring
- **Windows Server 2025** (10.0.0.10) – Active Directory, GPO, MS SQL Server
- **Security Onion (Eval)** (10.0.0.20) – Suricata, Zeek, Wazuh, Syslog
- **Kali Linux** (10.0.0.30) – Red team simulations, packet crafting
- **Windows 11 Endpoint** (10.0.0.50) – Sysmon, Splunk Forwarder, test user activity
- **Ubuntu VM** (10.0.0.60) – Splunk Enterprise Server

---

## 📊 Badges

![Splunk](https://img.shields.io/badge/SIEM-Splunk-blue) ![Security Onion](https://img.shields.io/badge/NSM-Security%20Onion-orange) ![Sysmon](https://img.shields.io/badge/Telemetry-Sysmon-purple) ![Suricata](https://img.shields.io/badge/IDS-Suricata-red) ![Kali Linux](https://img.shields.io/badge/OS-Kali%20Linux-black) ![Ubuntu](https://img.shields.io/badge/OS-Ubuntu-orange)

---

## 🧪 Symbolic Validation Matrix

| Scenario ID | Scenario | ATT&CK | Log Source | Detection Tools | Outcome |
|-------------|----------|--------|------------|----------------|---------|
| LAB-SIM-001 | Phishing Email | T1566 | Email Logs | Suricata, Splunk | Alert triggered, email headers parsed |
| LAB-SIM-002 | DNS Tunneling | T1071.004 | Network Logs | Zeek, Suricata | Abnormal DNS queries detected |
| LAB-SIM-003 | Privilege Escalation | T1055 | Sysmon | Sysmon, Wazuh | Event ID 4688, alert fired |
| LAB-SIM-004 | SQL Injection | T1190 | Web Logs | Splunk, Suricata | SQL log anomaly, Suricata HTTP rule |
| LAB-SIM-005 | Unauthorized File Access | T1070 | Windows Event Logs | Windows Event Logs, Splunk | Access report, policy violation noted |

Each scenario is documented in `/symbolic-validation/` with screenshots, rule IDs, and remediation steps.

---

## 🔬 Simulations

- [SIM-001 – Sysmon ProcessCreate](simulations/SIM-001-Sysmon-ProcessCreate/)
- [SIM-002 – Sysmon FileCreate](simulations/SIM-002-Sysmon-FileCreate/)
- [SIM-003 – PowerShell Download](simulations/SIM-003-PowerShell-Download/)

---

## 📊 Detection & Response

- **SIEM**: Splunk Enterprise (Ubuntu VM), Security Onion
- **IDS/IPS**: Suricata, Zeek
- **HIDS**: Wazuh + Sysmon
- **Threat Hunting**: SPL dashboards, packet analysis
- **Incident Response**: NIST-based playbooks

---

## 🔐 Governance & Compliance

- **Access control validation** with Windows ACLs
- **Audit log integrity** enforced through forwarder monitoring
- **User behavior monitoring** through Sysmon event families
- **Compliance simulation** aligned to SOC II / NIST 800-53

---

## ⚙️ Automation

- n8n workflow to triage alerts and parse logs
- Python scripts for log parsing and anomaly detection

---

## 🧑‍💻 Skills Demonstrated

- SIEM engineering (Splunk on Ubuntu VM)
- Log parsing and ingestion
- IDS tuning (Suricata/Zeek)
- HIDS configuration (Sysmon/Wazuh)
- Threat detection engineering
- Building repeatable simulations
- Incident response documentation
- NIST/NIST-800-61 alignment
- MITRE ATT&CK mapping
- Scripting automation (Python, n8n)

---

## 🗂️ Folder Structure

```
jce-cyber-lab/
├── diagrams/
├── simulations/
├── splunk-queries/
├── dashboards/
├── alerts/
├── troubleshooting/
└── scratchpad/
```

---

## 📌 How to Replicate This Lab

1. Deploy VMware VMs using the IP scheme in the topology diagram.
2. Install Splunk Enterprise on the Ubuntu VM.
3. Install Windows Server 2025, Security Onion, Windows 11, and Kali Linux VMs.
4. Configure pfSense firewall and NAT.
5. Deploy Sysmon and forward logs to Splunk.
6. Run attack simulations and collect symbolic logs.
7. Apply SPL queries to detect simulated activity.
8. Configure alerts using the provided templates.
9. Review dashboards and validate detection results.

---

## 📈 Next Steps

- Expand to VMware ESXi for enterprise-scale simulation
- Integrate Velociraptor for endpoint forensics
- Prepare for CySA+ and Splunk Core Certified User

---

> “Every detection is documented. Every alert is validated. Every scenario is reproducible.”
> — Carlo
