# Audit Evidence Index – JCE Cyber Lab

> Purpose: Track evidence artifacts used to validate security controls.
> This structure mirrors audit-readiness workflows (SOC2/PCI-style).

| Evidence ID | Control Area | Description | Source System | Evidence Type | File Path | Date Collected |
|------------|--------------|-------------|---------------|--------------|----------|---------------|
| E-AC-001 | Access Control | AD password policy settings | Windows Server AD | Screenshot | `audit-evidence/access-control/ad-password-policy.png` | YYYY-MM-DD |
| E-AC-002 | Access Control | AD lockout policy settings | Windows Server AD | Screenshot | `audit-evidence/access-control/ad-lockout-policy.png` | YYYY-MM-DD |
| E-LM-001 | Logging/Monitoring | Sysmon Event ID 1 process execution | Windows 11 | Log Export | `audit-evidence/logging-monitoring/sysmon-eventid1-export.evtx` | YYYY-MM-DD |
| E-LM-002 | Logging/Monitoring | Splunk alert triggered for suspicious execution | Splunk | Screenshot | `audit-evidence/logging-monitoring/splunk-alert-sim004.png` | YYYY-MM-DD |
| E-NS-001 | Network Security | pfSense firewall rules | pfSense | Screenshot | `audit-evidence/network-security/pfsense-firewall-rules.png` | YYYY-MM-DD |
| E-NS-002 | Network Security | Zeek DNS logs for baseline/anomaly | Security Onion | Log | `audit-evidence/network-security/zeek-dns.log` | YYYY-MM-DD |

