# Microsoft Sentinel Architecture

## Executive Summary

This document describes the Microsoft Sentinel architecture implemented within the JCE Enterprise-Modeled Security Operations Environment. The deployment demonstrates how endpoint telemetry is collected, processed, analyzed, and transformed into actionable security insights using native Microsoft Azure security services.

The architecture was designed to emulate a modern enterprise Security Operations Center (SOC) by integrating Windows endpoint telemetry with Azure Monitor, Log Analytics, and Microsoft Sentinel.

---

# Objectives

- Demonstrate a modern cloud-native SIEM architecture.
- Centralize Windows security telemetry.
- Enable threat detection using Microsoft Sentinel.
- Support incident investigation and threat hunting.
- Build a scalable architecture that can accommodate additional endpoints and cloud resources.

---

# High-Level Architecture

```text
                           Azure Subscription
                                   │
                                   ▼
                     Resource Group (RG-JCE-SOC)
                                   │
                ┌──────────────────┴──────────────────┐
                │                                     │
                ▼                                     ▼
      Log Analytics Workspace                 Microsoft Sentinel
          (LAW-JCE-SOC)                    SIEM / SOAR Platform
                ▲                                     │
                │                                     │
                │                           Analytics Rules
                │                           Hunting Queries
                │                           Workbooks
                │                           Incidents
                │                           Automation
                │
                │
        Data Collection Rule (DCR)
                ▲
                │
      Azure Monitor Agent (AMA)
                ▲
                │
        Windows 11 Endpoint
          JCE-WIN11-01
```

---

# Components

## Windows Endpoint

The Windows 11 endpoint generates operating system and security telemetry, including:

- Authentication events
- Account management events
- Process creation
- Security auditing
- Windows Event Logs
- Sysmon telemetry (planned)

---

## Azure Monitor Agent (AMA)

The Azure Monitor Agent securely collects telemetry from the endpoint and forwards it according to the assigned Data Collection Rule.

Responsibilities include:

- Secure log collection
- Endpoint telemetry forwarding
- Configuration updates through Azure
- Integration with Azure Monitor

---

## Data Collection Rule (DCR)

The Data Collection Rule defines:

- Which logs are collected
- Which endpoints receive the configuration
- Where telemetry is forwarded

This centralizes configuration and simplifies future expansion.

---

## Log Analytics Workspace

The Log Analytics Workspace serves as the central repository for collected telemetry.

Primary functions include:

- Data ingestion
- Long-term storage
- Query execution
- Historical analysis
- Integration with Microsoft Sentinel

Workspace:

```
LAW-JCE-SOC
```

---

## Microsoft Sentinel

Microsoft Sentinel provides:

- Security Information and Event Management (SIEM)
- Security Orchestration, Automation, and Response (SOAR)
- Threat hunting
- Analytics rules
- Incident management
- Investigation dashboards
- Workbook visualization

---

# Telemetry Flow

```text
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
        │
        ▼
Detection
Investigation
Threat Hunting
Incident Response
```

---

# Current Data Sources

| Data Source | Status |
|------------|--------|
| Windows Security Events | Enabled |
| Windows Event Logs | Enabled |
| Sysmon Events | Planned |
| Azure Activity | Available |
| Microsoft Entra ID Sign-in Logs | Available |
| Heartbeat | Enabled |

---

# Detection Workflow

1. Endpoint activity occurs.
2. Azure Monitor Agent collects telemetry.
3. Data Collection Rule filters the required logs.
4. Logs are stored in Log Analytics.
5. Microsoft Sentinel analyzes incoming data.
6. Analytics Rules generate alerts.
7. Security analysts investigate incidents using KQL and related evidence.

---

# Design Considerations

The architecture was designed with the following goals:

- Cloud-native monitoring
- Scalable endpoint onboarding
- Centralized log management
- Modular documentation
- Cost-conscious telemetry collection
- Support for future automation

---

# Future Enhancements

Planned improvements include:

- Sysmon integration
- Additional Windows endpoints
- Linux server onboarding
- Automation Rules
- Logic Apps playbooks
- Custom analytics rules
- Threat intelligence integration
- MITRE ATT&CK coverage mapping
- Defender for Endpoint integration

---

# Lessons Learned

Designing the architecture before expanding detection content established a clear understanding of how telemetry moves through the Microsoft Sentinel ecosystem. Separating each component into distinct responsibilities improved documentation quality, simplified troubleshooting, and created a scalable foundation for future enhancements.

---

# References

- Microsoft Sentinel Documentation
- Azure Monitor Documentation
- Azure Monitor Agent Documentation
- Azure Log Analytics Documentation
- Microsoft Learn
