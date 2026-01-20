# Logging and Monitoring Policy

## 1. Purpose
Ensure security-relevant logs are collected, centralized, and monitored to detect and investigate suspicious behavior.

## 2. Scope
Applies to:
- Sysmon + Windows Security logs
- Security Onion (Zeek, Suricata)
- Splunk ingestion, dashboards, correlation alerts
- Firewall logs (pfSense)

## 3. Policy Requirements
- Logs must be centralized in Splunk.
- Detection logic must generate alerts for defined threats.
- Logs should support investigations (timestamp, host, user, process, source IP).
- Monitoring must include:
  - Authentication events
  - Process execution events
  - DNS/network anomaly events

## 4. Evidence / Control Validation
- Splunk index + ingestion proof
- Alert screenshots
- Exported logs

## 5. Review Frequency
Monthly.

