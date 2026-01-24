# CV-SIM001 — Endpoint Execution Control Validation  
**Simulation:** SIM-001 – Phishing Email (T1566.002)  
**Control Type:** Detection & Monitoring Control  
**Owner:** Carlo Espina  
**Validation Date:** 2026-01-23  

---

## 🎯 Control Objective

Ensure that endpoint process execution resulting from phishing activity is:

- Logged at the endpoint  
- Ingested into the SIEM  
- Searchable for investigation  
- Correlated into detection logic  
- Capable of generating alertable evidence  

This validates endpoint execution visibility as a security control against phishing-based initial access.

---

## ⚠️ Risk Addressed

If phishing emails cause users to execute malicious content, attackers may gain initial access, steal credentials, deliver malware, or establish persistence.

Without reliable visibility into endpoint process execution, compromise may go undetected.

---

## 🛡️ Control Implementation

### Primary Telemetry (Authoritative)
- Windows Security Event ID 4688 – Process Creation  

### Supplemental Telemetry (Supporting)
- Security Onion network telemetry (Zeek / Suricata), when available  

### SIEM Layer
- Splunk ingestion of Windows logs  
- SPL searches validating suspicious browser execution  
- Correlation logic supporting phishing detection  

---

## 🧪 Control Testing Method

The control was tested through a controlled phishing simulation:

1. Simulated phishing click event  
2. Browser execution triggered  
3. Process creation event generated  
4. Event ingested into Splunk  
5. SPL searches validated visibility  
6. Correlation logic confirmed detection capability  

---

## 📋 Evidence Artifacts

| Evidence ID | Description |
|------------|-------------|
| E-SIM001-001 | Baseline Event ID 4688 ingestion proof |
| E-SIM001-002 | Browser execution showing URL / command-line |
| E-SIM001-003 | SPL correlation search proof |

Related screenshots follow naming convention:  
`sim001-evidence-###-description.png`

---

## 🧩 Framework Alignment

**NIST CSF**
- DE.CM – Security Continuous Monitoring  
- DE.AE – Anomalies and Events  
- RS.AN – Response Analysis  

**CIS Controls**
- Control 8 – Audit Log Management  
- Control 13 – Network Monitoring and Defense  

---

## ✅ Validation Status

**Status:** ✅ Validated  
This control successfully logged, ingested, and supported detection of phishing-related execution activity with evidence.

---

## 🔁 Related Documentation

- Risk Register Entry (Phishing / Initial Access Risk)
- Detection Matrix Coverage for Endpoint Execution
- SIM-001 Technical Simulation Documentation
