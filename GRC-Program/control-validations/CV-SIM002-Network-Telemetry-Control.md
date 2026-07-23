# CV-SIM002 — DNS Monitoring & Anomaly Detection Control Validation  
**Simulation:** SIM-002 – DNS Tunneling (T1071.004)  
**Control Type:** Network Monitoring / Detection Control  
**Owner:** Carlo Espina  
**Validation Date:** 2026-01-24  

---

## 🎯 Control Objective

Ensure the environment can detect anomalous DNS behavior consistent with command-and-control or DNS tunneling activity by validating that:

- DNS telemetry is captured at the network sensor  
- Events are indexed into ECS-compliant data streams  
- Analysts can investigate DNS anomalies using Security Onion Hunt  
- Evidence can be collected in a repeatable and defensible manner  

---

## ⚠️ Risk Addressed

Attackers use DNS as an application-layer protocol for:

- Command-and-control communication  
- Data exfiltration  
- Evasion of traditional perimeter controls  

Without DNS telemetry monitoring, these behaviors may bypass detection mechanisms.

---

## 🛡️ Control Implementation

### Primary Telemetry (Authoritative Source)

- **Zeek DNS logs** collected via Security Onion sensor  
- Events stored in ECS-compliant Elastic data streams  

### Supplemental Validation

- Packet capture (tcpdump → PCAP)  
- Wireshark packet-level inspection (optional)

### Analyst Investigation Layer

- Security Onion Hunt interface  
- ECS-aware KQL queries (e.g., `event.dataset: "zeek.dns"`)

---

## 🧪 Control Testing Method

The control was validated in two phases:

### Phase 1 – Baseline DNS Activity
Normal DNS traffic generated to establish standard behavior.

### Phase 2 – Suspicious DNS Behavior
High-frequency, long, randomized DNS queries generated to emulate DNS tunneling patterns.

Validation confirmed:

1. Zeek captured DNS traffic  
2. Events written to `dns.log`  
3. Events indexed into Elastic backend  
4. DNS telemetry discoverable via Hunt (KQL)  
5. Anomalies identifiable through query pivots  
6. Evidence documented through screenshots and optional PCAPs  

---

## 📋 Evidence Artifacts

| Evidence ID | Description |
|------------|-------------|
| E-SIM002-001 | Baseline DNS telemetry in Zeek logs |
| E-SIM002-002 | Baseline DNS visibility in Hunt |
| E-SIM002-003 | Suspicious DNS queries in Zeek logs |
| E-SIM002-004 | Suspicious DNS anomaly validation in Hunt |
| E-SIM002-006–011 (Optional) | Wireshark DNS packet-level validation |
| E-SIM002-012 (Optional) | Baseline DNS PCAP retention |
| E-SIM002-013 (Optional) | Suspicious DNS PCAP retention |

Screenshots follow naming convention:  
`sim002-evidence-###-description.png`

---

## 🧩 Framework Alignment

**NIST CSF**

| Function | Category |
|----------|----------|
| Identify | ID.RA |
| Protect | PR.PT |
| Detect | DE.CM |
| Detect | DE.AE |
| Respond | RS.AN |

**CIS Controls**
- Control 13 – Network Monitoring and Defense  
- Control 8 – Audit Log Management  

---

## 🧾 Governance & Compliance Notes

- DNS telemetry monitoring is treated as a core detective control.  
- ECS-aware querying is required to validate ingestion and analyst visibility.  
- Packet-level validation provides additional evidentiary strength when available.

---

## 👤 Control Ownership

| Item | Value |
|------|------|
| Control Owner | JCE (Owner / Security Program Owner) |
| Control Type | Detective |
| Test Frequency | Quarterly or after sensor/pipeline changes |
| Evidence Retention | 90 days minimum |
| Exception Handling | Failures documented in Issues & Resolutions and re-tested |

---

## ⚠️ Known Operational Consideration

Visibility in Security Onion Hunt depends on ECS-compliant query methods.  
Telemetry may exist but remain undiscoverable without proper KQL usage.

---

## ✅ Validation Status

**Control Test Result:** Pass ✅  
**Control Status:** Implemented and Verified  
**MITRE Technique Validated:** T1071.004 (DNS)

---

## 🔁 Related Documentation

- SIM-002 Technical Simulation Documentation  
- Risk Register (C2 / DNS Abuse Risk)  
- Detection Validation Matrix Entry  
- SIM-002 Issues & Resolutions Log  
