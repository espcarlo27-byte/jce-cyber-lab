# SIM-002 – DNS Tunneling Detection Alert

**Symbolic ID:** LAB-SIM-002-DNS-TUNNEL  
**MITRE Technique:** T1071.004 – Application Layer Protocol: DNS  
**Severity:** Medium  
**Status:** Active (Zeek / Network Metadata Layer)

---

## 🎯 Alert Purpose

This alert is designed to detect **potential DNS tunneling activity** by identifying
**suspicious or anomalous DNS query behavior** commonly associated with covert
command-and-control (C2) channels.

The detection focuses on:
- Abnormally long or randomized DNS subdomains
- Repetitive, high-frequency DNS queries
- DNS usage inconsistent with standard name resolution

This alert represents a **network-layer behavioral detection** based on **Zeek DNS
metadata**, suitable for early-stage C2 identification.

---

## 🔎 Detection Logic (Alert Search)

This alert uses Zeek DNS telemetry indexed in Security Onion and queried using
ECS-compliant fields.

```lucene
event.dataset:"zeek.dns" AND dns.query.length >= 35
```

Detection Notes:
- Zeek DNS events are the authoritative detection source
- Detection relies on behavioral indicators, not payload inspection
- Query length threshold is based on observed simulation data
- Logic is intentionally simple and explainable
- Additional frequency or entropy logic can be layered later

---

## ⏱️ Scheduling Configuration
- **Alert Type:** Scheduled
- **Run Frequency:** Every 5 minutes
- **Time Range:** Last 15 minutes

This configuration balances:
- Timely detection of DNS tunneling attempts
- Controlled alert volume in a lab environment

---

## 🚨 Trigger Conditions
- **Trigger When:** Number of Results ≥ 1
- **Throttle Period:** 10 minutes

This ensures:
- Any suspicious DNS tunneling indicator triggers review
- Repeated alerts from the same activity are suppressed

---

## ⚠️ Severity Classification
- **Severity Level:** Medium

**Rationale:**  
DNS tunneling is commonly associated with:
- Command-and-control communications
- Data exfiltration
- Stealthy persistence mechanisms

Severity is set to Medium to prompt investigation while allowing
analyst-driven validation before escalation.

---

## 📤 Alert Output Fields

The alert should include the following fields:
- `@timestamp`
- `source.ip`
- `destination.ip`
- `dns.query.name`
- `dns.query.length`
- `event.dataset`
- `simulation_id`
- `symbolic_id`

These fields support:
- Source attribution
- DNS behavior analysis
- Timeline reconstruction
- Detection traceability

---  

## 🧾 Example Alert Output (Representative)
```text
@timestamp: 2025-12-23 15:06:36
source.ip: 10.0.0.100
destination.ip: 10.0.0.1
dns.query.name: kqmdzvpxnlfjtrwqzvhdxqabc.example.com
dns.query.length: 37
event.dataset: zeek.dns
simulation_id: SIM-002
symbolic_id: LAB-SIM-002-DNS-TUNNEL
```

---

## 🛠️ Recommended Alert Actions

When this alert fires:
- Open an investigation in Security Onion
- Pivot on source.ip and dns.query.name
- Assess frequency and repetition patterns
- Correlate with endpoint or process activity (if available)
- Determine whether behavior aligns with known DNS tunneling tools

No automated remediation is configured for this lab simulation.

---

## 🧭 Analyst Response Workflow

Upon alert trigger, an analyst should:
1. Identify the source host generating DNS queries
2. Review DNS query structure and length
3. Assess repetition and query frequency
4. Pivot to other Zeek datasets (conn, notice)
5. Correlate with endpoint telemetry if available
6. Determine likelihood of DNS tunneling vs false positive
7. Document findings in the case record

---

## ✅ Validation Checklist

- [x] DNS tunneling–style traffic generated
- [x] Zeek DNS telemetry ingested and indexed
- [x] Detection query validated in Hunt
- [x] Alert logic defined and tested conceptually
- [x] Alert ready for activation
- [x] Evidence captured and documented

---

## 📎 Required Evidence

The following evidence supports this alert configuration:
- `sim002-zeek-dns-baseline-log.png`
- `sim002-hunt-zeek-dns-baseline.png`
- `sim002-zeek-dns-suspicious-log.png`
- `sim002-hunt-zeek-dns-suspicious.png`

Location:
```bash
simulations/SIM-002-DNS-Tunneling/screenshots/
```

---

## 🏁 Status
- Detection logic validated
- Alert logic finalized
- Evidence captured
- Alert is deployable and defensible

**SIM-002 Alert Status:** ***✅ Ready***
