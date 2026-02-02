# 👤 Active Directory User Inventory

## Executive Summary

This document provides the **authoritative inventory of human identities**
within the JCE Cyber Lab Active Directory environment.

The user inventory demonstrates:

- Role-based identity structuring
- Departmental attribution
- Executive and contractor separation
- IT privilege role distinction
- Alignment with enterprise identity governance practices

All human users include **Title** and **Department** attributes to provide
identity context used in SOC investigations and audit reviews.

---

## 🧠 Identity Governance Principles

The directory is structured to reflect real-world identity management:

| Principle | Implementation |
|-----------|----------------|
| Role Context | Titles describe job function |
| Organizational Context | Departments represent business unit |
| Identity Separation | Employees, Executives, IT, Contractors separated |
| Audit Readiness | User attributes support access review processes |
| Detection Context | Role and department improve SOC analysis |

---

## 🏢 Executive Accounts

| SamAccountName | Display Name | Title | Department |
|----------------|-------------|-------|------------|
| ceo.matt | Matt Carter | Chief Executive Officer | Executive |
| cfo.linda | Linda Reyes | Chief Financial Officer | Finance |
| cto.david | David Nguyen | Chief Technology Officer | Executive |

---

## 💼 Finance Department

| SamAccountName | Display Name | Title | Department |
|----------------|-------------|-------|------------|
| fin.account1 | James Wilson | Finance Staff | Finance |
| fin.payroll1 | Amy Lopez | Finance Staff | Finance |
| fin.payroll2 | Brian Singh | Finance Staff | Finance |

---

## 🧑‍💼 Human Resources

| SamAccountName | Display Name | Title | Department |
|----------------|-------------|-------|------------|
| hr.manager1 | Sarah Jones | HR Staff | Human Resources |
| hr.rep1 | Omar Hassan | HR Staff | Human Resources |

---

## 💰 Sales Department

| SamAccountName | Display Name | Title | Department |
|----------------|-------------|-------|------------|
| sales.rep1 | Sarah Kim | Sales Representative | Sales |
| sales.rep2 | Kevin Park | Sales Representative | Sales |

---

## 🖥 IT Role Accounts

| SamAccountName | Display Name | Title | Department |
|----------------|-------------|-------|------------|
| it.helpdesk1 | Dana Miller | Helpdesk Technician | IT |
| it.admin1 | Chris Baker | IT Administrator | IT |
| it.soc1 | Jack Moreno | Security Operations Analyst | IT |

These accounts represent **role-based IT responsibilities** and are used
to model privilege tiering and SOC workflows.

---

## 👷 Contractor Accounts

| SamAccountName | Display Name | Title | Department |
|----------------|-------------|-------|------------|
| contract.jdoe | Jordan Doe | Contractor | Contractors |
| contract.msingh | Mina Singh | Contractor | Contractors |

Contractor accounts are segregated from employees and controlled via
dedicated RBAC groups.

---

## ⚙ Service Accounts (Non-Human Identities)

Service accounts support system integrations and automation. These are not human users.

| Account | Purpose |
|---------|---------|
| svc_zimbra_ldap | Mail ↔ AD directory integration |
| svc_backup | Backup services (future) |
| svc_webapp | Application service identity (future) |

Service accounts:
- Do not receive job titles
- Are excluded from human RBAC role groups
- Represent machine authentication

---

## 📊 Why This Inventory Matters

User identity attributes enhance detection and governance:

| Use Case | Identity Value |
|----------|----------------|
| Incident response | Identify user role during investigation |
| Access reviews | Validate role-to-access alignment |
| Insider threat | Compare behavior against job function |
| Audit | Demonstrate structured identity governance |

This inventory provides the human context required to interpret authentication and access events.

---

> This user inventory demonstrates enterprise-style identity governance and supports SOC operations, GRC processes, and detection engineering workflows.

