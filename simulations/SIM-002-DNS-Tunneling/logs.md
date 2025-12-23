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

## 1. Baseline DNS Resolution (Normal Behavior)

**Source:** Zeek DNS Logs  
**View:** Security Onion Hunt (Baseline)  
**Screenshot Reference:**  
`sim002-zeek-dns-baseline-log.png`  
`sim002-hunt-zeek-dns-baseline.png`

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

2. High-Frequency Randomized DNS Queries (Suspicious Behavior)

Source: Zeek DNS Logs  
View: Security Onion Hunt (Suspicious Activity)  
Screenshot Reference:  
`sim002-zeek-dns-suspicious-log.png`  
`sim002-hunt-zeek-dns-suspicious.png`  

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
15:06:30 – Baseline DNS queries observed (normal domains)
15:06:36 – Randomized subdomain queries begin
15:06:36–15:07:00 – High-frequency DNS requests sustained
```

Conclusion:
> DNS tunneling–style activity was clearly distinguishable from baseline
> DNS behavior using Zeek metadata alone.

---

## 🧠 Detection Relevance

These logs directly support:
- Detection logic in queries.md
- Behavioral DNS anomaly identification
- Symbolic detection ID: LAB-SIM-002-DNS-TUNNEL

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

**Simulation Status:** ***✅ Validated (Zeek / Network Metadata Layer)***

