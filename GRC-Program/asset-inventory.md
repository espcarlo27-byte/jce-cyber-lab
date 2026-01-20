# Asset Inventory – JCE Cyber Lab

> Purpose: Maintain a living inventory of systems within the environment, supporting governance, risk analysis, and control ownership.

| Asset Name | Type | OS / Version | IP Address | Network Zone | Owner | Business Function | Criticality | Notes |
|----------|------|--------------|------------|--------------|-------|------------------|------------|------|
| pfSense | Firewall | pfSense CE | 10.0.0.1 | WAN/LAN | JCE | Edge firewall, routing, DNS/DHCP | High | Controls segmentation + NAT |
| Windows 11 | Endpoint | Win11 Pro | 10.0.10.50 | LAN | JCE | Primary endpoint telemetry + Sysmon | High | Splunk Forwarder installed |
| Windows Server | Domain Controller | Server 2025 | 10.0.10.10 | LAN | JCE | AD DS, DNS, authentication | High | Access control enforcement |
| Splunk Server | SIEM | Ubuntu 24.04 | 10.0.10.20 | LAN | JCE | Log aggregation + correlation | High | Receives Sysmon, Zeek |
| Security Onion | NSM/Sensor | SO Eval | 10.0.20.10 | Monitor | JCE | Zeek/Suricata monitoring | High | Passive monitoring network |
| Kali Linux | Attacker | Kali | 10.0.10.99 | LAN | JCE | Attack simulation | Medium | Used for controlled TTPs |

