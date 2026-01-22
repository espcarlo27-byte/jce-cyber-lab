# Asset Inventory – JCE Cyber Lab

> Purpose: Maintain a living inventory of systems within the environment, supporting governance, risk analysis, and control ownership.

| Asset Name | Type | OS / Version | IP Address | Network Zone | Owner | Business Function | Criticality | Notes |
|----------|------|--------------|------------|--------------|-------|------------------|------------|------|
| pfSense | Firewall | pfSense CE | 10.0.0.1 (Static) | WAN/LAN | JCE | Edge firewall, routing, NAT, DNS/DHCP | High | Controls segmentation + centralized DNS visibility |
| Windows 11 | Endpoint | Win11 Pro | DHCP (Dynamic) | LAN | JCE | Primary endpoint telemetry (Sysmon + Security logs) | High | Splunk Forwarder installed; detection relies on hostname/user context |
| Windows Server | Domain Controller | Server 2025 | 10.0.10.10 (Static) | LAN | JCE | AD DS, DNS (identity), authentication services | High | Access control enforcement (GPO, users, groups) |
| Splunk Server | SIEM | Ubuntu 24.04 | 10.0.10.20 (Static) | LAN | JCE | Log ingestion, correlation, and alerting | High | Receives Sysmon, Windows Security, Zeek telemetry |
| Security Onion | NSM/Sensor | SO Eval | 10.0.20.10 (Static) | Monitor | JCE | Network telemetry (Zeek/Suricata) | High | Passive monitoring via mirrored/SPAN traffic |
| Kali Linux | Attacker | Kali | DHCP (Dynamic) | LAN | JCE | Attack simulation host (controlled TTP execution) | Medium | Used only in controlled testing windows |
