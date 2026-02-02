# 🔐 Identity & Access Management (IAM)

## Executive Summary

The Identity & Access Management (IAM) section of the JCE Cyber Lab demonstrates
the design and implementation of a **role-based Active Directory identity model**
that supports **security operations, detection engineering, and governance**.

This IAM structure provides:

- Role-based access control (RBAC)
- Departmental identity context
- Executive and contractor separation
- IT privilege tiering
- Service account governance

The directory is intentionally structured to mirror **enterprise identity architecture**,
where access is granted to **roles**, not individuals.

---

## 🧠 IAM Design Philosophy

This lab follows **enterprise identity governance principles**, where:

| Principle | Implementation |
|-----------|----------------|
| Least Privilege | Users placed into role groups, not given direct permissions |
| Separation of Duties | IT, Executives, Employees, Contractors separated |
| Role-Based Access | RBAC groups define access boundaries |
| Identity Context | Title + Department assigned to all human users |
| Service Account Isolation | svc\_* accounts excluded from human RBAC groups |

This design enables detections and investigations to incorporate **identity context**, not just technical events.

---

## 👤 User Identity Structure

All human users include **Title** and **Department** attributes to simulate
real corporate identity directories used in SOC and GRC environments.

| Category | Examples |
|----------|----------|
| Executives | CEO, CFO, CTO |
| Finance | Payroll, Accounting |
| HR | HR staff and managers |
| Sales | Sales representatives |
| IT Roles | Helpdesk, Workstation Admin, SOC Analyst |
| Contractors | External users with restricted role group |

---

## 👥 RBAC Group Model

Access is assigned using **Role-Based Access Control (RBAC)**.

| RBAC Group | Purpose |
|------------|---------|
| RBAC_Executives | Executive leadership |
| RBAC_Finance_Users | Finance department |
| RBAC_HR_Users | HR department |
| RBAC_Sales_Users | Sales department |
| RBAC_IT_Helpdesk | Tier 1 IT support |
| RBAC_IT_WorkstationAdmins | Endpoint administrators |
| RBAC_IT_SecurityOps | SOC analysts |
| RBAC_Contractors | External contractors |

Additional groups exist as **reserved roles** to model scalable enterprise IAM.

---

## ⚙ Service Accounts

Service accounts are governed separately from human identities.

| Account | Purpose |
|--------|---------|
| svc_zimbra_ldap | Mail ↔ AD integration |
| svc_backup | Backup services (future) |
| svc_webapp | Application identity (future) |

Service accounts:

- Do not receive human job titles
- Are excluded from RBAC role groups
- Represent machine/service authentication

---

## 🧩 Why IAM Matters for Detection Engineering

Identity context enhances security detections:

| Scenario | Identity Value |
|----------|----------------|
| After-hours login | Contractor vs Executive risk difference |
| Privileged action | IT admin vs Finance user anomaly |
| Lateral movement | Role-based access deviation |
| Phishing | Department-based targeting analysis |

IAM transforms raw logs into **behavior-aware security telemetry**.

---

## 📤 Evidence Generation

IAM documentation is backed by reproducible exports:

- `ad_users_FINAL.csv`
- `ad_groups_FINAL.csv`
- `group_membership_FINAL.csv`

Export commands are documented to maintain audit traceability.

---

## 🧭 IAM Section Navigation

This IAM section is structured to mirror enterprise identity governance documentation.  
Each document focuses on a specific component of identity architecture, access control, or evidence generation.

---

### 🔍 Core Identity References

- 👤 **[AD User Inventory](ad-user-inventory.md)** – Human identities, roles, and departmental structure  
- 👥 **[AD Group Inventory](ad-group-inventory.md)** – RBAC, application, and resource group definitions  
- 🧩 **[RBAC Model](rbac-model.md)** – Role-based access control architecture and privilege tiering  
- ⚙ **[Service Account Governance](service-accounts.md)** – Non-human identity controls and monitoring

---

### 🏗 Identity Architecture & Governance

- 🗺 **[IAM Topology Diagram](iam-topology.md)** – Visual overview of users, RBAC groups, service accounts, and resource relationships  
- 🧾 **[Delegation Model](delegation-model.md)** – Least-privilege administrative delegation boundaries  
- 🕵️ **[Identity-Driven Detection Use Cases](identity-detection-usecases.md)** – How identity context improves SOC detections

---

### 📤 Evidence & Validation

- 📁 **[Export Commands](export-commands.md)** – Reproducible AD evidence generation  
- CSV evidence files (generated via documented commands):
  - `ad_users_FINAL.csv`
  - `ad_groups_FINAL.csv`
  - `group_membership_FINAL.csv`

---

This structure separates identity architecture into clear layers:

| Layer | Purpose |
|------|---------|
| Identity Structure | Users + groups |
| Access Control Design | RBAC + delegation |
| Security Operations | Detection use cases |
| Governance Evidence | Exports and validation |

> This IAM model demonstrates enterprise-style identity governance supporting SOC operations, GRC traceability, and detection engineering workflows.

