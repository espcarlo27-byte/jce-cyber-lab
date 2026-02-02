# 🧾 Delegation Model (Least Privilege)

## Executive Summary

This document defines Active Directory delegation boundaries used to model
enterprise least-privilege administration. Delegation is applied so that
support roles can perform operational tasks without receiving domain-wide control.

---

## 🧠 Delegation Principles

| Principle | Implementation |
|-----------|----------------|
| Least Privilege | Only required rights are delegated |
| Separation of Duties | Helpdesk ≠ Admins ≠ Domain Admins |
| Scoped Delegation | Rights constrained to specific OUs |
| Auditability | Delegated tasks are explainable and reviewable |

---

## 👥 Delegation Targets

| Role Group | Delegated Capability | Scope (OU) |
|------------|----------------------|------------|
| RBAC_IT_Helpdesk | Reset passwords, unlock accounts | Employees / Contractors OUs |
| RBAC_IT_WorkstationAdmins | Join computers, manage workstation objects | Workstations OU |
| RBAC_IT_ServerAdmins | Manage server objects (future) | Servers OU |

---

## ✅ Recommended Guardrails

- Helpdesk can reset passwords but cannot:
  - modify group memberships
  - create privileged accounts
  - edit GPOs
- Service accounts are excluded from delegated administrative roles
- Domain Admin usage is minimized and reserved for Tier 0 tasks

---

> Delegation strengthens governance and reduces blast radius during identity compromise.
