# Microsoft Sentinel Incident Investigation Workflow

## Executive Summary

This document describes the incident investigation methodology used within the JCE Enterprise-Modeled Security Operations Environment when analyzing security alerts in Microsoft Sentinel.

The investigation process follows a structured workflow designed to validate alerts, gather relevant evidence, correlate telemetry from multiple sources, determine whether malicious activity occurred, and initiate appropriate response actions.

The methodology is applicable to common security scenarios such as phishing, suspicious PowerShell execution, brute-force attacks, account compromise, privilege escalation, and suspicious process execution.

---

# Investigation Methodology

The investigation process follows six repeatable phases:

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

This repeatable workflow promotes consistent investigations and supports efficient incident response.

---

# Phase 1 — Validate

The first step is determining whether the alert represents legitimate activity or requires further investigation.

Validation includes reviewing:

- Alert severity
- Alert timestamp
- Alert source
- Alert rule
- Affected endpoint
- User account
- Alert description
- Triggering analytics rule

Typical questions include:

- Is the alert legitimate?
- Was telemetry successfully collected?
- Is the event complete?
- Are duplicate alerts present?

---

# Phase 2 — Gather Context

Once the alert has been validated, additional context is collected.

Typical context includes:

## User Information

- Username
- Department
- Administrative privileges
- Recent authentication history

## Endpoint Information

- Computer name
- Operating system
- Device role
- Asset criticality

## Network Information

- Source IP
- Destination IP
- Geographic location
- Internal or external communication

## Authentication Activity

- Successful logons
- Failed logons
- Logon type
- Recent account changes

Gathering context establishes a baseline before deeper analysis begins.

---

# Phase 3 — Investigate

The investigation focuses on collecting evidence from available telemetry.

Evidence may include:

### Endpoint Telemetry

- Process creation
- Parent-child process relationships
- PowerShell execution
- Command-line arguments
- Service creation
- Scheduled tasks

### Authentication Logs

- Event ID 4624
- Event ID 4625
- Account lockouts
- Privileged logons

### Network Activity

- Outbound connections
- DNS requests
- Remote IP addresses
- Beaconing behavior

### Account Activity

- Password changes
- Group membership changes
- New user accounts
- Privilege assignments

---

# Phase 4 — Correlate

Individual events rarely provide the complete picture.

Evidence is correlated across multiple telemetry sources.

Examples include:

## Authentication + Endpoint

A successful logon immediately followed by suspicious PowerShell execution.

## Endpoint + Network

A suspicious process followed by outbound communication to an unfamiliar IP address.

## Authentication + Network

Repeated failed logons followed by a successful authentication from the same source.

## Endpoint + Identity

Privilege escalation followed by creation of a new administrative account.

Correlation improves confidence when determining whether activity is malicious.

---

# Phase 5 — Decide

Evidence collected during the investigation is evaluated.

Possible outcomes include:

- Benign activity
- Expected administrative activity
- Policy violation
- Suspicious activity requiring monitoring
- Confirmed security incident

Factors considered include:

- User behavior
- Historical activity
- Known maintenance windows
- Business justification
- Supporting evidence

---

# Phase 6 — Respond

When malicious activity is confirmed, appropriate response actions are initiated.

Possible response actions include:

- Escalate the incident
- Isolate the affected endpoint
- Disable or lock the compromised account
- Reset credentials
- Block malicious IP addresses
- Collect forensic evidence
- Notify stakeholders
- Follow organizational incident response procedures

---

# Example Investigation

## Scenario

Microsoft Sentinel generates an alert for suspicious PowerShell execution.

### Validate

Confirm the alert details, endpoint, user, timestamp, and analytics rule.

### Gather Context

Determine:

- Who executed PowerShell?
- Which endpoint?
- Is the user an administrator?
- Has the user executed PowerShell previously?

### Investigate

Review:

- Process creation
- Parent process
- Command line
- Authentication events
- Recent account activity

### Correlate

Determine whether:

- The PowerShell process created network connections.
- Additional suspicious processes executed.
- Authentication events occurred before execution.
- Other alerts were generated for the same endpoint.

### Decide

Determine whether activity is:

- Administrative
- Benign
- Suspicious
- Malicious

### Respond

If malicious:

- Isolate endpoint.
- Escalate incident.
- Reset credentials if necessary.
- Continue forensic investigation.

---

# Supporting Microsoft Sentinel Features

Microsoft Sentinel assists investigations through:

- Incidents
- Analytics Rules
- KQL Queries
- Entity Mapping
- Investigation Graph
- Workbooks
- Watchlists
- Hunting Queries
- Automation Rules

---

# MITRE ATT&CK Alignment

This workflow supports investigations involving techniques such as:

| Technique | Description |
|-----------|-------------|
| T1110 | Brute Force |
| T1059 | Command and Scripting Interpreter |
| T1078 | Valid Accounts |
| T1566 | Phishing |
| T1055 | Process Injection |
| T1548 | Abuse Elevation Control Mechanism |

---

# Lessons Learned

Successful investigations rely on collecting complete evidence before reaching conclusions. Correlating endpoint, authentication, and network telemetry provides significantly greater confidence than analyzing isolated events.

Following a structured methodology ensures investigations remain repeatable, consistent, and evidence-driven while reducing the likelihood of overlooking important indicators.

---

# References

- Microsoft Sentinel Documentation
- Microsoft Learn
- MITRE ATT&CK Framework
- Azure Monitor Documentation
