# CV-SIM004 — Enhanced Process Telemetry Control Validation  
**Simulation:** SIM-004 – Sysmon Process Create (T1059)  
**Control Type:** Endpoint Monitoring Control  
**Owner:** Carlo Espina  
**Validation Date:** 2026-01-24  

---

## 🎯 Control Objective

Ensure that endpoint process execution is captured with **detailed telemetry**, including:

- Command-line arguments  
- Parent → child process relationships  
- Executing user context  
- Timestamp and host attribution  

This validates enhanced endpoint telemetry as a control that improves detection accuracy and investigative capability.

---

## ⚠️ Risk Addressed

Basic execution logs may not provide enough context to detect or investigate:

- Malicious scripts  
- Living-off-the-land techniques  
- Obfuscated command execution  
- Privilege misuse  

Without detailed process telemetry, attacker behavior may blend into normal activity.

---

## 🛡️ Control Implementation

### Primary Telemetry
- **Sysmon Event ID 1 – Process Create**

Provides:
- Full command line  
- Parent process  
- Process GUIDs  
- Image path  

### Supplemental Telemetry
- Windows Security Event ID 4688 (baseline execution logging)

### SIEM Layer
- Splunk ingestion of Sysmon logs  
- SPL searches validating command-line visibility  
- Queries showing parent-child process chains  

---

## 🧪 Control Testing Method

The control was tested through controlled execution scenarios:

1. Commands executed on endpoint  
2. Sysmon generated Process Create events  
3. Events ingested into Splunk  
4. SPL searches validated:
   - command-line logging  
   - process lineage visibility  
5. Data confirmed usable for detection engineering and investigation  

---

## 📋 Evidence Artifacts

| Evidence ID | Description |
|------------|-------------|
| E-SIM004-001 | Sysmon Event ID 1 ingestion proof |
| E-SIM004-002 | Command-line visibility proof |
| E-SIM004-003 | Parent-child process relationship proof |

Related screenshots follow naming convention:  
`sim004-evidence-###-description.png`

---

## 🧩 Framework Alignment

**NIST CSF**
- DE.CM – Security Continuous Monitoring  
- DE.AE – Anomalies and Events  

**CIS Controls**
- Control 8 – Audit Log Management  
- Control 10 – Malware Defenses  

---

## ✅ Validation Status

**Status:** ✅ Validated  
Sysmon telemetry successfully provided detailed execution context supporting detection engineering and investigations.

---

## 🔁 Related Documentation

- Risk Register Entry (Malicious Execution Visibility Risk)
- Detection Matrix Coverage for Endpoint Telemetry
- SIM-004 Technical Simulation Documentation
