# 🕵️ Identity-Driven Detection Use Cases

## Executive Summary

This document outlines detection engineering use cases enabled by structured IAM:
Titles, Departments, RBAC groups, and service account governance provide
investigation context that improves fidelity and reduces false positives.

---

## 🧠 Detection Philosophy

Identity context transforms “an event happened” into:

- Who did it?
- Is this normal for their role?
- Does the access match their department?
- Is this a service account behaving like a human?

---

## 🔍 Use Cases Enabled by RBAC + Identity Context

| Scenario | Why IAM Helps |
|----------|----------------|
| Executive account logon anomalies | Exec identities carry higher risk |
| Contractor after-hours logons | Contractors should have narrower access patterns |
| Role-to-access mismatch | Finance user attempting IT admin actions is suspicious |
| Privileged group change monitoring | Tracks escalation into high-impact groups |
| Service account interactive logon | Often indicates misuse or compromise |

---

## 📌 Examples of “High Signal” Context

- RBAC_IT_SecurityOps user logging into finance endpoints at odd hours
- Contractor account attempting lateral movement to privileged assets
- Service account authenticating from unexpected host / workstation subnet

---

> Identity governance makes detections more explainable, auditable, and behavior-aligned.
