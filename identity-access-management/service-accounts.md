# ⚙ Service Account Governance

## Executive Summary

This document defines the governance model for **service accounts**
within the Enterprise Security Operations Environment (JCE) Active Directory environment.

Service accounts represent **machine or application identities** and are
managed separately from human users to maintain:

- Identity clarity
- Security boundary separation
- Audit traceability
- Detection reliability

This mirrors enterprise IAM practices where service identities are treated as
**privileged technical assets**, not employee accounts.

---

## 🧠 Service Account Design Principles

| Principle | Implementation |
|-----------|----------------|
| Non-Human Identity | Service accounts represent systems or applications |
| Role Separation | Not members of human RBAC role groups |
| Least Privilege | Granted only permissions required for service function |
| Traceability | Each service account has a defined purpose |
| Security Monitoring | Activity from service accounts is detectable and attributable |

---

## 🖥 Current Service Accounts

| Account | Purpose |
|---------|---------|
| svc_zimbra_ldap | Mail server ↔ Active Directory directory integration |
| svc_backup | Backup process identity (reserved for future use) |
| svc_webapp | Application service identity (reserved for future use) |

---

## 🔐 Security Controls

Service accounts follow governance controls to reduce misuse risk:

| Control Area | Governance Approach |
|--------------|---------------------|
| Interactive Login | Disabled where technically feasible |
| Privilege Scope | Not placed in human RBAC role groups |
| Password Policy | Strong passwords (rotation documented when implemented) |
| Monitoring | Activity visible in authentication logs and SIEM |
| Usage Context | Account purpose documented and justified |

---

## 📊 Why Service Accounts Matter for Detection

Service accounts behave differently from human users. Their governance enables:

| Detection Scenario | Security Insight |
|--------------------|------------------|
| After-hours login | Unusual if service account not tied to scheduled task |
| Lateral movement | Service accounts often abused in attacks |
| Privilege escalation | High-risk if service account gains new group memberships |
| Credential misuse | Unexpected logon source indicates compromise |

Separating service identities improves detection accuracy and reduces false positives.

---

## 📌 Implementation Notes

- Service accounts are intentionally excluded from RBAC human role groups.
- Their activity is distinguishable from employee logons.
- Additional service accounts will be documented as new systems are integrated.

---

> Service account governance strengthens identity hygiene and enhances the reliability of detection engineering across the Enterprise Security Operations Environment (JCE).

