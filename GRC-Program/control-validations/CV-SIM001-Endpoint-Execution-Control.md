# CV-SIM001 — Endpoint Phishing Link Detection Control Validation  
**Simulation:** SIM-001 – Phishing Email (T1566.002)  
**Control Type:** Endpoint Monitoring + Detection Engineering Control  
**Owner:** Carlo Espina  
**Validation Date:** 2026-01-24  

---

## 🎯 Control Objective

Ensure the environment can **detect and alert on phishing link execution**
using **authoritative endpoint telemetry**, SIEM correlation logic, and
repeatable evidence suitable for audit validation.

---

## ⚠️ Risk Addressed

Phishing links are a primary initial access vector used to:

- Deliver malware  
- Harvest credentials  
- Establish attacker footholds  

If endpoint execution telemetry is not captured and correlated, phishing-based compromise may go undetected.

---

## 🛡️ Control Implementation

### Primary Telemetry (Authoritative Source)

- Windows Security EventCode **4688 – Process Creation**
- Command-line auditing enabled (`Process_Command_Line` field)

### Supplemental Telemetry (Optional)

- Security Onion network logs (Zeek / Suricata)  
  Used only as supporting validation when available.

### Detection & SIEM Layer

- Splunk Enterprise ingestion of endpoint logs  
- SPL correlation logic detecting browser execution with URL context  
- Alerting pipeline configured to generate detection alerts

---

## 🧪 Control Testing Method

The control was validated through a controlled phishing simulation:

1. User opened a phishing email on Windows endpoint  
2. Suspicious URL was clicked  
3. Browser execution occurred (`chrome.exe`)  
4. Windows logged EventCode 4688 with command-line data  
5. Event ingested into Splunk  
6. SPL correlation search detected URL execution  
7. Alert fired with symbolic detection ID

---

## 📋 Evidence Artifacts

| Evidence ID | Description |
|------------|-------------|
| E-SIM001-001 | Email viewed / phishing content |
| E-SIM001-002 | Browser execution EventCode 4688 |
| E-SIM001-003 | URL present in `Process_Command_Line` |
| E-SIM001-004 | Splunk correlation search output |
| E-SIM001-005 | Alert configuration + firing evidence |

Screenshots follow naming convention:  
`sim001-evidence-###-description.png`

---

## 🧩 Framework Alignment

**NIST CSF**

| Function | Category |
|----------|----------|
| Protect | PR.AT |
| Detect | DE.CM |
| Detect | DE.AE |
| Respond | RS.AN |

**CIS Controls**
- Control 8 – Audit Log Management  
- Control 17 – Incident Response Management  

---

## 🧾 Governance & Compliance Notes

- Detection relies on endpoint telemetry as the authoritative signal.  
- Network telemetry is optional and not required for control validity.  
- Evidence collected supports audit validation of monitoring and detection controls.

---

## 👤 Control Ownership

| Item | Value |
|------|------|
| Control Owner | JCE (Lab Owner / Security Program Owner) |
| Control Type | Preventive + Detective |
| Test Frequency | Quarterly or after environment changes |
| Evidence Retention | 90 days minimum |
| Exception Handling | Failures logged in Issues & Resolutions and re-tested |

---

## ✅ Validation Status

**Control Test Result:** Pass ✅  
**Control Status:** Implemented and Verified  
**Linked Detection ID:** `LAB-SIM-001-PHISHING-ALERT`

---

## 🔁 Related Documentation

- SIM-001 Technical Simulation Documentation  
- Risk Register (Phishing / Initial Access Risk)  
- Detection Validation Matrix Entry  
- SIM-001 Issues & Resolutions Log  
