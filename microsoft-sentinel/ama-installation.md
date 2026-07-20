# Azure Monitor Agent (AMA) Installation

## Executive Summary

This document describes the deployment and validation of the Azure Monitor Agent (AMA) within the JCE Enterprise-Modeled Security Operations Environment. The AMA serves as the telemetry collection agent responsible for securely forwarding Windows endpoint data to Azure Monitor according to configured Data Collection Rules (DCRs).

Deploying AMA is a critical step in enabling Microsoft Sentinel to collect security telemetry for monitoring, detection, threat hunting, and incident investigation.

---

# Objectives

- Deploy Azure Monitor Agent to a Windows endpoint.
- Establish secure communication with Azure Monitor.
- Associate the endpoint with a Data Collection Rule (DCR).
- Validate telemetry ingestion into Log Analytics.
- Prepare the endpoint for Microsoft Sentinel analytics.

---

# Environment

| Component | Configuration |
|-----------|---------------|
| Endpoint | JCE-WIN11-01 |
| Operating System | Windows 11 Pro |
| Agent | Azure Monitor Agent (AMA) |
| Log Analytics Workspace | LAW-JCE-SOC |
| Microsoft Sentinel | Enabled |
| Resource Group | RG-JCE-SOC |
| Region | East US |

---

# What is Azure Monitor Agent?

Azure Monitor Agent (AMA) is Microsoft's modern telemetry collection agent for Azure Monitor and Microsoft Sentinel.

Its responsibilities include:

- Collecting Windows event logs
- Collecting performance metrics
- Collecting Sysmon telemetry (when configured)
- Securely forwarding telemetry to Azure Monitor
- Applying centralized Data Collection Rule configurations

Unlike the legacy Log Analytics agent, AMA separates telemetry collection from configuration by using Data Collection Rules (DCRs), providing greater flexibility and scalability.

---

# Deployment Workflow

```text
Windows Endpoint
        │
        ▼
Install Azure Monitor Agent
        │
        ▼
Authenticate with Azure
        │
        ▼
Assign Data Collection Rule
        │
        ▼
Collect Security Telemetry
        │
        ▼
Forward Logs to Log Analytics
        │
        ▼
Microsoft Sentinel
```

---

# Installation Process

## Step 1 — Select the Endpoint

The Windows endpoint selected for onboarding:

```
JCE-WIN11-01
```

---

## Step 2 — Deploy Azure Monitor Agent

The Azure Monitor Agent was deployed from the Azure portal by selecting the target machine and installing the AMA extension.

The installation established secure communication between the endpoint and Azure Monitor.

---

## Step 3 — Associate the Data Collection Rule

After installation, the endpoint was associated with the appropriate Data Collection Rule (DCR).

The DCR determines:

- Which logs are collected
- Which event channels are monitored
- Where telemetry is forwarded

---

## Step 4 — Verify Agent Health

Validation included confirming:

- AMA installation completed successfully.
- Agent status reported as healthy.
- Endpoint communication with Azure Monitor was established.

---

## Step 5 — Validate Data Ingestion

Telemetry validation was performed using KQL queries.

Example:

```kql
SecurityEvent
| where TimeGenerated >= ago(1h)
| take 10
```

If Sysmon telemetry is enabled:

```kql
WindowsEvent
| where TimeGenerated >= ago(1h)
| take 10
```

---

# Telemetry Collected

The deployed AMA supports collection of:

| Telemetry | Purpose |
|-----------|---------|
| Security Event Logs | Authentication and auditing |
| Windows Event Logs | Operating system events |
| Sysmon Events | Detailed endpoint visibility |
| Performance Metrics | Endpoint health monitoring |

---

# Validation Checklist

Successful deployment was verified by confirming:

- Azure Monitor Agent installed.
- Endpoint visible within Azure.
- Data Collection Rule assigned.
- Security events arriving in Log Analytics.
- KQL queries returning expected results.
- Microsoft Sentinel receiving telemetry.

---

# Troubleshooting

## No Logs Received

Possible causes:

- AMA installation incomplete
- Incorrect Data Collection Rule
- Endpoint not associated with the DCR
- Internet connectivity issues
- Windows Firewall restrictions

---

## Agent Not Reporting

Verify:

- Azure Monitor Agent service is running.
- Endpoint has internet connectivity.
- Azure extension deployment completed successfully.
- Resource permissions are correct.

---

## Empty KQL Results

Check:

- Correct table name (`SecurityEvent` or `WindowsEvent`)
- Query time range
- Windows auditing configuration
- Data Collection Rule configuration

---

# Best Practices

- Deploy AMA using centralized Azure management.
- Use Data Collection Rules to standardize telemetry collection.
- Validate data ingestion immediately after deployment.
- Collect only the telemetry required for monitoring objectives.
- Review ingestion regularly to balance visibility and cost.

---

# Lessons Learned

Deploying the Azure Monitor Agent demonstrated the importance of validating every stage of the telemetry pipeline. Successful installation alone does not guarantee visibility; agent health, Data Collection Rule configuration, and data ingestion must all be verified before relying on Microsoft Sentinel for detection and investigation.

Understanding AMA as part of the broader telemetry pipeline strengthens troubleshooting and supports scalable enterprise deployments.

---

# References

- Microsoft Learn – Azure Monitor Agent
- Microsoft Sentinel Documentation
- Azure Monitor Documentation
- Azure Log Analytics Documentation
- Kusto Query Language (KQL) Documentation
