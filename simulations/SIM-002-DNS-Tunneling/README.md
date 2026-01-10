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
