# CV-SIM002 — Network Telemetry Monitoring Control Validation  
**Simulation:** SIM-002 – Network Telemetry / DNS Activity  
**Control Type:** Detection & Monitoring Control  
**Owner:** Carlo Espina  
**Validation Date:** 2026-01-24  

---

## 🎯 Control Objective

Ensure that network activity, specifically DNS-related behavior, is:

- Logged at the network monitoring layer  
- Ingested into the SIEM  
- Searchable for investigation  
- Capable of supporting detection of suspicious behavior  
- Usable as supporting evidence during incident analysis  

This validates network telemetry visibility as a control supporting detection of command-and-control activity, DNS abuse, and suspicious external communications.

---

## ⚠️ Risk Addressed

Attackers commonly use DNS and other outbound network communications for:

- Command-and-control (C2) communication  
- Data exfiltration  
- Domain-based malware callbacks  
- DNS tunneling techniques  

Without reliable network telemetry, these behaviors may go undetected even if endpoint logs exist.

---

## 🛡️ Control Implementation

### Primary Telemetry (Authoritative for this control)
- Zeek DNS logs (Security Onion)
- Suricata network alerts (when triggered)

### Supplemental Telemetry
- Firewall logs (pfSense), if applicable

### SIEM Layer
- Splunk ingestion of Zeek and/or Suricata logs  
- SPL searches validating DNS query visibility  
- Querying domains, client IPs, and query types  

---

## 🧪 Control Testing Method

The control was tested through a controlled network activity simulation:

1. Endpoint generated DNS/network traffic  
2. Traffic observed by Security Onion sensor  
3. Zeek DNS logs created  
4. Logs ingested into Splunk  
5. SPL searches confirmed DNS visibility  
6. Data validated as usable for investigation and correlation  

---

## 📋 Evidence Artifacts

| Evidence ID | Description |
|------------|-------------|
| E-SIM002-001 | Zeek DNS log ingestion proof |
| E-SIM002-002 | Example DNS query visibility in Splunk |
| E-SIM002-003 | Search results demonstrating network telemetry usefulness |

Related screenshots follow naming convention:  
`sim002-evidence-###-description.png`

---

## 🧩 Framework Alignment

**NIST CSF**
- DE.CM – Security Continuous Monitoring  
- DE.AE – Anomalies and Events  

**CIS Controls**
- Control 13 – Network Monitoring and Defense  
- Control 8 – Audit Log Management  

---

## ✅ Validation Status

**Status:** ✅ Validated  
Network telemetry logs were successfully captured, ingested, and demonstrated to support detection and investigative analysis.

---

## 🔁 Related Documentation

- Risk Register Entry (C2 / Data Exfiltration Risk)
- Detection Matrix Coverage for Network Monitoring
- SIM-002 Technical Simulation Documentation
