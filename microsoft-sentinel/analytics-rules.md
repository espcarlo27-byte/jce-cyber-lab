# Microsoft Sentinel Analytics Rules

## Executive Summary

This document describes the implementation and purpose of Analytics Rules within the JCE Enterprise-Modeled Security Operations Environment.

Analytics Rules are the primary detection mechanism in Microsoft Sentinel. They continuously evaluate collected telemetry against defined logic to identify suspicious activity and generate security alerts and incidents for investigation.

By combining telemetry collected from Windows endpoints, Azure resources, and identity services with Kusto Query Language (KQL), Analytics Rules provide automated detection capabilities that support Security Operations Center (SOC) workflows.

---

# Objectives

- Understand the purpose of Analytics Rules.
- Automate threat detection.
- Generate actionable security alerts.
- Reduce manual monitoring.
- Support incident response workflows.
- Align detections with the MITRE ATT&CK framework.

---

# Environment

| Component | Configuration |
|-----------|---------------|
| SIEM | Microsoft Sentinel |
| Log Analytics Workspace | LAW-JCE-SOC |
| Endpoint | JCE-WIN11-01 |
| Query Language | Kusto Query Language (KQL) |
| Detection Engine | Analytics Rules |

---

# What are Analytics Rules?

Analytics Rules continuously evaluate telemetry collected within Microsoft Sentinel.

When predefined conditions are met, they can:

- Generate alerts
- Create incidents
- Associate entities
- Trigger automation
- Notify analysts
- Initiate investigation workflows

Analytics Rules transform raw telemetry into actionable security events.

---

# Detection Workflow

```text
Windows Endpoint
        │
        ▼
Azure Monitor Agent (AMA)
        │
        ▼
Data Collection Rule (DCR)
        │
        ▼
Log Analytics Workspace
        │
        ▼
KQL Detection Query
        │
        ▼
Analytics Rule
        │
        ▼
Alert
        │
        ▼
Incident
        │
        ▼
SOC Investigation
```

---

# Types of Analytics Rules

Microsoft Sentinel supports multiple rule types.

| Rule Type | Purpose |
|-----------|----------|
| Scheduled | Execute KQL queries on a defined schedule |
| Microsoft Security | Import alerts from Microsoft security products |
| Near Real-Time (NRT) | Detect suspicious activity with minimal delay |
| Fusion | Correlate multiple alerts into a single high-confidence incident |
| Machine Learning | Detect anomalous behavior using behavioral analysis |

---

# Detection Strategy

Within this environment, Analytics Rules focus on common Windows security events.

Examples include:

- Failed logons
- Successful logons after multiple failures
- Suspicious PowerShell execution
- Process creation
- Account lockouts
- New user creation
- Privileged group membership changes
- Special privilege assignments

These detections support proactive monitoring and incident response.

---

# Example Detection 1 — Multiple Failed Logons

### Objective

Detect repeated authentication failures that may indicate password spraying or brute-force attacks.

Example KQL

```kql
SecurityEvent
| where EventID == 4625
| summarize FailedAttempts=count() by TargetAccount
| where FailedAttempts >= 5
```

MITRE ATT&CK

- T1110 — Brute Force

---

# Example Detection 2 — Successful Logon Following Failed Attempts

Objective

Identify successful authentication immediately following repeated failures.

Detection approach:

- Event ID 4625
- Event ID 4624
- Timestamp correlation
- Source IP comparison

Potential Indicators

- Credential compromise
- Password guessing
- Account takeover

---

# Example Detection 3 — Suspicious PowerShell

Example KQL

```kql
SecurityEvent
| where EventID == 4688
| where Process has "powershell"
```

Potential Indicators

- Encoded commands
- Download cradles
- Living-off-the-Land techniques

MITRE ATT&CK

- T1059.001 — PowerShell

---

# Entity Mapping

Analytics Rules can associate important entities with an alert.

Examples include:

- User account
- Computer
- Host
- IP Address
- Process
- File
- URL

Entity mapping improves investigation efficiency by providing contextual relationships.

---

# Incident Creation

When detection criteria are satisfied, Microsoft Sentinel can automatically:

- Generate an alert
- Create an incident
- Associate related alerts
- Map entities
- Assign severity
- Begin investigation

This reduces analyst response time and improves consistency.

---

# Best Practices

- Write focused KQL queries.
- Minimize false positives.
- Test rules before production use.
- Validate expected results.
- Map detections to MITRE ATT&CK.
- Use entity mapping whenever possible.
- Review rule performance regularly.

---

# Future Enhancements

Planned Analytics Rules include:

- Beaconing detection
- Impossible travel
- Service account abuse
- Privilege escalation
- Lateral movement
- Persistence mechanisms
- LOLBins
- Suspicious scheduled tasks
- Pass-the-Hash
- Kerberoasting

---

# Lessons Learned

Analytics Rules provide the automation layer that transforms collected telemetry into actionable security alerts. Effective rules require high-quality telemetry, well-designed KQL queries, and careful tuning to balance detection capability with false positive reduction.

Developing and validating Analytics Rules strengthens overall detection coverage while supporting efficient SOC operations.

---

# References

- Microsoft Sentinel Documentation
- Microsoft Learn
- Kusto Query Language (KQL) Documentation
- MITRE ATT&CK Framework
