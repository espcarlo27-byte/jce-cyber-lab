# Microsoft Sentinel Validation Tests

## Executive Summary

This document describes the validation procedures performed to verify the successful deployment and operation of Microsoft Sentinel within the JCE Enterprise-Modeled Security Operations Environment.

Each validation test confirms that telemetry is successfully collected, ingested, queried, and available for detection and investigation.

The objective is to ensure that the Microsoft Sentinel deployment functions as expected before implementing analytics rules, automation, and incident response workflows.

---

# Validation Objectives

- Verify Azure resource deployment.
- Confirm Azure Monitor Agent (AMA) functionality.
- Validate Data Collection Rule (DCR) assignment.
- Verify Windows Security Event ingestion.
- Confirm Log Analytics connectivity.
- Validate Microsoft Sentinel visibility.
- Test KQL query execution.

---

# Environment

| Component | Status |
|-----------|--------|
| Resource Group | RG-JCE-SOC |
| Log Analytics Workspace | LAW-JCE-SOC |
| Microsoft Sentinel | Enabled |
| Azure Monitor Agent | Installed |
| Data Collection Rule | Assigned |
| Endpoint | JCE-WIN11-01 |

---

# Validation Workflow

```
Deploy Resources
        │
        ▼
Install AMA
        │
        ▼
Assign DCR
        │
        ▼
Generate Windows Activity
        │
        ▼
Verify Log Analytics
        │
        ▼
Run KQL Queries
        │
        ▼
Validate Detection Visibility
```

---

# Validation Test 1 — Azure Resources

## Objective

Verify required Azure resources exist.

### Validation

Confirmed:

- Resource Group
- Log Analytics Workspace
- Microsoft Sentinel
- Data Collection Rule

### Expected Result

All Azure resources deployed successfully.

### Status

✅ Passed

---

# Validation Test 2 — Azure Monitor Agent

## Objective

Verify AMA deployment.

### Validation

Confirmed:

- Agent installation
- Endpoint connectivity
- Healthy status

### Expected Result

Azure Monitor Agent reports successfully.

### Status

✅ Passed

---

# Validation Test 3 — Data Collection Rule

## Objective

Verify endpoint association.

### Validation

Confirmed:

- Correct DCR assignment
- Security Event collection enabled

### Expected Result

Endpoint receives DCR configuration.

### Status

✅ Passed

---

# Validation Test 4 — Windows Security Events

## Objective

Verify Windows Security Event ingestion.

Example query:

```kql
SecurityEvent
| summarize Count=count() by EventID
| order by Count desc
```

### Expected Result

Security events returned successfully.

### Status

✅ Passed

---

# Validation Test 5 — Recent Events

```kql
SecurityEvent
| where TimeGenerated >= ago(1h)
| take 20
```

### Expected Result

Recent Windows Security Events displayed.

### Status

✅ Passed

---

# Validation Test 6 — Failed Logons

```kql
SecurityEvent
| where EventID == 4625
```

### Expected Result

Failed authentication events appear after generating test activity.

### Status

✅ Passed

---

# Validation Test 7 — Successful Logons

```kql
SecurityEvent
| where EventID == 4624
```

### Expected Result

Successful logon events returned.

### Status

✅ Passed

---

# Validation Test 8 — Process Creation

```kql
SecurityEvent
| where EventID == 4688
```

### Expected Result

Process creation events successfully collected.

### Status

✅ Passed

---

# Validation Test 9 — Sysmon (When Enabled)

```kql
WindowsEvent
| where Provider == "Microsoft-Windows-Sysmon"
```

### Expected Result

Sysmon telemetry appears after configuration.

### Status

🟡 Planned

---

# Validation Test 10 — Microsoft Sentinel

## Objective

Verify Microsoft Sentinel can consume collected telemetry.

Validation included:

- Log visibility
- Query execution
- Incident support
- Hunting capability

### Expected Result

Sentinel successfully accesses Log Analytics data.

### Status

✅ Passed

---

# Validation Summary

| Test | Result |
|------|--------|
| Azure Resources | ✅ Passed |
| AMA Deployment | ✅ Passed |
| DCR Assignment | ✅ Passed |
| Security Events | ✅ Passed |
| Recent Events | ✅ Passed |
| Failed Logons | ✅ Passed |
| Successful Logons | ✅ Passed |
| Process Creation | ✅ Passed |
| Sysmon Validation | 🟡 Planned |
| Sentinel Visibility | ✅ Passed |

---

# Lessons Learned

Validation is a critical phase of any Microsoft Sentinel deployment. Successfully deploying Azure resources does not guarantee operational visibility. Each component—including Azure Monitor Agent, Data Collection Rules, Log Analytics, and Microsoft Sentinel—must be validated to ensure telemetry flows correctly through the monitoring pipeline.

Performing structured validation before creating analytics rules reduces troubleshooting time and increases confidence in future detections and investigations.

---

# References

- Microsoft Sentinel Documentation
- Azure Monitor Documentation
- Azure Log Analytics Documentation
- Microsoft Learn

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
