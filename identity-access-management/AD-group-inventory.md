# 👥 Active Directory Group Inventory

## Executive Summary

This document provides the **authoritative inventory of security groups**
used to implement **Role-Based Access Control (RBAC)** and identity governance
within the JCE Cyber Lab Active Directory environment.

Group structure is designed to be:

- Role-based (RBAC_* groups)
- Application-aware (APP_* groups)
- Resource-ready (RES_* groups)
- Scalable for future simulations and expansions

This mirrors enterprise IAM practice where access is governed by **groups**, not individuals.

---

## 🧠 Group Governance Philosophy

| Principle | Implementation |
|-----------|----------------|
| Role-Based Access | Users assigned to RBAC groups based on job function |
| Separation of Concerns | RBAC ≠ Application Admin ≠ Resource Access |
| Least Privilege | Membership is intentional and auditable |
| Scalability | Reserved groups exist for future scenarios |
| Auditability | Group membership explains access decisions |

---

## 👤 RBAC Role Groups (Human Identity Roles)

RBAC groups represent **who a user is** (role/department), not what they can access.

| Group | Purpose | Membership Notes |
|------|---------|------------------|
| RBAC_Executives | Executive leadership users | CEO/CFO/CTO |
| RBAC_Finance_Users | Finance department users | Payroll + accounting |
| RBAC_HR_Users | HR department users | HR staff and managers |
| RBAC_Sales_Users | Sales department users | Sales reps |
| RBAC_Contractors | External contractor identities | Contractor accounts only |
| RBAC_IT_Helpdesk | Tier 1 support role | Reset/unlock/user support scope |
| RBAC_IT_WorkstationAdmins | Endpoint administration role | Workstation support/admin |
| RBAC_IT_SecurityOps | SOC analyst role | Security operations visibility |
| RBAC_IT_ServerAdmins | Server administration role | **Reserved** (future) |
| RBAC_Marketing_Users | Marketing role group | **Reserved** (future) |
| RBAC_Operations_Users | Operations role group | **Reserved** (future) |

> 📌 Reserved groups exist intentionally to keep the IAM model scalable and consistent as the lab expands.

---

## 🧩 Application Administration Groups (APP_*)

Application groups represent **administrative roles inside tools**, not job functions.

| Group | Purpose | Status |
|------|---------|--------|
| APP_Zimbra_Admins | Mail platform administration | Optional / as-needed |
| APP_Splunk_Admins | SIEM administration | Optional / as-needed |

---

## 🗂 Resource Access Groups (RES_*)

Resource groups represent **access to shares/folders/resources**, not human roles.

| Group | Purpose | Status |
|------|---------|--------|
| RES_Finance_Share_RW | Finance resource read/write access | Not yet mapped |
| RES_HR_Share_RW | HR resource read/write access | Not yet mapped |

> When file shares are introduced, RES_* groups become the security boundary for NTFS/share permissions.

---

## 🔗 Recommended Enterprise Mapping Pattern

This lab follows an enterprise-friendly mapping:

- **RBAC group** (role) → nested into → **RES group** (resource access)

Example:
- RBAC_Finance_Users → member of → RES_Finance_Share_RW

This avoids one-off per-user access and keeps permissions explainable.

---

> Group inventory supports audit-ready access reviews and detection engineering context throughout the JCE Cyber Lab.
