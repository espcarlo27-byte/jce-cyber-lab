# 🧩 Role-Based Access Control (RBAC) Model

## Executive Summary

This document defines the **Role-Based Access Control (RBAC) architecture**
implemented in the Enterprise Security Operations Environment (JCE) Active Directory environment.

The RBAC model ensures:

- Access is granted to **roles**, not individuals
- Identity context aligns with job function
- Privilege levels are clearly separated
- The directory supports **SOC investigations**, **detection engineering**, and **audit traceability**

This mirrors enterprise IAM design where role groups form the foundation of access governance.

---

## 🧠 RBAC Design Philosophy

The RBAC model follows security governance principles:

| Principle | Implementation |
|-----------|----------------|
| Least Privilege | Users only receive access required for their role |
| Separation of Duties | IT, Executives, Employees, and Contractors separated |
| Role Aggregation | Permissions assigned to groups, not user objects |
| Scalability | Placeholder roles exist for future expansion |
| Auditability | Group membership provides clear access justification |

This design enables consistent and explainable access assignments across the environment.

---

## 👥 Role Groups Overview

RBAC groups represent **job roles** or **organizational functions**.

| RBAC Group | Role Description |
|------------|------------------|
| RBAC_Executives | Executive leadership accounts |
| RBAC_Finance_Users | Finance department personnel |
| RBAC_HR_Users | Human Resources staff |
| RBAC_Sales_Users | Sales personnel |
| RBAC_Contractors | External contractor accounts |
| RBAC_IT_Helpdesk | Tier 1 IT support technicians |
| RBAC_IT_WorkstationAdmins | Endpoint administrators |
| RBAC_IT_SecurityOps | SOC analysts and security operations |

---

## 🔐 Privilege Tiering Model

The Enterprise Security Operations Environment (JCE) models **privilege separation** similar to enterprise environments.

| Tier | Scope | Example Role |
|------|------|--------------|
| Tier 0 | Domain control (not widely used in Enterprise Security Operations Environment (JCE)) | Domain Admins |
| Tier 1 | Server & infrastructure administration | RBAC_IT_ServerAdmins (reserved) |
| Tier 2 | Workstations & user support | RBAC_IT_Helpdesk, RBAC_IT_WorkstationAdmins |
| Business Users | Standard employee access | Finance, HR, Sales, Executives |
| External | Limited external access | RBAC_Contractors |

This tiering prevents privilege overlap and reduces lateral movement risk.

---

## 🔗 Role Assignment Strategy

Users are mapped to RBAC groups based on **job function**.

| Role Type | Assigned RBAC Group |
|-----------|--------------------|
| Executives | RBAC_Executives |
| Finance staff | RBAC_Finance_Users |
| HR staff | RBAC_HR_Users |
| Sales staff | RBAC_Sales_Users |
| Helpdesk technician | RBAC_IT_Helpdesk |
| Workstation administrator | RBAC_IT_WorkstationAdmins |
| SOC analyst | RBAC_IT_SecurityOps |
| Contractors | RBAC_Contractors |

No user receives direct permissions to resources without group mediation.

---

## ⚙ Relationship to Resource & Application Groups

RBAC groups represent **who the user is**.  
Resource and application groups represent **what the user can access**.

| Group Type | Purpose |
|------------|---------|
| RBAC_* | Job role identity |
| APP_* | Application administration roles |
| RES_* | File share / resource access roles |

This separation ensures clean identity governance and simplified auditing.

---

## 📊 Why RBAC Matters for Detection Engineering

RBAC allows SOC detections to leverage identity context:

| Detection Scenario | RBAC Value |
|--------------------|-----------|
| Privileged action | Identify if action matches role expectation |
| After-hours login | Contractor vs Executive risk level |
| Lateral movement | Cross-role access anomaly detection |
| Account misuse | Role-based behavioral baseline comparison |

RBAC transforms raw authentication logs into **context-rich security signals**.

---

## 📌 Implementation Notes

- Some RBAC groups exist as **reserved roles** for future scenarios
- Service accounts are not members of RBAC human role groups
- Executive, IT, Employee, and Contractor roles are deliberately separated

This structure reflects real-world identity governance practices.

---

> The RBAC model forms the foundation of identity governance in the Enterprise Security Operations Environment (JCE), supporting detection engineering, SOC operations, and audit readiness.

