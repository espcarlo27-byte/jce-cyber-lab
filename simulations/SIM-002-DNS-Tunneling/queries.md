# SIM-002 – DNS Tunneling (T1071.004) – Detection Queries

This file documents the detection logic and investigative queries used during
SIM-002, which demonstrates **DNS tunneling–style activity** using
**Zeek DNS telemetry indexed in Security Onion**.

This simulation reflects a **realistic SOC detection workflow**, where:
- Network metadata (Zeek) is the primary detection signal
- DNS payloads are opaque, but behavior is observable
- Detection is based on **query structure, length, entropy, and frequency**

Detection logic is documented across **baseline validation,
primary detection, and alert readiness**.

> ***“Packet-level validation (tcpdump + Wireshark) is documented in steps.md and used only as supporting evidence, not as the primary detection mechanism.”***

---

## 🧾 Evidence & Naming Convention Notes

This simulation uses the standardized evidence naming convention:

- Evidence IDs: `E-SIM002-###`
- Screenshot files: `sim002-evidence-###-<short-description>.png`

Examples:
- `sim002-evidence-001-zeek-dns-visible.png`
- `sim002-evidence-002-suspicious-dns-length.png`
- `sim002-evidence-003-hunt-timeline-pivot.png`

---

## 1. Baseline DNS Activity (Normal Resolution)

**Evidence ID:** `E-SIM002-001`

**Purpose:**  
Establish expected DNS traffic patterns prior to tunneling detection.

Baseline Characteristics:
- Human-readable domain names
- Short query lengths
- Low entropy
- Normal query frequency

### Hunt Query
```lucene
event.dataset:"zeek.dns"
```

Example Baseline Observation
```text
dns.query.name: www.google-analytics.com
dns.query.length: 24
dns.response.code_name: NOERROR
```

What This Confirms:
- DNS resolution is functioning normally
- Endpoint → DNS server path is healthy
- Establishes a behavioral baseline for comparison

---

## 2. PRIMARY Detection – High-Entropy DNS Queries (Zeek)

**Evidence ID:** `E-SIM002-002`

Purpose:  
Detect DNS queries indicative of tunneling or covert C2 behavior.

Threat Characteristics:
- Randomized subdomains
- Elevated query length
- Repeated queries in short time windows
- Legitimate DNS responses (NOERROR)

### Hunt Query – Suspicious DNS Queries
```lucene
event.dataset:"zeek.dns"
AND dns.query.length >= 35
```

What This Detects:
- Encoded or randomized DNS subdomains
- Data-carrying DNS requests
- DNS tunneling–style behavior

---

## 3. Frequency-Based DNS Anomaly Detection

Purpose:  
Identify high-volume DNS query patterns originating from a single host.

### Hunt Query – DNS Burst Behavior
```lucene
event.dataset:"zeek.dns"
```

Hunt Aggregation Strategy:
- Group by source.ip
- Observe rapid increases in DNS volume
- Correlate with elevated query length

Why This Matters:
- DNS tunneling often relies on volume + entropy
- Frequency amplifies confidence in malicious intent

---

## 4. Domain Structure & Subdomain Analysis

Purpose:  
Analyze DNS query composition for tunneling indicators.

Hunt Fields of Interest
- `dns.query.name`
- `dns.subdomain`
- `dns.highest_registered_domain`
- `dns.subdomain_length`

Indicators:
- Long subdomains
- Randomized character distribution
- Repeated queries to the same parent domain

This confirms behavioral misuse of DNS, not simple resolution.

---

## 5. Traffic Generation Alignment (Kali Linux)

Traffic Generation Command Used:
```bash
for i in {1..50}; do nslookup $(head /dev/urandom | tr -dc a-z | head -c 25).example.com; done
```

Expected Zeek Observations:
- Multiple DNS queries per second
- Randomized subdomains under example.com
- Consistent destination DNS server
- UDP/53 transport
- `dns.response.code_name: NOERROR`

This confirms direct alignment between attacker activity and observed telemetry.

---

## 6. Correlation-Ready Detection Logic (Symbolic)

Purpose:  
Define a symbolic detection identifier for SOC tracking.

**Detection Metadata**
```text
Simulation ID: SIM-002
Symbolic ID: LAB-SIM-002-DNS-TUNNEL
Technique: T1071.004 – Application Layer Protocol: DNS
```

This detection is metadata-driven, allowing correlation with:
- Alerts
- Case management
- Detection matrices

---

## 7. Alert Readiness (Conceptual)

Alert Conditions:
- DNS query length ≥ 35
- High-frequency queries from same source
- Randomized subdomain patterns

**Severity:** High
**Tactic:** Command and Control
**Technique:** DNS Tunneling

This logic is alert-ready without redesign.

---

✅ Interpretation Guide
| Observation	| Meaning |
|--------------|----------|
| Short, readable domains | Normal DNS behavior |
| Long randomized subdomains	| Potential DNS tunneling |
| High query volume from one host	| C2 / exfil attempt |
| NOERROR responses |	DNS used as transport |
| Zeek metadata sufficient |	Payload not required|

---

## 🏁 Query Summary
- Zeek DNS telemetry provided full detection visibility
- DNS tunneling was detected via behavioral analysis
- Queries align with real-world SOC detection methods
- Detection logic is reusable and alert-ready

> This file represents the finalized detection query documentation for SIM-002
> and accurately reflects the telemetry, analysis, and SOC decision-making
> observed during execution.
