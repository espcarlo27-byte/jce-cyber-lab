# Microsoft Sentinel Data Collection Rules (DCR)

## Executive Summary

This document describes the implementation of Azure Data Collection Rules (DCR) within the JCE Enterprise-Modeled Security Operations Environment. Data Collection Rules define which telemetry is collected from monitored systems and forwarded to Azure Log Analytics for analysis in Microsoft Sentinel.

Proper DCR configuration ensures that only relevant security telemetry is collected, helping balance visibility, detection capability, and cost management.

---

# Objectives

- Configure Azure Data Collection Rules for Windows endpoints.
- Collect Windows Security Event Logs required for security monitoring.
- Enable future collection of Sysmon telemetry.
- Validate successful data ingestion into Log Analytics.
- Support Microsoft Sentinel analytics, hunting, and incident investigations.

---

# Environment

| Component | Configuration |
|----------|---------------|
| SIEM | Microsoft Sentinel |
| Log Analytics Workspace | LAW-JCE-SOC |
| Resource Group | RG-JCE-SOC |
| Endpoint | JCE-WIN11-01 |
| Agent | Azure Monitor Agent (AMA) |
| Collection Method | Data Collection Rule (DCR) |

---

# What is a Data Collection Rule?

A Data Collection Rule defines:

- Which data sources are monitored.
- Which event logs are collected.
- Where collected telemetry is sent.
- Which endpoints receive the configuration.

Rather than configuring each endpoint individually, a DCR provides centralized and consistent telemetry collection across multiple systems.

---

# Architecture

```
Windows Endpoint
       │
       ▼
Azure Monitor Agent
       │
       ▼
Data Collection Rule
       │
       ▼
Log Analytics Workspace
       │
       ▼
Microsoft Sentinel
```

---

# Configured Data Sources

The following data sources were configured for collection.

| Data Source | Purpose |
|-------------|---------|
| Security Event Logs | Authentication and security auditing |
| Windows Event Logs | Operating system activity |
| Sysmon Events (planned) | Detailed endpoint telemetry |
| Performance Counters (optional) | System health monitoring |

---

# Security Events Collected

Examples of Windows Security Event IDs monitored include:

| Event ID | Description |
|----------|-------------|
| 4624 | Successful logon |
| 4625 | Failed logon |
| 4634 | Logoff |
| 4648 | Explicit credential logon |
| 4672 | Special privileges assigned |
| 4688 | Process creation |
| 4720 | User account created |
| 4726 | User account deleted |
| 4732 | Member added to security group |
| 4740 | Account locked out |

These events support authentication monitoring, account management, and process activity investigations.

---

# Validation

After deployment, telemetry validation was performed by executing KQL queries within Log Analytics.

Example:

```kql
SecurityEvent
| summarize Count=count() by EventID
| order by Count desc
```

Successful query results confirmed that Windows Security Events were being collected through the configured Data Collection Rule.

---

# Benefits of Data Collection Rules

Implementing DCRs provides several operational advantages:

- Centralized configuration management.
- Consistent telemetry collection across endpoints.
- Simplified onboarding of additional systems.
- Reduced administrative overhead.
- Support for scalable enterprise deployments.
- Better control over data collection costs.

---

# Troubleshooting

## No Events Received

Verify:

- Azure Monitor Agent is installed.
- Endpoint is associated with the correct DCR.
- Windows event logging is enabled.
- The endpoint has internet connectivity.
- Log Analytics Workspace permissions are correct.

---

## Missing Event IDs

Possible causes include:

- Incorrect event log selection.
- Windows auditing not enabled.
- Insufficient endpoint activity.
- Incorrect query time range.

---

# Lessons Learned

Data Collection Rules are a foundational component of Microsoft Sentinel deployments. Properly configured DCRs ensure reliable telemetry collection while reducing unnecessary data ingestion. Understanding how data flows from the endpoint through the Azure Monitor Agent into Log Analytics is essential for building effective detection and investigation capabilities.

---

# References

- Microsoft Sentinel Documentation
- Azure Monitor Agent Documentation
- Azure Monitor Data Collection Rules Documentation
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
