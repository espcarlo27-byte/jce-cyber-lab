# SOC Investigation Playbooks

## Executive Summary

This directory contains standardized investigation playbooks developed within the JCE Enterprise-Modeled Security Operations Environment.

The purpose of these playbooks is to provide repeatable investigation procedures for common cybersecurity incidents encountered by Security Operations Center (SOC) analysts.

Rather than focusing on a specific SIEM platform, each playbook emphasizes investigation methodology, evidence collection, telemetry correlation, and incident response. This approach allows the investigation process to remain consistent across Microsoft Sentinel, Splunk, Security Onion, Microsoft Defender XDR, and other security platforms.

---

# Investigation Philosophy

Technology changes.

Investigation methodology should not.

Every investigation follows the same structured workflow:

```
Validate
     │
     ▼
Gather Context
     │
     ▼
Investigate
     │
     ▼
Correlate
     │
     ▼
Decide
     │
     ▼
Respond
```

This methodology promotes consistency, repeatability, and evidence-based decision making.

---

# Investigation Principles

Every investigation should answer six questions:

1. What happened?
2. Who was involved?
3. When did it happen?
4. Where did it occur?
5. How did it happen?
6. What should be done next?

---

# Evidence Sources

Investigations may use evidence from multiple platforms, including:

## Endpoint

- Windows Event Logs
- Sysmon
- Microsoft Defender for Endpoint

## Identity

- Active Directory
- Microsoft Entra ID
- Authentication logs

## Network

- Zeek
- Suricata
- Firewall logs
- DNS logs
- Proxy logs

## SIEM

- Microsoft Sentinel
- Splunk
- Security Onion

---

# Current Playbooks

| Playbook | Status |
|----------|--------|
| Phishing Investigation | Planned |
| Brute Force Investigation | Planned |
| Password Spraying | Planned |
| Suspicious PowerShell | Planned |
| Account Compromise | Planned |
| Privilege Escalation | Planned |
| Beaconing Detection | Planned |
| Ransomware Investigation | Planned |
| Lateral Movement | Planned |

---

# Standard Investigation Structure

Every playbook follows the same format.

1. Scenario Overview
2. Objectives
3. Alert Validation
4. Context Collection
5. Endpoint Investigation
6. Identity Investigation
7. Network Investigation
8. Evidence Correlation
9. Determination
10. Response
11. MITRE ATT&CK Mapping
12. Relevant Windows Event IDs
13. Sysmon Events
14. Microsoft Sentinel KQL
15. Splunk SPL
16. Security Onion Investigation
17. Documentation
18. Lessons Learned

---

# Future Expansion

Additional playbooks will be developed as new simulations and technologies are added to the environment.

Future topics include:

- Insider Threat
- Living-off-the-Land Binaries (LOLBins)
- Kerberoasting
- Pass-the-Hash
- Pass-the-Ticket
- WMI Abuse
- Scheduled Task Persistence
- Service Account Abuse
- Cloud Identity Attacks

---

# Purpose

These playbooks serve as both a technical reference and a practical guide for conducting structured, evidence-driven security investigations.

By maintaining a consistent investigation methodology across multiple technologies, analysts can improve accuracy, reduce response time, and continuously strengthen detection and response capabilities.
