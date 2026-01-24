# CV-SIM003 — Detection Correlation Control Validation  
**Simulation:** SIM-003 – Detection Logic & Correlation  
**Control Type:** Detection Engineering / Monitoring Control  
**Owner:** Carlo Espina  
**Validation Date:** 2026-01-24  

---

## 🎯 Control Objective

Ensure that security telemetry is not only logged but:

- Aggregated in the SIEM  
- Correlated into detection logic  
- Capable of identifying suspicious behavior patterns  
- Able to generate alertable events with supporting evidence  

This validates that the environment can transform raw telemetry into actionable security detections.

---

## ⚠️ Risk Addressed

Even when logs exist, attackers may remain undetected if:

- Events are not correlated  
- Suspicious patterns are not recognized  
- Alerts are not generated  

Without detection logic, security monitoring becomes passive logging instead of active defense.

---

## 🛡️ Control Implementation

### Data Sources Used
- Windows endpoint logs (e.g., Sysmon / Security logs)
- Network telemetry (Zeek / Suricata), when applicable

### SIEM Detection Layer
- Splunk SPL searches designed to identify suspicious patterns  
- Aggregation and filtering of events  
- Logic-based detection criteria  
- Query structures used to reduce false positives  

---

## 🧪 Control Testing Method

The control was validated through a controlled simulation:

1. Suspicious activity generated in the lab environment  
2. Relevant telemetry captured by logging sources  
3. Events ingested into Splunk  
4. SPL correlation searches executed  
5. Detection logic confirmed ability to identify the activity  
6. Results verified as usable for investigation  

---

## 📋 Evidence Artifacts

| Evidence ID | Description |
|------------|-------------|
| E-SIM003-001 | Correlation SPL search execution proof |
| E-SIM003-002 | Detection result output in Splunk |
| E-SIM003-003 | Supporting raw event visibility |

Related screenshots follow naming convention:  
`sim003-evidence-###-description.png`

---

## 🧩 Framework Alignment

**NIST CSF**
- DE.AE – Anomalies and Events  
- DE.CM – Security Continuous Monitoring  

**CIS Controls**
- Control 8 – Audit Log Management  
- Control 17 – Incident Response Management  

---

## ✅ Validation Status

**Status:** ✅ Validated  
Correlation logic successfully transformed raw telemetry into a detection event with supporting evidence.

---

## 🔁 Related Documentation

- Risk Register Entry (Detection Gaps Risk)
- Detection Matrix Coverage
- SIM-003 Technical Simulation Documentation
