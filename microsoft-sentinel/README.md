# Microsoft Sentinel Deployment

## Executive Summary

This project documents the end-to-end deployment, configuration, validation, and operational monitoring using Microsoft Sentinel within the JCE Enterprise-Modeled Security Operations Environment.

The implementation extends the enterprise security monitoring capabilities of the environment into Microsoft Azure by integrating Microsoft Sentinel with Azure Monitor Agent (AMA), Log Analytics Workspace, Data Collection Rules (DCR), Windows Event Logs, Sysmon telemetry, and Kusto Query Language (KQL).

The project demonstrates cloud-native SIEM deployment, endpoint onboarding, centralized log collection, detection engineering, threat investigation, and security monitoring following enterprise SOC workflows and MITRE ATT&CK-aligned detection methodologies.

This deployment demonstrates practical experience deploying, configuring, validating, and operating Microsoft Sentinel within an enterprise-modeled Security Operations Center (SOC), following industry best practices for security monitoring, detection engineering, and incident investigation.

---

# Project Objectives

- Deploy Microsoft Sentinel within Microsoft Azure.
- Configure Log Analytics Workspace.
- Configure Azure Monitor Agent (AMA).
- Create Data Collection Rules (DCR).
- Onboard Windows endpoints.
- Validate Windows Security Event ingestion.
- Validate Sysmon event ingestion.
- Develop KQL detection queries.
- Investigate security events.
- Document deployment using enterprise security documentation practices.

---

# Technology Stack

## Cloud Security

- Microsoft Sentinel
- Microsoft Azure
- Azure Monitor Agent (AMA)
- Azure Log Analytics Workspace
- Data Collection Rules (DCR)

## Endpoint Monitoring

- Windows 11
- Sysmon
- Windows Security Events
- Windows Event Logs

## SIEM & Detection

- Microsoft Sentinel
- Splunk Enterprise
- Kusto Query Language (KQL)
- SPL
- MITRE ATT&CK

## Identity

- Active Directory
- Group Policy
- LDAP / LDAPS

---

# Technical Skills Demonstrated

- Microsoft Sentinel Deployment
- Azure Security Monitoring
- Endpoint Onboarding
- Windows Event Collection
- Azure Monitor Agent
- Data Collection Rules
- KQL Query Development
- SIEM Monitoring
- Detection Engineering
- Threat Detection
- Incident Investigation
- MITRE ATT&CK Mapping
- Security Operations
- Enterprise Documentation

---

# Project Documentation

The following documents provide a comprehensive guide to the deployment, configuration, operation, and validation of Microsoft Sentinel within the JCE Enterprise-Modeled Security Operations Environment.

| Document | Purpose |
|----------|---------|
| `architecture.md` | Microsoft Sentinel architecture and telemetry flow |
| `windows-onboarding.md` | Onboard Windows endpoints into Microsoft Sentinel |
| `ama-installation.md` | Deploy and validate Azure Monitor Agent (AMA) |
| `data-collection-rules.md` | Configure Azure Data Collection Rules (DCR) |
| `kql-detection-queries.md` | KQL queries for validation, detection, and threat hunting |
| `analytics-rules.md` | Configure and manage Microsoft Sentinel Analytics Rules |
| `automation-rules.md` | Automate incident response using Automation Rules and Logic Apps |
| `incident-investigation.md` | Structured SOC investigation methodology using Microsoft Sentinel |
| `threat-hunting.md` | Proactive threat hunting methodology and KQL examples |
| `mitre-attack-mapping.md` | Map detections to MITRE ATT&CK techniques |
| `validation-tests.md` | Validate deployment, telemetry, and detections |
| `cost-management.md` | Azure budgeting and Microsoft Sentinel cost optimization |

---

# Environment Overview

The Microsoft Sentinel deployment integrates with the JCE Enterprise-Modeled Security Operations Environment, providing centralized visibility across endpoint, identity, and cloud telemetry.

Primary components include:

- Microsoft Sentinel
- Azure Log Analytics Workspace
- Azure Monitor Agent
- Windows 11 Endpoint
- Sysmon
- Active Directory
- Splunk Enterprise
- Security Onion
- pfSense

---

# Validation Activities

The deployment was validated through multiple operational scenarios including:

- Windows Security Event ingestion
- Sysmon telemetry validation
- Azure Monitor Agent verification
- Log Analytics query testing
- KQL hunting queries
- Detection engineering validation
- Threat investigation workflows
- MITRE ATT&CK-aligned simulations

---

# Screenshots

This project includes screenshots demonstrating:

- Azure Resource Group
- Log Analytics Workspace
- Microsoft Sentinel
- Azure Monitor Agent
- Data Collection Rules
- Windows Event Collection
- KQL Queries
- Security Incidents
- Investigation Timeline

---

# Lessons Learned

The deployment reinforced enterprise best practices for:

- Cloud-native SIEM deployment
- Endpoint onboarding
- Detection engineering
- Threat hunting
- Security monitoring
- Documentation
- Continuous security improvement

---

# Related Projects

- Enterprise Security Operations Environment
- Splunk Enterprise SIEM
- Security Onion
- MITRE ATT&CK Detection Simulations
- Identity & Access Management
- Detection Engineering
- Governance, Risk & Compliance

---

# Repository Structure

This documentation is organized into dedicated deployment guides covering architecture, onboarding, telemetry collection, detection engineering, investigation workflows, governance, and operational validation.

Each document is intended to mirror the type of internal documentation maintained by enterprise Security Operations teams.

---

**Project Status**

✔ Deployment Completed

✔ Endpoint Onboarding Validated

✔ Windows Security Events Collected

✔ Azure Monitor Agent Configured

✔ Data Collection Rules Implemented

✔ KQL Queries Validated

✔ Detection Engineering Completed

✔ Investigation Workflows Documented

This deployment successfully demonstrates the implementation and operational validation of Microsoft Sentinel within an enterprise-modeled Security Operations Environment.
