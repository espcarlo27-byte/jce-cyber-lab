# CV-SIM004 — Endpoint Execution Baseline Telemetry Control Validation  
**Simulation:** SIM-004 – Sysmon Process Create (T1059)  
**Control Type:** Endpoint Monitoring / Telemetry Depth Control  
**Owner:** Carlo Espina  
**Validation Date:** 2026-01-24  

---

## 🎯 Control Objective

Ensure endpoint process execution is captured with **enriched telemetry**
that supports baseline development and future detection engineering by validating that:

- Sysmon Process Create events are generated  
- Command-line arguments are logged  
- Parent → child process relationships are visible  
- Execution events are searchable for analyst use  
- A reliable baseline of normal execution behavior can be established  

---

## ⚠️ Risk Addressed

Basic execution logging may lack sufficient context to detect:

- Script abuse  
- Living-off-the-land techniques  
- Suspicious command execution  
- Privilege escalation attempts  

Without detailed process telemetry, malicious activity may blend into normal behavior.

---

## 🛡️ Control Implementation

### Primary Telemetry (Authoritative Source)

- **Sysmon Event ID 1 – Process Create**
  - Full command-line logging
  - Parent-child process lineage
  - User context
  - Integrity level

### Supplemental Telemetry

- Windows Security Event ID 4688 (baseline comparison)

### Analyst / SIEM Layer

- SPL searches used to validate telemetry visibility  
- Alert configured to confirm telemetry availability  

> SIEM ingestion supports analysis but is not a prerequisite for control validity.

---

## 🧪 Control Testing Method

The control was validated through controlled baseline execution:

1. Standard user launched common binaries  
2. Commands executed with and without arguments  
3. Sysmon generated Process Create events  
4. Command-line fields verified  
5. Parent-child process relationships observed  
6. Events validated via SPL queries  
7. Alert triggered confirming telemetry presence  

---

## 📋 Evidence Artifacts

| Evidence ID | Description |
|------------|-------------|
| E-SIM004-001 | Windows Security Event 4688 baseline |
| E-SIM004-002 | Sysmon Event ID 1 enriched telemetry |
| E-SIM004-003 | Correlated SPL results |

Screenshots follow naming convention:  
`sim004-evidence-###-description.png`

---

## 🧩 Framework Alignment

**NIST CSF**

| Function | Category |
|----------|----------|
| Detect | DE.CM |
| Detect | DE.AE |

**CIS Controls**
- Control 8 – Audit Log Management  
- Control 10 – Malware Defenses  

---

## 🧾 Governance & Compliance Notes

- This control establishes the execution baseline required for higher-risk detections.
- Serves as prerequisite for privilege escalation and advanced execution detections.
- Evidence supports audit validation of endpoint monitoring controls.

---

## 👤 Control Ownership

| Item | Value |
|------|------|
| Control Owner | JCE (Lab Owner / Security Program Owner) |
| Control Type | Detective |
| Test Frequency | Quarterly or after endpoint telemetry changes |
| Evidence Retention | 90 days minimum |
| Exception Handling | Failures logged in Issues & Resolutions and re-tested |

---

## 🔁 Dependency Note

This control provides the baseline required for:

- SIM-005 – Privilege Escalation Detection  
- Script abuse detections  
- Living-off-the-land monitoring  

---

## ✅ Validation Status

**Control Test Result:** Pass ✅  
**Control Status:** Implemented and Verified  
**MITRE Technique Supported:** T1059 (Execution)

---

## 🔁 Related Documentation

- SIM-004 Technical Simulation Documentation  
- Risk Register (Execution Visibility Risk)  
- Detection Validation Matrix Entry  
- SIM-004 Issues & Resolutions Log  
