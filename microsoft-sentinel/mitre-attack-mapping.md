# MITRE ATT&CK Detection Mapping

## Executive Summary

This document maps common MITRE ATT&CK techniques to the telemetry, detection logic, and investigation methodology implemented within the JCE Enterprise-Modeled Security Operations Environment.

Rather than treating MITRE ATT&CK as a standalone framework, this document demonstrates how attacker techniques can be identified using Windows Security Events, Sysmon, Microsoft Sentinel, Splunk, and Security Onion.

The objective is to create a repeatable reference that links attacker behavior to practical detection and investigation workflows.

---

# Objectives

- Map MITRE ATT&CK techniques to enterprise telemetry.
- Identify relevant Windows Event IDs.
- Identify applicable Sysmon events.
- Associate Microsoft Sentinel KQL queries.
- Associate Splunk searches.
- Identify Security Onion evidence.
- Provide investigation guidance.
- Support SOC analyst investigations.

---

# Detection Methodology

Each technique is mapped using the following structure:

- MITRE Technique
- ATT&CK Tactic
- Windows Event IDs
- Sysmon Events
- Microsoft Sentinel
- Splunk
- Security Onion
- Investigation Workflow
- Response Actions

---

# Technique T1110 — Brute Force

## ATT&CK Tactic

Credential Access

## Windows Security Events

- 4625 — Failed Logon
- 4624 — Successful Logon (after failures)
- 4740 — Account Lockout

## Sysmon

Not typically required.

## Microsoft Sentinel

Example KQL

```kql
SecurityEvent
| where EventID == 4625
| summarize FailedAttempts=count() by TargetAccount
| where FailedAttempts >= 5
```

## Splunk

Example SPL

```spl
index=winevent_security EventCode=4625
| stats count by TargetUserName
```

## Security Onion

Review:

- Zeek conn.log
- Firewall logs
- Source IP reputation
- Geographic location

## Investigation

- Validate the alert.
- Gather user and host context.
- Correlate failed and successful logons.
- Review source IP addresses.
- Determine whether authentication succeeded.

## Response

- Reset credentials.
- Enable or enforce MFA.
- Block malicious IPs if appropriate.
- Escalate confirmed attacks.

---

# Technique T1059.001 — PowerShell

## ATT&CK Tactic

Execution

## Windows Events

- 4688 — Process Creation

## Sysmon

- Event ID 1 — Process Creation

## Microsoft Sentinel

```kql
SecurityEvent
| where EventID == 4688
| where Process has "powershell"
```

## Splunk

```spl
index=winevent_security EventCode=4688 powershell
```

## Security Onion

Review:

- DNS activity
- HTTP connections
- Suricata alerts

## Investigation

Review:

- Parent process
- Command line
- Encoded commands
- Download activity
- Network communication

## Response

- Isolate endpoint.
- Collect forensic evidence.
- Reset credentials if compromised.
- Continue incident investigation.

---

# Technique T1078 — Valid Accounts

## ATT&CK Tactic

Defense Evasion

## Windows Events

- 4624
- 4672

## Microsoft Sentinel

Review successful logons from unusual sources.

## Investigation

Determine:

- Is the logon normal?
- Is the device expected?
- Is the location expected?
- Were privileged accounts involved?

## Response

- Validate user activity.
- Review MFA.
- Reset credentials if necessary.

---

# Technique T1566 — Phishing

## ATT&CK Tactic

Initial Access

## Evidence Sources

Windows

- Browser launches
- PowerShell
- Process creation

Network

- DNS requests
- HTTP connections

Identity

- Authentication activity

## Investigation

- Confirm user interaction.
- Review endpoint telemetry.
- Correlate network activity.
- Review authentication events.

## Response

- Isolate endpoint if necessary.
- Reset credentials.
- Block malicious domains.
- Notify affected users.

---

# Technique T1055 — Process Injection

## Windows Events

- 4688

## Sysmon

- Event ID 1
- Event ID 8
- Event ID 10

## Investigation

Review:

- Parent-child process relationships
- Unusual process execution
- Memory injection indicators

---

# Technique T1548 — Abuse Elevation Control Mechanism

## Windows Events

- 4672
- 4688

## Investigation

Determine:

- Was privilege escalation authorized?
- Were administrative tools abused?
- Did suspicious processes execute afterward?

---

# Investigation Workflow

Every mapped technique follows the same investigation methodology:

1. Validate
2. Gather Context
3. Investigate
4. Correlate
5. Decide
6. Respond

This workflow provides consistency across all investigations regardless of the specific ATT&CK technique involved.

---

# Future Technique Coverage

Planned additions include:

- T1558 — Kerberoasting
- T1550 — Pass-the-Hash
- T1021 — Remote Services
- T1047 — WMI
- T1053 — Scheduled Tasks
- T1098 — Account Manipulation
- T1003 — Credential Dumping
- T1486 — Data Encrypted for Impact
- T1071 — Application Layer Protocol
- T1105 — Ingress Tool Transfer

---

# Lessons Learned

MITRE ATT&CK becomes significantly more valuable when mapped directly to operational telemetry and investigation procedures. By connecting ATT&CK techniques to Windows events, Sysmon, Microsoft Sentinel, Splunk, and Security Onion, analysts can move more efficiently from detection to investigation and response.

Maintaining a standardized mapping also improves consistency across investigations, supports analyst training, and strengthens overall detection engineering efforts.

---

# References

- MITRE ATT&CK Framework
- Microsoft Sentinel Documentation
- Microsoft Learn
- Sysmon Documentation
- Splunk Documentation
- Security Onion Documentation
