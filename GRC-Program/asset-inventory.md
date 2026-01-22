# Asset Inventory – JCE Cyber Lab

> Purpose: Maintain a living inventory of systems within the environment, supporting governance, risk analysis, and control ownership.

> 📌 **Authoritative Reference:**  
> IP addressing strategy (DHCP vs Static) is defined in **[`architecture/network-topology.md`](../architecture/network-topology.md)**.  
> This asset inventory mirrors that source to ensure consistency across governance, risk tracking, and audit evidence.

| Asset Name | Hostname | Type | OS / Version | IP Address | Network Zone | Owner | Business Function | Criticality | Notes |
|----------|----------|------|--------------|------------|--------------|-------|------------------|------------|------|
| pfSense | pfSense | Firewall | pfSense CE | 10.0.0.1 (Static) | WAN/LAN | JCE | Edge firewall, routing, NAT, DNS resolver, DHCP | High | Central choke point; traffic mirroring to Security Onion |
| Windows Server 2025 (AD) | DC01 | Domain Controller | Server 2025 | 10.0.0.10 (Static) | LAN | JCE | Identity & authentication services (AD DS / GPO); Splunk Forwarder | High | Static required for stable identity services and predictable log correlation |
| Security Onion (Eval) | SecurityOnion | NSM/Sensor | Security Onion Eval | 10.0.0.11 (Static) | Monitor | JCE | Passive monitoring (Zeek/Suricata), ECS telemetry | High | Static for sensor management; non-inline deployment |
| Ubuntu Server (Splunk Enterprise) | Splunk | SIEM | Ubuntu Server | DHCP (Dynamic) | LAN | JCE | Central log ingestion, correlation, dashboards, alerting | High | DHCP by design to simulate flexible SOC infrastructure |
| Windows 11 Endpoint | Windows11Pro | Endpoint | Win11 Pro | DHCP (Dynamic) | LAN | JCE | Primary endpoint telemetry (Sysmon + Security logs) | High | Splunk Forwarder installed; detections rely on hostname + user context |
| Kali Linux | Kali | Attacker | Kali | DHCP (Dynamic) | LAN | JCE | Attack simulation host (controlled adversary TTP execution) | Medium | Ephemeral attacker modeling; non-persistent by design |
