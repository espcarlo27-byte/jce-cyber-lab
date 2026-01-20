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

