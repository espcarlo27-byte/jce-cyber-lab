# Security Control Inventory – Enterprise Security Operations Environment (JCE)

> Purpose: Maintain a centralized inventory of implemented security controls,
their associated risks, validation records, and operational status.

---

## 📊 Control Inventory

| Control ID | Control Name | Control Type | Risk Mitigated | Implementation | Validation Record | Status |
|-----------|--------------|--------------|----------------|----------------|-------------------|--------|
| CTRL-001 | Endpoint Phishing Link Detection | Detective | R-006 | Windows Event 4688 logging + Splunk correlation | CV-SIM001 | Implemented & Verified |
| CTRL-002 | DNS Monitoring & Anomaly Detection | Detective | R-007 | Zeek DNS telemetry + Security Onion Hunt | CV-SIM002 | Implemented & Verified |
| CTRL-003 | Web Application IDS Detection | Detective | R-008 | Suricata IDS monitoring | CV-SIM003 | Implemented & Verified |
| CTRL-004 | Endpoint Execution Baseline Telemetry | Detective | R-009 | Sysmon Event ID 1 enriched process telemetry | CV-SIM004 | Implemented & Verified |
| CTRL-005 | Privilege Escalation Behavior Detection | Detective | R-010 | Integrity level + process lineage monitoring | CV-SIM005 | Implemented & Verified |
| CTRL-006 | Account Authentication Controls | Preventive | R-001 | AD password policy + lockout rules | AD Policy Config | Partially Implemented |
| CTRL-007 | Patch Management | Preventive | R-002 | OS updates + monitoring | Patch Logs | In Progress |
| CTRL-008 | Privileged Access Restrictions | Preventive | R-003 | Least privilege configuration | Group Membership Review | In Progress |
| CTRL-009 | Log Retention & Availability | Detective | R-004 | Splunk indexing & retention settings | Splunk Config Evidence | Implemented |
| CTRL-010 | Third-Party Tool Risk Review | Governance | R-005 | Vendor Risk Review Process | VRM Checklist | Planned |

---

## 🧠 Control Types Explained

| Type | Meaning |
|------|--------|
| Preventive | Stops security events from occurring |
| Detective | Identifies security events after they occur |
| Governance | Policies, oversight, or management processes |

---

## 🔄 Control Lifecycle

Controls should be:

1. Implemented  
2. Validated (via CV files)  
3. Periodically re-tested  
4. Updated after environment or threat changes  

---

## 🔗 Supporting Documentation

- Risk Register → Links risks to controls  
- Control Validations → Evidence of control effectiveness  
- Detection Matrix → Detection coverage  
- Issues & Resolutions → Remediation tracking  

---

## 🟢 Overall Control Posture

The Enterprise Security Operations Environment (JCE) maintains layered controls across:

- Endpoint monitoring  
- Network monitoring  
- Application monitoring  
- Privilege monitoring  
- Governance and policy  

This inventory supports audit readiness, risk management, and detection engineering maturity.
