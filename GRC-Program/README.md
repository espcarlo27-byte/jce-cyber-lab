# GRC Program – Governance, Risk, and Compliance (Enterprise Security Operations Environment (JCE))

## 🎯 Purpose

This folder documents the **Governance, Risk, and Compliance (GRC)** layer of the Enterprise Security Operations Environment (JCE).
While the simulation folders validate detection engineering and SOC workflows, this GRC program ensures:

- Security controls are **documented**
- Risks are **tracked and prioritized**
- Controls are **mapped to a framework** (NIST CSF)
- Evidence is **collected and audit-ready**
- Policies and standards are **defined and enforced**

This creates an enterprise-style security program that aligns with real-world environments such as:
**PCI DSS**, **SOC 2**, and general governance best practices.

---

## 📌 Program Components

- ✅ **Asset Inventory** → `asset-inventory.md`
- ✅ **Risk Register** → `risk-register.md`
- ✅ **Control Mapping (NIST CSF)** → `control-mapping-nist-csf.md`
- ✅ **Policies** → `/policies/`
- ✅ **Audit Evidence Index** → `/audit-evidence/evidence-index.md`

---

## 🔍 How This Maps to Real Compliance

This lab uses technical controls and evidence sources such as:

- pfSense firewall rules
- Windows security logs + Sysmon
- Splunk dashboards & alerts
- Security Onion (Zeek + Suricata)
- Endpoint telemetry and investigation artifacts

The output is structured like a compliance or audit environment:
controls are validated with repeatable evidence.

