# SOC Playbook: Phishing Investigation

## Executive Summary

This playbook documents the investigation methodology used to analyze suspected phishing incidents within the JCE Enterprise-Modeled Security Operations Environment.

The objective is to determine whether a phishing email resulted in user interaction, credential compromise, malware execution, or additional malicious activity. The investigation combines endpoint, identity, and network telemetry with evidence correlation to support accurate, repeatable, and evidence-driven decision making.

This playbook follows the standardized SOC investigation methodology:

**Validate → Gather Context → Investigate → Correlate → Decide → Respond**

---

# MITRE ATT&CK

| Tactic | Technique |
|---------|-----------|
| Initial Access | T1566 – Phishing |
| Credential Access | T1110 (if credentials harvested) |
| Execution | T1059 (PowerShell, scripts if applicable) |
| Command and Control | T1071 (if beaconing observed) |

---

# Investigation Workflow

```
Alert
   │
   ▼
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

---

# Phase 1 — Validate

## Objective

Confirm that the alert is legitimate before beginning a full investigation.

Validate:

- Alert severity
- Alert source
- Detection rule
- Timestamp
- User
- Endpoint
- Email recipient

Questions

- Is this a genuine alert?
- Was the email actually delivered?
- Did the user interact with the message?

---

# Phase 2 — Gather Context

Collect information about the user, endpoint, and email.

## User

- Username
- Department
- Administrative privileges
- Normal working hours

## Endpoint

- Computer name
- Operating system
- Asset criticality

## Email

- Sender
- Recipient
- Subject
- Attachments
- Embedded URLs
- Delivery timestamp

---

# Phase 3 — Investigate

## Endpoint Investigation

Review:

- Windows Event ID 4688
- Sysmon Event ID 1
- Browser execution
- PowerShell execution
- Office application launches
- Child processes
- Downloaded files

Questions

- Was a process launched after the email was opened?
- Were suspicious command-line arguments used?
- Was PowerShell executed?
- Was a payload downloaded?

---

## Identity Investigation

Review:

Windows Events

- 4624
- 4625
- 4648

Microsoft Sentinel

- SigninLogs

Determine:

- Were there new logons after the click?
- Were credentials used from another location?
- Was MFA triggered?
- Were privileged accounts involved?

---

## Network Investigation

Review:

- DNS queries
- HTTP/HTTPS connections
- Firewall logs
- Zeek logs
- Suricata alerts
- External IP addresses

Questions

- Did the endpoint contact a suspicious domain?
- Were files downloaded?
- Was there beaconing activity?

---

# Phase 4 — Correlate

Correlate telemetry across multiple sources.

Example

Email Delivered

↓

User Click

↓

Browser Launch

↓

PowerShell Execution

↓

DNS Request

↓

Outbound HTTPS Connection

↓

Successful Authentication

↓

Privilege Escalation

Correlation establishes whether isolated events are part of the same attack chain.

---

# Phase 5 — Decide

Determine whether the activity is:

- Benign
- Suspicious
- Confirmed phishing
- Credential compromise
- Malware execution

Factors

- User behavior
- Historical activity
- Supporting evidence
- Timeline consistency

---

# Phase 6 — Respond

Possible response actions include:

- Isolate endpoint
- Disable compromised account
- Reset password
- Revoke active sessions
- Block malicious domains
- Block malicious IP addresses
- Notify affected users
- Escalate incident
- Preserve forensic evidence

---

# Microsoft Sentinel Investigation

Example KQL

Successful logons

```kql
SecurityEvent
| where EventID == 4624
```

Failed logons

```kql
SecurityEvent
| where EventID == 4625
```

Process creation

```kql
SecurityEvent
| where EventID == 4688
```

Recent Security Events

```kql
SecurityEvent
| where TimeGenerated >= ago(1h)
```

---

# Splunk Investigation

Example SPL

Failed Logons

```spl
index=winevent_security EventCode=4625
```

Process Creation

```spl
index=winevent_security EventCode=4688
```

Sysmon

```spl
index=winevent_sysmon EventCode=1
```

---

# Security Onion Investigation

Review:

## Zeek

- conn.log
- dns.log
- http.log

## Suricata

- alerts
- suspicious HTTP traffic
- malware signatures

Questions

- Were external connections established?
- Did DNS resolve suspicious domains?
- Were IDS alerts generated?

---

# Evidence Checklist

- Alert validated
- Email reviewed
- User interviewed (if applicable)
- Endpoint examined
- Authentication reviewed
- Network activity reviewed
- Timeline created
- Evidence correlated
- Findings documented

---

# Lessons Learned

A phishing alert should never be evaluated solely on the email itself. Effective investigations require correlating endpoint activity, authentication events, and network telemetry to determine the full scope of the incident.

Following a structured investigation methodology ensures that evidence is collected consistently, conclusions are supported by data, and response actions are appropriate for the observed activity.

---

# Interview Response

If asked during an interview how you would investigate a phishing alert:

> I would first validate the alert by confirming the email delivery, recipient, timestamp, and alert details. Next, I would gather context about the user, endpoint, and email. I would investigate endpoint telemetry such as process creation, browser activity, and PowerShell execution, then review authentication events for suspicious logons and network telemetry for outbound connections or malicious domains. After correlating evidence across endpoint, identity, and network logs, I would determine whether the activity was benign or malicious. If confirmed malicious, I would isolate the endpoint, reset credentials if necessary, escalate the incident, and follow the organization's incident response procedures.

---

# Analyst Notes

Key investigation principles demonstrated in this playbook:

- Validate the alert before assuming malicious activity.
- Gather sufficient context before drawing conclusions.
- Correlate endpoint, identity, and network telemetry.
- Base decisions on evidence rather than assumptions.
- Follow organizational incident response procedures.
