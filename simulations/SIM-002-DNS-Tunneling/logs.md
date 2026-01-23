# SIM-002 – DNS Tunneling (T1071.004) – Log Evidence

This file contains **symbolic and representative Zeek DNS log evidence**
captured during SIM-002, demonstrating **DNS-based command-and-control–style
tunneling behavior** within the lab environment.

The logs below reflect **actual telemetry observed in Security Onion**
and are used to validate:
- Detection logic in `queries.md`
- Analytical reasoning for DNS tunneling behavior
- Evidence captured in the `screenshots/` directory

---

## 🧾 Log Sources Used

- **Zeek DNS Logs** – Primary detection and validation source
- **Security Onion Hunt Interface** – Indexed and searchable telemetry
- **Elastic (ECS-normalized fields)** – DNS metadata and enrichment

> ℹ️ **Evidence Note**  
> All logs documented below were confirmed via both raw Zeek DNS logs
> and the Security Onion Hunt UI. Screenshots are referenced for each
> validation stage.

---

## 🔄 Field Normalization Notes

The following Zeek DNS fields were confirmed and consistently populated:

- `dns.query.name`
- `dns.query.length`
- `dns.subdomain`
- `dns.highest_registered_domain`
- `dns.response.code_name`
- `source.ip`
- `destination.ip`
- `network.transport`

These fields are sufficient for **behavioral DNS tunneling detection**
without requiring payload inspection.

---

## 🧾 Evidence & Naming Convention Notes

This simulation follows the standardized evidence naming convention:

- Evidence IDs: `E-SIM002-###`
- Screenshot files: `sim002-evidence-###-<short-description>.png`
- PCAP files (optional): `sim002-evidence-###-<short-description>.pcap`

---

## 1. Baseline DNS Resolution (Normal Behavior)

**Evidence IDs:** `E-SIM002-001`, `E-SIM002-002`  
**Source:** Zeek DNS Logs  
**View:** Security Onion Hunt (Baseline)  

**Screenshot References:**  
- `sim002-evidence-001-zeek-dns-baseline-log.png`  
- `sim002-evidence-002-hunt-zeek-dns-baseline.png`

```text
Timestamp: 2025-12-23 15:06:36
Source IP: 10.0.0.100
Destination IP: 10.0.0.1
Query: www.google-analytics.com
Query Length: 24
Record Type: A
Response Code: NOERROR
Transport: UDP/53
```
Interpretation:
- Short, human-readable domain
- Normal query length
- Legitimate, well-known domain
- Expected DNS resolution behavior

This establishes a clean baseline for comparison.

---

## 2. High-Frequency Randomized DNS Queries (Suspicious Behavior)

**Evidence ID:** `E-SIM002-003`, `E-SIM002-004`
**Source:** Zeek DNS Logs + Hunt UI
**View:** Security Onion Hunt (Suspicious Activity)  

**Screenshot Reference:**  
- `sim002-evidence-003-zeek-dns-suspicious-log.png`
- `sim002-evidence-004-hunt-zeek-dns-suspicious.png `

Traffic Generation Command (Kali Linux):
```bash
for i in {1..50}; do nslookup $(head /dev/urandom | tr -dc a-z | head -c 25).example.com; done
```
```text
Timestamp: 2025-12-23 15:06:36
Source IP: 10.0.0.100
Destination IP: 10.0.0.1
Query: kqmdzvpxnlfjtrwqzvhdxqabc.example.com
Query Length: 37+
Record Type: A
Response Code: NOERROR
Transport: UDP/53
```
Interpretation:
- Randomized, high-entropy subdomains
- Elevated query length
- Rapid, repeated DNS requests
- Behavior consistent with DNS tunneling or data exfiltration techniques

---

## 3. Behavioral Pattern Confirmation

Observed patterns across multiple events:
- Repeated DNS queries in short time windows
- Randomized subdomain strings
- Consistent destination (DNS server)
- UDP/53 transport
- No resolution failures (NOERROR responses)

These characteristics align with MITRE ATT&CK T1071.004 – DNS-based C2.

---

## 🔗 Correlated DNS Activity Timeline
```text
15:06:30 – Baseline DNS queries observed (normal domains) [E-SIM002-001 / E-SIM002-002]
15:06:36 – Randomized subdomain queries begin [E-SIM002-003 / E-SIM002-004]
15:06:36–15:07:00 – High-frequency DNS requests sustained [E-SIM002-003 / E-SIM002-004]
```

Conclusion:
> DNS tunneling–style activity was clearly distinguishable from baseline
> DNS behavior using Zeek metadata alone.

---
## PCAP Evidence (Optional – Supporting Evidence Only)

Packet-level validation is optional supporting evidence for SIM-002.
Zeek DNS telemetry remains the authoritative detection signal.  

## Optional PCAP Retention Artifacts

**Evidence ID:** `E-SIM002-012`
- `sim002-evidence-012-baseline-dns.pcap`
**Purpose:** Baseline DNS PCAP capture (normal resolution)  

**Evidence ID:** `E-SIM002-013`
- `sim002-evidence-013-suspicious-dns.pcap`
**Purpose:** Suspicious DNS PCAP capture (tunneling-style behavior)  

> Note: PCAPs may be stored outside GitHub due to size.
> If stored externally, document location + checksum.

## Optional Wireshark Validation Screenshots

**Baseline Analysis**  
- `E-SIM002-006` → `sim002-evidence-006-wireshark-baseline-overview.png`
- `E-SIM002-007` → `sim002-evidence-007-wireshark-baseline-query-details.png`

**Suspicious Analysis**
- `E-SIM002-008` → `sim002-evidence-008-wireshark-suspicious-overview.png`
- `E-SIM002-009` → `sim002-evidence-009-wireshark-suspicious-long-query.png`
- `E-SIM002-010` → `sim002-evidence-010-wireshark-suspicious-randomized-subdomain.png`
- `E-SIM002-011` → `sim002-evidence-011-wireshark-suspicious-frequency.png`

> ***Packet-level validation (tcpdump + Wireshark) is documented in `steps.md` and used only as supporting evidence, not as the primary detection mechanism.***

---

## 🧠 Detection Relevance

These logs directly support:
- Detection logic in `queries.md`
- Behavioral DNS anomaly identification
- Symbolic detection ID: `LAB-SIM-002-DNS-TUNNEL`

The simulation demonstrates that DNS tunneling can be detected using
query structure, length, and frequency, even when payloads are opaque.

---

## 🏁 Status
- [x] Baseline DNS behavior captured
- [x] Suspicious DNS activity generated
- [x] Zeek DNS logs ingested and indexed
- [x] Hunt UI validation completed
- [x] Screenshots captured and referenced
- [x] Detection logic validated

> ***Simulation Status: ✅ Validated (Zeek / Network Metadata Layer)***

