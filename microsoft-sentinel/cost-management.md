# Microsoft Sentinel Cost Management

## Executive Summary

This document describes the cost management strategy implemented for Microsoft Sentinel within the JCE Enterprise-Modeled Security Operations Environment.

Microsoft Sentinel is a cloud-native SIEM built on Azure Log Analytics. Because log ingestion and data retention directly affect operating costs, a deliberate cost management strategy was established before expanding telemetry collection.

The objective is to balance security visibility with responsible cloud resource management while maintaining an enterprise-oriented deployment.

---

# Objectives

- Monitor Azure spending throughout the project.
- Prevent unexpected cloud charges.
- Collect only necessary security telemetry.
- Optimize data ingestion.
- Build scalable monitoring practices suitable for enterprise environments.

---

# Environment

| Component | Configuration |
|-----------|---------------|
| Cloud Platform | Microsoft Azure |
| SIEM | Microsoft Sentinel |
| Resource Group | RG-JCE-SOC |
| Log Analytics Workspace | LAW-JCE-SOC |
| Pricing Model | Pay-As-You-Go |
| Budget Monitoring | Azure Cost Management |

---

# Cost Considerations

Microsoft Sentinel costs are primarily influenced by:

- Log ingestion volume
- Data retention period
- Connected data sources
- Number of monitored endpoints
- Analytics processing

Understanding these factors allows telemetry collection to be planned appropriately without sacrificing visibility.

---

# Cost Management Strategy

The following practices were implemented throughout the project.

## Budget Creation

An Azure Budget was created to monitor spending within the Resource Group.

Objectives included:

- Track projected monthly costs.
- Receive spending notifications.
- Detect unexpected increases.
- Maintain project affordability.

---

## Alert Thresholds

Budget notifications were configured at multiple spending thresholds.

| Threshold | Purpose |
|-----------|---------|
| 50% | Early awareness |
| 75% | Review telemetry collection |
| 90% | Investigate increased usage |
| 100% | Immediate review and corrective action |

---

## Controlled Data Collection

Telemetry collection was intentionally limited to security-relevant data.

Examples include:

- Windows Security Event Logs
- Authentication events
- Account management events
- Process creation events
- Planned Sysmon telemetry

Collecting only necessary logs helps reduce ingestion costs while maintaining investigative value.

---

## Incremental Expansion

Rather than onboarding numerous systems simultaneously, the environment was expanded gradually.

Deployment progression:

1. Azure environment
2. Microsoft Sentinel
3. Windows endpoint
4. Azure Monitor Agent
5. Data Collection Rule
6. Security event validation
7. Detection queries
8. Additional telemetry sources

This phased approach simplifies troubleshooting while minimizing unnecessary costs.

---

# Monitoring Usage

Azure Cost Management provides visibility into:

- Current spending
- Forecasted spending
- Daily costs
- Resource Group costs
- Log Analytics usage
- Budget status

Regular review of these metrics helps identify opportunities to optimize telemetry collection.

---

# Optimization Practices

The following practices help control operating costs:

- Collect only required logs.
- Avoid unnecessary diagnostic settings.
- Regularly review Data Collection Rules.
- Remove unused resources.
- Validate telemetry before expanding collection.
- Monitor daily ingestion trends.
- Enable additional telemetry only when justified.

---

# Scalability Considerations

As the environment grows, additional cost controls may include:

- Multiple Data Collection Rules for different endpoint groups.
- Tiered data retention policies.
- Selective Sysmon configuration.
- Custom log filtering.
- Scheduled reviews of ingestion volume.

These practices support larger deployments while maintaining financial control.

---

# Lessons Learned

Cost management is an essential part of operating a cloud-native SIEM. Planning for budget monitoring before expanding telemetry collection reduces operational risk and encourages disciplined engineering practices.

Balancing visibility with cost ensures that Microsoft Sentinel remains both effective and sustainable as additional endpoints and telemetry sources are introduced.

---

# References

- Microsoft Sentinel Pricing Documentation
- Azure Cost Management Documentation
- Azure Monitor Documentation
- Log Analytics Pricing Documentation

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
