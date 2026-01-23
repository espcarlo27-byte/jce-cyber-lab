# SIM-002 – DNS Tunneling (T1071.004)

## 🎯 Goal

Simulate DNS-based command-and-control–style traffic to validate **Zeek DNS telemetry visibility**, anomaly identification, and analyst-level investigation using **Security Onion Hunt**.

This simulation validates the **LAB-SIM-002** entry in the Detection Validation Matrix through **end-to-end packet capture, indexing, and UI-based analysis**.

---

## 🧩 MITRE ATT&CK Mapping

- **Technique:** T1071.004 – Application Layer Protocol: DNS  
- **Tactic:** Command and Control (TA0011)

---

## 🏗 Lab Components Used

- **Attacker:** Kali Linux  
- **Endpoints:** Windows 11, Ubuntu  
- **Network Sensor:** Security Onion (Zeek)  
- **Gateway / DNS Resolver:** pfSense  
- **Network:** Single broadcast domain (LAN tap via pfSense)

---

## 📂 Files in This Simulation

- `steps.md` – DNS traffic generation and simulation steps  
- `queries.md` – KQL queries used in Security Onion Hunt  
- `screenshots/` – Zeek DNS Hunt evidence and timelines  
- `issues-and-resolutions/` – Troubleshooting and remediation history  

---

## 🧪 Simulation Overview

This simulation was executed in **two phases**:

### Phase 1 – Baseline DNS Activity
Normal DNS queries were generated from Kali Linux to establish expected DNS behavior patterns, including:
- Standard domain lengths
- Low entropy query names
- Typical query frequency

### Phase 2 – Suspicious DNS Behavior
High-frequency DNS queries with long, randomized subdomains were generated to emulate **DNS tunneling–like behavior**, enabling analysis of:
- Query length anomalies
- Repeated base domains
- Abnormal request volume

---

## 🔎 Detection & Validation

### Zeek DNS Telemetry
- DNS traffic was successfully captured by Zeek
- DNS logs were written to `dns.log`
- Events were indexed into Security Onion’s Elastic backend

### Hunt Verification (KQL)
DNS telemetry was confirmed in Security Onion Hunt using ECS-aware queries:
```so
event.dataset: "zeek.dns"
```

Additional pivots included:
- `dns.question.name`
- `source.ip`
- `destination.port`
- `network.transport`

### (Optional) Packet-Level Validation (tcpdump → Wireshark)

Because Security Onion is deployed in CLI mode, packet capture evidence was generated using tcpdump and saved into .pcap files.  

The **PCAPs** can then be transferred to **Kali Linux** (or the host PC) and analyzed using **Wireshark** to validate **DNS tunneling** indicators at the packet level.

Example analyst indicators:
- unusually long DNS query names
- high-entropy/randomized subdomains
- repeated base domain queries
- elevated query frequency over short time windows

---

## ✅ Success Criteria (Met)

- Abnormal DNS query patterns generated  
- Zeek captured and logged DNS telemetry  
- DNS events indexed and visible in Hunt  
- Analyst-level investigation performed using KQL  
- Evidence captured and documented  

### Optional validation:
- Packet-level DNS evidence reviewed in Wireshark (PCAP generated via tcpdump)

---

---

## 🛡 GRC Control Validation (Governance / Risk / Compliance)

This simulation is treated as a **security control test** to support audit readiness
and continuous improvement in the JCE Cyber Lab security program.

### 🎯 Control Objective

Ensure the environment can **detect anomalous DNS behavior consistent with DNS tunneling**
by validating that:

- DNS telemetry is captured by Zeek
- Events are indexed into ECS-compliant data streams
- An analyst can investigate using Security Onion Hunt (KQL)
- Evidence can be collected in a repeatable and defensible way

### 🧩 Applicable Framework Mapping (NIST CSF)

| Function | Category | Mapping |
|---------|----------|---------|
| Identify | ID.RA | Risk analysis for C2/exfil over DNS |
| Protect | PR.PT | Protective technology and network controls (firewall/DNS centralization) |
| Detect | DE.CM | Continuous monitoring of DNS telemetry via Zeek |
| Detect | DE.AE | Detection of anomalies in query length/entropy/frequency |
| Respond | RS.AN | Analyst investigation and validation using Hunt pivots |

### ✅ Control(s) Validated

| Control Area | Control Statement | Validation Method | Result |
|-------------|-------------------|------------------|--------|
| Network Monitoring | DNS traffic is captured and logged for visibility | Zeek `dns.log` / ECS telemetry | Pass ✅ |
| Detection Capability | DNS anomalies can be identified via Hunt pivots and query patterns | Security Onion Hunt (KQL) | Pass ✅ |
| Evidence Readiness | Evidence can be captured and retained for audit-ready validation | Screenshots + query outputs + optional PCAP | Pass ✅ |

### 👤 Control Ownership & Governance

| Item | Value |
|------|-------|
| Control Owner | JCE (Lab Owner / Security Program Owner) |
| Control Type | Detective |
| Test Frequency | Quarterly (or after sensor / pipeline changes) |
| Evidence Retention | 90 days minimum (lab standard) |
| Exception Handling | If telemetry/indexing fails → document in Issues & Resolutions and re-test after remediation |

### 📌 Evidence Collected (Audit-Ready)

All evidence follows the standardized naming convention:

- Evidence IDs: `E-SIM002-###`
- Screenshot format: `sim002-evidence-###-<short-description>.png`

| Evidence ID | Description | Source | Location |
|------------|-------------|--------|----------|
| E-SIM002-001 | Baseline DNS telemetry observed in Zeek `dns.log` | Security Onion (Zeek logs) | `screenshots/` |
| E-SIM002-002 | Baseline DNS telemetry validated in Hunt (ECS dataset visibility) | Security Onion Hunt | `screenshots/` |
| E-SIM002-003 | Suspicious DNS tunneling-style queries observed in Zeek `dns.log` | Security Onion (Zeek logs) | `screenshots/` |
| E-SIM002-004 | Suspicious DNS behavior validated in Hunt (length anomaly / pivots) | Security Onion Hunt | `screenshots/` |
| E-SIM002-006 → E-SIM002-011 (Optional) | Wireshark packet-level DNS validation screenshots (baseline + suspicious) | tcpdump → Wireshark | `screenshots/` |
| E-SIM002-012 (Optional) | Baseline DNS PCAP retention (`sim002-evidence-012-baseline-dns.pcap`) | Security Onion tcpdump | `pcap/` (or external archive) |
| E-SIM002-013 (Optional) | Suspicious DNS PCAP retention (`sim002-evidence-013-suspicious-dns.pcap`) | Security Onion tcpdump | `pcap/` (or external archive) |


### 🧾 Compliance/Audit Readiness Notes

- This SIM provides defensible evidence supporting DNS monitoring controls aligned with modern SOC requirements.
- DNS tunneling represents a high-impact technique often associated with **Command & Control** and **data exfiltration**.
- ECS-aware querying is required to validate ingestion and visibility in Security Onion 2.x Hunt.

### 🟢 Control Test Status

**Control Test Result:** Pass ✅  
**Control Status:** Implemented and Verified  
**MITRE Technique Validated:** T1071.004 (DNS)

---

## ⚠️ Issues Encountered & Resolutions

During initial execution, DNS telemetry was visible at the Zeek sensor but **not immediately discoverable in Hunt** due to incorrect query methods.

### Root Cause
Security Onion 2.x stores Zeek logs in **ECS-compliant Elastic data streams**. Free-text searches (e.g., `zeek.dns`) do not return results unless ECS fields or KQL syntax are used.

### Resolution
DNS telemetry was successfully queried using:
```so
event.dataset: "zeek.dns"
```

This restored full visibility in the Hunt interface.

Full technical breakdown:  
`../../issues-and-resolutions/sim-002-dns-tunneling.md`

---

## 🧠 Analyst Takeaway

This simulation highlights an important SOC reality:

Detection capability depends not only on telemetry collection, but also on analyst familiarity with the underlying data model.

Key lessons:
- Zeek can be fully operational while data remains invisible without ECS-aware queries  
- Understanding Elastic data streams is critical in modern SOC environments
- Packet-level evidence (PCAP + Wireshark) provides strong validation when available
- Proper documentation of investigation blockers is as important as detection success  

---

## 🏁 Status

**Simulation Status:** ✅ Validated

DNS telemetry was successfully captured, indexed, queried, and analyzed within Security Onion Hunt.  
SIM-002 is fully reproducible and defensible as a completed detection scenario.
