# Windows Endpoint Onboarding to Microsoft Sentinel

## Executive Summary

This document describes the process used to onboard a Windows endpoint from the JCE Enterprise-Modeled Security Operations Environment into Microsoft Sentinel using Azure Monitor Agent (AMA) and a Data Collection Rule (DCR).

The objective was to collect Windows Security Event Logs and endpoint telemetry in Azure Log Analytics, enabling detection, investigation, and threat hunting through Microsoft Sentinel.

---

# Environment

| Component | Configuration |
|-----------|---------------|
| SIEM | Microsoft Sentinel |
| Log Analytics Workspace | LAW-JCE-SOC |
| Resource Group | RG-JCE-SOC |
| Region | East US |
| Endpoint | JCE-WIN11-01 |
| Operating System | Windows 11 Pro |
| Azure Monitor Agent | Installed |
| Data Collection Rule | Configured |
| Windows Security Events | Enabled |

---

# Objectives

- Connect a Windows endpoint to Microsoft Sentinel.
- Install the Azure Monitor Agent.
- Configure Data Collection Rules.
- Collect Windows Security Event Logs.
- Validate successful data ingestion.
- Prepare the environment for KQL detections and incident investigations.

---

# Architecture

```
Windows 11 Endpoint
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
Microsoft Sentinel
        │
        ▼
KQL Queries • Analytics Rules • Investigations
```

---

# Implementation Steps

## Step 1 — Create Log Analytics Workspace

A dedicated Log Analytics Workspace was created to centralize endpoint telemetry for Microsoft Sentinel.

Workspace Name

```
LAW-JCE-SOC
```

---

## Step 2 — Enable Microsoft Sentinel

Microsoft Sentinel was enabled on the Log Analytics Workspace to provide SIEM and SOAR capabilities.

---

## Step 3 — Install Azure Monitor Agent

The Azure Monitor Agent (AMA) was deployed to the Windows endpoint.

Endpoint

```
JCE-WIN11-01
```

AMA enables secure telemetry collection from Windows devices into Azure Monitor.

---

## Step 4 — Configure Data Collection Rule

A Data Collection Rule (DCR) was created and associated with the Windows endpoint.

The DCR specifies which Windows event logs are collected and forwarded to Log Analytics.

Configured data sources included:

- Security Event Logs
- Windows Event Logs
- Sysmon (when configured)
- Performance metrics (optional)

---

## Step 5 — Associate Endpoint

The Data Collection Rule was assigned to the Windows endpoint.

This association allows Azure Monitor Agent to begin forwarding configured telemetry.

---

## Step 6 — Validate Data Collection

After onboarding was completed, Log Analytics was queried to verify event ingestion.

Example query:

```kql
SecurityEvent
| take 10
```

If Sysmon is configured:

```kql
WindowsEvent
| take 10
```

---

# Validation

Validation consisted of confirming:

- Azure Monitor Agent installed successfully.
- Endpoint associated with the Data Collection Rule.
- Windows Security Events appearing in Log Analytics.
- KQL queries returning expected results.
- Endpoint visible within Microsoft Sentinel.

---

# Troubleshooting

Common onboarding issues include:

## No Security Events

Possible causes:

- Azure Monitor Agent not installed
- Incorrect Data Collection Rule
- Missing DCR association
- Insufficient permissions

---

## Empty Query Results

Verify:

- Correct table name
- Time range
- Data Collection Rule configuration
- Windows event logging enabled

---

## Agent Offline

Check:

- Internet connectivity
- Azure Arc / Azure VM connection status
- Agent health
- Windows services

---

# Lessons Learned

The onboarding process demonstrated that successful Microsoft Sentinel deployment depends on accurate agent installation, properly configured Data Collection Rules, and validation of data ingestion before building detection logic.

Establishing reliable telemetry collection provides the foundation for analytics rules, threat hunting, and incident response.

---

# References

- Microsoft Sentinel Documentation
- Azure Monitor Agent Documentation
- Log Analytics Documentation
- Kusto Query Language (KQL) Documentation

---

## Related Documentation

- [Microsoft Sentinel Overview](README.md)
- [Architecture](architecture.md)
- [Windows Endpoint Onboarding](windows-onboarding.md)
- [Azure Monitor Agent Installation](ama-installation.md)
- [Data Collection Rules](data-collection-rules.md)
- [KQL Detection Queries](kql-detection-queries.md)
- [Analytics Rules](analytics-rules.md)
- [Automation Rules](automation-rules.md)
- [Threat Hunting](threat-hunting.md)
- [Incident Investigation](incident-investigation.md)
- [MITRE ATT&CK Mapping](mitre-attack-mapping.md)
- [Validation Tests](validation-tests.md)
- [Cost Management](cost-management.md)
