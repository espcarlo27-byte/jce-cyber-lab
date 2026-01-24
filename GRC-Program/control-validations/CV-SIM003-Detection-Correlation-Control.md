# CV-SIM003 — Web Application IDS Detection Control Validation  
**Simulation:** SIM-003 – SQL Injection (T1190)  
**Control Type:** Network Intrusion Detection Control  
**Owner:** Carlo Espina  
**Validation Date:** 2026-01-24  

---

## 🎯 Control Objective

Ensure that malicious web application activity, specifically SQL injection attempts against a vulnerable web application, can be:

- Observed at the network monitoring layer  
- Detected by IDS signatures  
- Logged as security-relevant events  
- Used as investigative evidence  

This validates IDS-based visibility for web application exploitation attempts.

---

## ⚠️ Risk Addressed

Attackers exploit public-facing applications using SQL injection to:

- Bypass authentication  
- Access sensitive data  
- Extract credentials  
- Compromise backend databases  

Without network-based application-layer monitoring, these attacks may succeed without detection.

---

## 🛡️ Control Implementation

### Primary Telemetry (Authoritative for this control)

- **Security Onion (Suricata IDS)**  
  Detection of web-based attack patterns and SQL injection-related behavior

### Supplemental Telemetry

- Zeek HTTP logs (request visibility context)

### SIEM Layer

- ❌ IDS-to-SIEM ingestion not implemented by design  
- SIEM correlation excluded and documented as a known visibility gap  

---

## 🧪 Control Testing Method

The control was validated through a controlled SQL injection simulation:

1. SQL injection payloads submitted to DVWA application  
2. Application executed injected SQL logic  
3. HTTP traffic traversed inline Security Onion sensor  
4. Suricata generated a web policy alert  
5. Alert source/destination IPs verified  
6. Detection confirmed at IDS layer  

---

## 📋 Evidence Artifacts

| Evidence ID | Description |
|------------|-------------|
| E-SIM003-001 | Suricata SQL injection alert proof |
| E-SIM003-002 | Zeek HTTP log request visibility |
| E-SIM003-003 | Alert metadata showing source/destination IPs |

Related screenshots follow naming convention:  
`sim003-evidence-###-description.png`

---

## 🧩 Framework Alignment

**NIST CSF**
- DE.CM – Security Continuous Monitoring  
- DE.AE – Anomalies and Events  

**CIS Controls**
- Control 13 – Network Monitoring and Defense  
- Control 16 – Application Software Security  

---

## ⚠️ Known Limitation (Documented Risk)

IDS alerts were **not ingested into SIEM** due to intentional lab design.  
This represents a **visibility gap** between detection and centralized correlation.

This limitation is recorded for transparency and mirrors real-world SOC integration gaps.

---

## ✅ Validation Status

**Status:** ✅ Validated (IDS Layer Only)  
SQL injection activity was successfully detected by network intrusion detection controls. SIEM ingestion remains out of scope for this simulation.

---

## 🔁 Related Documentation

- Risk Register Entry (Web Application Exploitation Risk)
- SIM-003 Technical Simulation Documentation
- Issues & Resolutions Record for SQL Injection Simulation
