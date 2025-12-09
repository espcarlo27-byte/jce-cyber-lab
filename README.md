# JCE Cyber Lab 🛡️

## Executive Summary

This cyber lab demonstrates my ability to design, deploy, and operate a full detection engineering and SOC workflow across network, endpoint, and identity layers. It contains end-to-end attack simulations, symbolic and real log evidence, Splunk detections, alerting pipelines, and MITRE-aligned validation. Each detection scenario has a **dedicated simulation folder (1:1 mapping)** to show reproducible, hands-on results.

---

## 🔧 Lab Topology

![Lab Topology](diagrams/lab-topology.png)

* **pfSense** (10.0.0.1) – Firewall, NAT, VPN, TAP/SPAN mirroring  
* **Windows Server 2025** (10.0.0.10) – AD, DNS, GPO, MS SQL Server  
* **Security Onion (Eval)** (10.0.0.20) – Suricata, Zeek, Wazuh, Syslog  
* **Kali Linux** (10.0.0.30) – Red-team simulations and tooling  
* **Windows 11 Endpoint** (10.0.0.50) – Windows Security Auditing, Splunk Universal Forwarder  
* **Ubuntu VM** (10.0.0.60) – Splunk Enterprise SIEM  

---

## 📊 Detection Validation Matrix (Aligned 1:1)

Each scenario has a matching `/simulations/SIM-00X-*` folder containing: steps, logs, queries, alerts, and screenshots.

| SIM ID       | Scenario                 | MITRE ATT&CK | Data Source                          | Detection Tools                | Status        |
| ------------ | ------------------------ | ------------ | ------------------------------------ | ------------------------------ | ------------- |
| SIM-001      | Phishing Email           | T1566.002    | Windows Security 4688 + Network      | Splunk, Suricata               | ✅ Validated  |
| SIM-002      | DNS Tunneling            | T1071.004    | DNS + Network                        | Zeek, Suricata, Splunk         | Ready         |
| SIM-003      | Privilege Escalation     | T1055        | Windows + Sysmon                     | Wazuh, Splunk                  | Ready         |
| SIM-004      | SQL Injection            | T1190        | Web/HTTP Logs                        | Suricata, Splunk               | Ready         |
| SIM-005      | Unauthorized File Access | T1070        | Windows Logs                         | Splunk                         | Ready         |
| SIM-006      | Sysmon ProcessCreate     | T1059        | Sysmon                               | Sysmon, Splunk                 | Ready         |
| SIM-007      | Sysmon FileCreate        | T1105        | Sysmon                               | Sysmon, Splunk                 | Ready         |
| SIM-008      | PowerShell Download      | T1059.001    | Sysmon + Network                     | Sysmon, Suricata, Splunk       | Ready         |

---

## 🧪 Simulations (Hands-On Evidence)

Every simulation contains:

* `README.md` – Summary + expected outcome  
* `steps.md` – Full reproducible steps  
* `logs.md` – Symbolic and real logs  
* `queries.md` – SPL detection logic  
* `alert-config.md` – Alert definition + symbolic ID  
* `screenshots/` – Evidence of hits and alerts  

### Available Simulations

* ✅ [SIM-001 – Phishing Email (Validated)](simulations/SIM-001-Phishing-Email/)  
* [SIM-002 – DNS Tunneling](simulations/SIM-002-DNS-Tunneling/)  
* [SIM-003 – Privilege Escalation](simulations/SIM-003-Privilege-Escalation/)  
* [SIM-004 – SQL Injection](simulations/SIM-004-SQL-Injection/)  
* [SIM-005 – Unauthorized File Access](simulations/SIM-005-Unauthorized-File-Access/)  
* [SIM-006 – Sysmon ProcessCreate](simulations/SIM-006-Sysmon-ProcessCreate/)  
* [SIM-007 – Sysmon FileCreate](simulations/SIM-007-Sysmon-FileCreate/)  
* [SIM-008 – PowerShell Download](simulations/SIM-008-PowerShell-Download/)  

---

## 📂 Repository Structure
jce-cyber-lab/
├── README.md
├── diagrams/
├── detection-matrix/
├── simulations/
│ ├── SIM-001-Phishing-Email/
│ ├── SIM-002-DNS-Tunneling/
│ ├── SIM-003-Privilege-Escalation/
│ ├── SIM-004-SQL-Injection/
│ ├── SIM-005-Unauthorized-File-Access/
│ ├── SIM-006-Sysmon-ProcessCreate/
│ ├── SIM-007-Sysmon-FileCreate/
│ └── SIM-008-PowerShell-Download/
├── splunk-queries/
├── alerts/
├── dashboards/
├── troubleshooting/
└── scratchpad/

---

## 📊 Detection & Response Capabilities

* **SIEM:** Splunk Enterprise (Ubuntu)  
* **NSM/IDS:** Suricata + Zeek (Security Onion)  
* **HIDS / Telemetry:** Windows Security Auditing, Sysmon, Wazuh  
* **Threat Hunting:** SPL dashboards, network analysis  
* **IR Framework:** NIST 800-61  

---

## ⚙️ Automation

* n8n workflows for automated triage  
* Python scripts for log parsing and anomaly detection  
* Custom symbolic log tagging  

---

## 🧑‍💻 Skills Demonstrated

* SIEM engineering (Splunk)
* Detection engineering and SPL writing
* Windows Security Auditing & command-line logging
* Sysmon rule validation
* Suricata/Zeek NSM analysis
* Log ingestion + parsing pipeline design
* MITRE ATT&CK mapping
* Incident response workflow creation
* Dashboard creation and alert tuning

---

## 📌 How to Replicate This Lab

1. Deploy all VMs based on the topology diagram.  
2. Install Splunk on Ubuntu and configure ingest from:
   * Windows Security Logs (via Splunk UF)
   * Sysmon (for Sysmon-based sims)
   * Security Onion (Suricata/Zeek logs)  
3. Configure pfSense routes + monitoring.  
4. Run simulations in order (SIM-001 → SIM-008).  
5. Capture logs and screenshots as evidence.  
6. Validate detections using dashboards and alerts.  
7. Update detection-matrix with results.  

---

## 📈 Next Steps

* Add Velociraptor for DFIR endpoint collection  
* Add more credential access simulations  
* Expand to VMware ESXi cluster  
* Build a full SOC dashboard pack  

---

> **“Every detection is documented. Every alert is validated. Every scenario is reproducible.”**  
> — Carlo

