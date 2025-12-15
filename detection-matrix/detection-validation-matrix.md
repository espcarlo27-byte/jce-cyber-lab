# Detection Validation Matrix

This matrix provides a **1:1 mapping** between simulated attack scenarios and their validated detections across endpoint and network telemetry.

Each row links directly to a **hands-on simulation folder** containing:
- Reproducible steps
- Symbolic and real logs
- Detection queries
- Alert configurations
- Screenshots as evidence

This matrix serves as the **authoritative validation record** for the JCE Cyber Lab.

---

## 🧪 Detection Validation Matrix

| SIM ID  | Scenario Name            | MITRE ATT&CK | Tactic                | Data Sources                              | Detection Tools            | Alert Symbolic ID                  | Status        | Evidence |
|--------:|--------------------------|--------------|------------------------|-------------------------------------------|----------------------------|-----------------------------------|---------------|----------|
| SIM-001 | Phishing Email (Link)    | T1566.002    | Initial Access         | Sysmon, Suricata, HTTP                    | Splunk, Security Onion     | LAB-SIM-001-PHISHING-ALERT         | ✅ Validated  | [View](../simulations/SIM-001-Phishing-Email/) |
| SIM-002 | DNS Tunneling            | T1071.004    | Command & Control      | DNS Logs, Network Traffic                 | Zeek, Suricata, Splunk     | LAB-SIM-002-DNS-TUNNEL-ALERT       | ⚠️ Partial    | [View](../simulations/SIM-002-DNS-Tunneling/) |
| SIM-003 | Privilege Escalation     | T1055        | Privilege Escalation   | Sysmon, Windows Security (4688)           | Splunk                     | LAB-SIM-003-PRIVESC-ALERT          | ✅ Validated  | [View](../simulations/SIM-003-Privilege-Escalation/) |
| SIM-004 | SQL Injection            | T1190        | Initial Access         | Web / HTTP Logs                           | Suricata, Splunk           | TBD                               | ⏳ Planned    | — |
| SIM-005 | Unauthorized File Access | T1070        | Defense Evasion        | Windows Logs, Sysmon                      | Splunk                     | TBD                               | ⏳ Planned    | — |
| SIM-006 | Sysmon ProcessCreate     | T1059        | Execution              | Sysmon                                    | Splunk                     | TBD                               | ⏳ Planned    | — |
| SIM-007 | Sysmon FileCreate        | T1105        | Command & Control      | Sysmon                                    | Splunk                     | TBD                               | ⏳ Planned    | — |
| SIM-008 | PowerShell Download      | T1059.001    | Execution              | Sysmon, Network                           | Splunk, Suricata           | TBD                               | ⏳ Planned    | — |

---

## 🧠 Status Legend

- ✅ **Validated** — Simulation executed, logs captured, detection confirmed, alert fired, screenshots saved  
- ⚠️ **Partial** — Simulation executed with documented environmental limitations  
- ⏳ **Planned** — Simulation structure created, execution pending  

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

This matrix reflects **real lab execution**, not theoretical coverage.

---
