# SIM-002 – DNS Tunneling (T1071.004)

## 🎯 Goal

Simulate DNS-based command-and-control–style traffic to validate **network-level DNS telemetry**, anomaly identification, and **analyst investigation across both Security Onion and Splunk**.

This simulation validates the **LAB-SIM-002** entry in the Detection Validation Matrix through **end-to-end packet capture, log forwarding, indexing, and analyst-driven investigation**.

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
- **SIEM / Analysis Platform:** Splunk Enterprise  
- **Log Forwarding:** Splunk Universal Forwarder (Zeek logs)  
- **Network:** Single broadcast domain (LAN tap via pfSense)

---

## 📂 Files in This Simulation

- `steps.md` – DNS traffic generation and simulation steps  
- `queries.md` –  
  - KQL queries used in Security Onion Hunt  
  - SPL searches used for DNS analysis in Splunk  
- `screenshots/` –  
  - Zeek DNS evidence (Security Onion Hunt)  
  - DNS analysis views from Splunk  
- `issues-and-resolutions/` – Troubleshooting and remediation history  

---

## 🧪 Simulation Overview

This simulation was executed in **two phases**, with detections validated at the **network sensor** and **SIEM analysis** layers.

### Phase 1 – Baseline DNS Activity

Normal DNS queries were generated to establish expected DNS behavior patterns, including:
- Standard domain lengths  
- Low-entropy query names  
- Typical query frequency  

### Phase 2 – Suspicious DNS Behavior

High-frequency DNS queries with long, randomized subdomains were generated to emulate **DNS tunneling–like behavior**, enabling analysis of:
- Abnormally long DNS labels  
- Repeated base domains  
- Elevated request volume inconsistent with normal browsing  

---

## 🔎 Detection & Validation

### Zeek DNS Telemetry (Security Onion)

- DNS traffic was captured by Zeek  
- DNS events written to `dns.log`  
- Events indexed into Security Onion’s Elastic backend  

### Hunt Verification (Security Onion – KQL)

DNS telemetry was validated using ECS-aware KQL queries:

```so
event.dataset: "zeek.dns"
```

### Key Investigation Pivots

- `dns.question.name`
- `source.ip`
- `destination.port`
- `network.transport`

---

### SIEM Analysis (Splunk)

Zeek DNS logs were forwarded from Security Onion to Splunk to support **SOC-style investigation and historical analysis**.

In Splunk, DNS tunneling indicators were analyzed using SPL searches focusing on:
- Unusually long DNS query names  
- Repeated queries to the same external domains  
- High query frequency from a single source host  

Splunk was used as an **analysis and visibility layer**, not the primary detection engine, mirroring how many SOCs centrally consume Zeek telemetry.

---

## ✅ Success Criteria (Met)

- Suspicious DNS tunneling–like traffic generated  
- Zeek successfully captured DNS telemetry  
- DNS events indexed and visible in Security Onion Hunt  
- Zeek logs forwarded and searchable in Splunk  
- Analyst-level investigation performed using **KQL and SPL**  
- Evidence captured and documented  

---

## ⚠️ Issues Encountered & Resolutions

### Issue: DNS Telemetry Not Immediately Visible in Hunt

DNS events were visible at the Zeek sensor but initially **not discoverable in Security Onion Hunt**.

**Root Cause**  
Security Onion 2.x stores Zeek logs in **ECS-compliant Elastic data streams**. Free-text searches (e.g., `zeek.dns`) do not return results without ECS-aware KQL syntax.

**Resolution**
```so
event.dataset: "zeek.dns"
```
This restored full visibility in Hunt.

**Full technical breakdown:**  
`../../issues-and-resolutions/sim-002-dns-tunneling.md`

---

## 🧠 Analyst Takeaway

This simulation demonstrates a realistic SOC workflow:

- **Zeek** provides deep protocol-level DNS visibility  
- **Security Onion** enables rapid network-centric hunting  
- **Splunk** centralizes DNS telemetry for scalable analysis and historical review  

**Key lessons:**
- DNS tunneling is best detected through **behavioral analysis**, not signatures  
- Network telemetry remains effective even without endpoint context  
- SIEMs amplify sensor value by enabling repeatable, analyst-driven investigations  

---

## 🏁 Status

**Simulation Status:** ✅ Validated

> DNS tunneling–like activity was successfully captured, forwarded, indexed, and analyzed using **Zeek, Security Onion Hunt, and Splunk**.  
SIM-002 is fully reproducible and defensible as a completed detection and analysis scenario.

