# Asset Inventory – JCE Cyber Lab

> Purpose: Maintain a living inventory of systems within the environment, supporting governance, risk analysis, and control ownership.

> 📌 **Authoritative Reference:**  
> IP addressing strategy (DHCP vs Static) is defined in **[`architecture/network-topology.md`](../architecture/network-topology.md)**.  
> This asset inventory mirrors that source to ensure consistency across governance, risk tracking, and audit evidence.

| Asset Name | Hostname | Type | OS / Version | IP Address | Network Zone | Owner | Business Function | Criticality | Notes |
|----------|----------|------|--------------|------------|--------------|-------|------------------|------------|------|
| pfSense | pfSense.local.lab | Firewall | pfSense CE | 10.0.0.1 (Static) | WAN/LAN | JCE | Edge firewall, routing, NAT, DNS resolver, DHCP | High | Central choke point; traffic mirroring to Security Onion |
| Windows Server 2025 (AD) | winserver2025 | Domain Controller | Server 2025 | 10.0.0.10 (Static) | LAN | JCE | Identity & authentication services (AD DS / GPO); Splunk Forwarder | High | Static required for stable identity services and predictable log correlation |
| Security Onion (Eval) | securityonion | NSM/Sensor | Security Onion Eval | 10.0.0.11 (Static) | Monitor | JCE | Passive monitoring (Zeek/Suricata), ECS telemetry | High | Static for sensor management; non-inline deployment |
| Ubuntu Server (Splunk Enterprise) | ubuntu24.04 | SIEM | Ubuntu Server | DHCP (Dynamic) | LAN | JCE | Central log ingestion, correlation, dashboards, alerting | High | DHCP by design to simulate flexible SOC infrastructure |
| Windows 11 Endpoint | windows11pro | Endpoint | Win11 Pro | DHCP (Dynamic) | LAN | JCE | Primary endpoint telemetry (Sysmon + Security logs) | High | Splunk Forwarder installed; detections rely on hostname + user context |
| Kali Linux | kali | Attacker | Kali | DHCP (Dynamic) | LAN | JCE | Attack simulation host (controlled adversary TTP execution) | Medium | Ephemeral attacker modeling; non-persistent by design |

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

### 🛡 Example from the JCE Cyber Lab

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
