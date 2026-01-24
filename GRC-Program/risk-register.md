# Risk Register – JCE Cyber Lab

> Purpose: Identify, prioritize, and track cybersecurity risks and mitigation efforts.

### Scoring
- Likelihood: 1 (Low) – 5 (High)
- Impact: 1 (Low) – 5 (High)
- Inherent Risk = Likelihood x Impact
- Residual Risk: risk after mitigation

| Risk ID | Risk Statement | Category | Likelihood | Impact | Inherent Risk | Existing Controls | Mitigation / Treatment Plan | Residual Risk | Status | Evidence |
|--------|-----------------|----------|------------|--------|---------------|------------------|-----------------------------|--------------|--------|---------|
| R-001 | Lack of MFA could allow credential compromise to lead to unauthorized access. | Access Control | 4 | 5 | 20 | AD Password Policy, Logging | Implement MFA (future), strengthen lockout rules | 12 | Open | Screenshot of AD policies |
| R-002 | Unpatched systems may allow exploitation of known vulnerabilities. | Vulnerability Mgmt | 3 | 5 | 15 | Update cycles, monitoring | Patch schedule + periodic vuln scanning | 8 | In Progress | Patch logs, version screenshots |
| R-003 | Excessive admin privileges may allow privilege escalation. | IAM | 4 | 4 | 16 | Least privilege efforts | Restrict admin accounts, audit group membership | 9 | Open | AD group membership screenshot |
| R-004 | Insufficient log retention could impact investigations. | Logging | 3 | 4 | 12 | Splunk indexes + alerting | Define retention requirements + storage plan | 6 | Mitigated | Splunk index settings screenshot |
| R-005 | Third-party tools (EDR agents, packages) could introduce risk if not assessed. | Vendor Risk | 3 | 3 | 9 | None | Implement VRM review checklist for tools | 5 | Open | VRM policy + checklist |
| R-006 | Phishing-based initial access may go undetected if endpoint execution telemetry or correlation fails. | Detection Engineering | 4 | 5 | 20 | Endpoint logging (4688), Sysmon, Splunk correlation | Maintain detection logic, validate quarterly via SIM-001 | 6 | Mitigated | CV-SIM001 |
| R-007 | DNS-based command-and-control or data exfiltration may bypass monitoring if DNS telemetry is unavailable or not queried correctly. | Network Monitoring | 4 | 5 | 20 | Zeek DNS logs, Hunt queries, ECS ingestion | Quarterly validation via SIM-002, maintain ECS query standards | 7 | Mitigated | CV-SIM002 |
| R-008 | Web application attacks (SQL injection) may succeed without detection if IDS coverage or placement is incorrect. | Application Security | 3 | 5 | 15 | Suricata IDS, inline monitoring | Maintain IDS rules, periodic validation via SIM-003 | 6 | Mitigated | CV-SIM003 |
| R-009 | Lack of detailed process telemetry may prevent detection of malicious execution techniques. | Endpoint Monitoring | 4 | 4 | 16 | Sysmon Event ID 1 logging | Maintain Sysmon configuration, validate baseline via SIM-004 | 5 | Mitigated | CV-SIM004 |
| R-010 | Privilege escalation may occur without detection if integrity-level monitoring or process lineage tracking fails. | Privilege Monitoring | 4 | 5 | 20 | Security 4688 + Sysmon integrity level detection | Maintain detection logic, validate quarterly via SIM-005 | 6 | Mitigated | CV-SIM005 |


