# Asset Inventory – Enterprise Security Operations Environment (JCE)

> Purpose: Maintain a living inventory of systems within the environment, supporting governance, risk analysis, and control ownership.

> 📌 **Authoritative Reference:**  
> IP addressing strategy (DHCP vs Static) is defined in **[`architecture/network-topology.md`](../architecture/network-topology.md)**.  
> This asset inventory mirrors that source to ensure consistency across governance, risk tracking, and audit evidence.

| Asset Name | Hostname | Type | OS / Version | IP Address | Network Zone | Owner | Business Function | Security Role | Criticality | Notes |
|----------|----------|------|--------------|------------|--------------|-------|------------------|---------------|------------|------|
| pfSense | pfSense.local.lab | Firewall / Gateway | pfSense CE | 10.0.0.1 (Static) | WAN/LAN | JCE | Network perimeter control, routing, NAT, DNS resolution, DHCP services, and traffic visibility point | Preventive + Detective | High | Central network choke point; supports segmentation and DNS monitoring; traffic mirrored to Security Onion |
| Windows Server 2025 (AD) | winserver2025 | Domain Controller | Server 2025 | 10.0.0.10 (Static) | LAN | JCE | Identity and access management platform (AD DS, authentication, authorization, GPO enforcement) | Preventive + Governance | High | Core identity authority; compromise impacts entire environment |
| Security Onion (Eval) | securityonion | NSM / Sensor | Security Onion Eval | 10.0.0.11 (Static) | Monitor | JCE | Network security monitoring platform providing Zeek telemetry, Suricata IDS alerts, and packet-level visibility | Detective | High | Primary network detection sensor; loss reduces visibility into C2, web attacks, and anomalies |
| Ubuntu Server (Splunk Enterprise) | ubuntu24.04 | SIEM Platform | Ubuntu Server | DHCP (Dynamic) | LAN | JCE | Centralized security monitoring platform responsible for log ingestion, event correlation, dashboards, alerting, and investigative search | Detective | High | Security monitoring brain; DHCP by design to simulate flexible SOC infrastructure |
| Windows 11 Endpoint | windows11pro | Endpoint / Log Source | Win11 Pro | DHCP (Dynamic) | LAN | JCE | User workstation and primary host telemetry source (Sysmon + Security logs) for execution and privilege monitoring | Detective (Telemetry Source) | High | Critical detection data source; detections rely on hostname and user context rather than static IP |
| Kali Linux | kali | Adversary Simulation | Kali | DHCP (Dynamic) | LAN | JCE | Controlled adversary emulation system used to generate attack telemetry and validate security controls | Testing / Validation | Medium | Ephemeral attacker modeling; not production but required for control validation |

---

## 🔐 Security Role Definitions

| Role | Meaning |
|------|--------|
| Preventive | Stops security incidents before they occur (e.g., firewall, identity controls) |
| Detective | Detects suspicious or malicious activity after it occurs (e.g., SIEM, IDS) |
| Governance | Enforces policy, identity, or security management structures (e.g., Active Directory) |
| Testing / Validation | Used to simulate attacks and validate that controls function correctly (e.g., Kali Linux) |
| Telemetry Source | Generates security logs that support detection (e.g., endpoints) |

---

## 🔴 Why Asset Criticality Matters

Asset criticality reflects how important a system is to the **security posture, business operations, and incident response capability** of the environment.

It helps prioritize:

- Security monitoring focus  
- Patch urgency  
- Backup and recovery planning  
- Incident response escalation  
- Access control strictness  

If a high-criticality asset fails or is compromised, the **impact to security visibility or operational capability is significant**.

---

### 📊 Criticality Levels

| Level | Meaning | Security Impact Example |
|------|---------|--------------------------|
| **High** | Essential to security monitoring, identity control, or core infrastructure | SIEM failure results in loss of detection and alerting capability |
| **Medium** | Supports operations but does not directly control security visibility | Internal application server with limited sensitive data |
| **Low** | Non-critical or easily replaceable systems | Test workstation with no privileged access |

---

### 🧠 How Criticality Is Determined

Criticality is based on:

1. **Security Function** – Does this system detect, prevent, or respond to threats?  
2. **Data Sensitivity** – Does it process credentials, logs, or sensitive data?  
3. **Operational Dependency** – Do other systems depend on it?  
4. **Impact of Failure** – What happens if it goes offline or is compromised?

---

### 🛡 Example from the Enterprise Security Operations Environment (JCE)

The **Ubuntu Server (Splunk Enterprise)** is marked **High Criticality** because:

- It is the central log ingestion platform  
- Detection correlation depends on it  
- Alerts originate from it  
- Incident investigations rely on its data  

If unavailable, **security visibility is severely reduced**, directly impacting detection and response capability.

---

### 🎯 Why This Matters in GRC

Criticality helps align:

**Assets → Risks → Controls → Monitoring Priority**

This ensures high-impact systems receive:

- Stronger monitoring  
- Faster remediation  
- More frequent validation  
- Stricter access control  

Documenting criticality supports risk-based decision-making and audit readiness.
