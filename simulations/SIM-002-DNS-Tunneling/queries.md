# SIM-002 – DNS Tunneling (T1071.004) – Detection Queries

This file documents the detection logic and investigative queries relevant to
SIM-002, which demonstrates **DNS tunneling–related activity** under a
**SIEM ingestion outage scenario**.

This simulation reflects a **real-world SOC degradation condition**, where:
- **Packet-level sensor visibility** is the authoritative source
- **Suricata → Elastic → Hunt** ingestion is unavailable (HTTP 401)
- **SIEM-based correlation is deferred**, not invalidated

Detection logic is therefore documented across **baseline validation,
primary detection, and future alert readiness**.

---

## 1. Baseline DNS Activity (Normal Resolution)

**Purpose:**  
Establish expected DNS traffic patterns prior to tunneling detection.

Baseline Characteristics:
- Standard domain names
- Low entropy query strings
- Normal query frequency

Baseline validation was performed via **direct packet inspection** at the sensor
layer due to SIEM unavailability.

```text
Example Query:
google.com
```

What This Confirms:
- DNS resolution functioning normally
- Endpoint → DNS server path is healthy
- Establishes baseline behavior for comparison

---

## 2. PRIMARY Detection – Sensor-Level DNS Validation (Authoritative)

Purpose:  
Confirm DNS activity at the network layer during SIEM ingestion failure.

Authoritative Detection Method:  
Packet capture using tcpdump on the Security Onion sensor.
```bash
sudo tcpdump -i any -n port 53
```

What This Confirms:
- DNS traffic is reaching the sensor
- Source system is actively generating queries
- Destination DNS server is responding
- Query contents are observable in plaintext

This represents the primary detection signal for SIM-002 under degraded
visibility conditions.

---

## 3. Planned IDS Detection – Suricata DNS Events (Post-Recovery)

Purpose:  
Define the detection logic that will be used once Elastic ingestion is restored.
```lucene
event.dataset:"suricata.dns"
```

Intended Usage:
- Security Onion → Hunt
- Identification of anomalous DNS patterns
- Foundation for tunneling-specific analysis (length, entropy, frequency)

Why This Matters:
- Detection logic is preserved and ready
- SIEM outage does not invalidate detection design
- Enables rapid reactivation post-recovery

---

## 4. Supplemental Endpoint DNS Telemetry (Optional)

Purpose:  
Provide endpoint-level context if Windows DNS logging is enabled.
```spl
index=winevent_system EventCode=22
| table _time host QueryName QueryStatus
| sort -_time
```

What This Adds:
- Endpoint confirmation of DNS queries
- Useful for correlation with network telemetry
- Not required for primary detection validity

> ⚠️ This telemetry was unavailable during SIM-002 due to downstream ingest blockage.

---

## 5. Cross-Layer Correlation Logic (Conceptual)

Purpose:  
Describe how signals would be correlated in a fully operational SOC environment.

Correlation Approach:
- Packet-level DNS validation (sensor)
- IDS-based DNS event detection (Suricata)
- Endpoint DNS confirmation (Windows logs)
- SIEM correlation and alerting (Splunk / Elastic)

This mirrors real-world SOC workflows during recovery from data pipeline outages.

---

## 6. ✅ PRIMARY ALERT QUERY (FINAL – POST-RECOVERY)

Purpose:  
Define the alert-ready query once SIEM ingestion is restored.
```lucene
event.dataset:"suricata.dns"
```

Expected Outcome:
- DNS events returned → alert logic activates
- Suspicious patterns investigated
- Symbolic detection restored without redesign

---

## ✅ Interpretation Guide
| Result                    | Meaning |
|---------------------------|----------------------|
| DNS visible via tcpdump	  | Network-layer detection confirmed |
| Suricata Hunt empty	      | Elastic ingest failure |
| Endpoint DNS logs absent  |	Downstream ingestion blocked |
| SIEM restored             |	Detection immediately reactivatable |

---

## 🏁 Query Summary
- Packet capture served as the authoritative detection method
- DNS activity was conclusively validated at the sensor layer
- Suricata and SIEM queries are preserved for post-recovery use
- Detection engineering intent remains intact despite outage

> This file represents the finalized detection logic documentation for SIM-002
> and accurately reflects the telemetry, constraints, and SOC decision-making
> observed during the simulation.
