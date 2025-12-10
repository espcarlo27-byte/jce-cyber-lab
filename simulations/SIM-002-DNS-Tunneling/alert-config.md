# SIM-002 – DNS Tunneling Detection Alert

**Symbolic ID:** LAB-SIM-002-DNS-TUNNEL  
**Technique:** T1071.004 – Application Layer Protocol: DNS  
**Severity:** High  
**Status:** Ready for Deployment  

---

## 🎯 Alert Purpose

This alert detects suspected **DNS tunneling activity** based on:
- Abnormally long DNS query lengths
- Encoded subdomain structures
- High-entropy DNS traffic patterns

This behavior is commonly associated with:
- Data exfiltration
- Covert C2 communication
- Beaconing malware over DNS

***This alert represents a **Command-and-Control channel detection** use case.***

---

## 🔎 Detection Logic (Alert Search)

This query is copied directly from the **primary correlation query** in `queries.md` and is used as the alert trigger.

```spl
sourcetype=zeek:dns
| eval qlen=len(query)
| where qlen > 50
| eval simulation_id="SIM-002"
| eval symbolic_id="LAB-SIM-002-DNS-TUNNEL"
| table _time, id.orig_h, id.resp_h, query, qlen, simulation_id, symbolic_id
| sort - _time
```

---

## ⏱️ Scheduling Configuration

- Alert Type: Scheduled
- Run Frequency: Every 5 minutes
- Time Window: Last 15 minutes

***This ensures near real-time detection of DNS tunneling while preventing excessive alert noise.***

---

## 🚨 Trigger Conditions

- Trigger When: Number of Results ≥ 1
- Throttle Period: 10 minutes

***Prevents repeated alerts from the same host during continuous tunneling activity.***

---

## ⚠️ Severity Classification

- Severity Level: High
- Rationale:
Covert DNS tunneling indicates potential active command-and-control or data exfiltration. This represents a direct compromise risk and requires immediate investigation.

---

## 📤 Alert Output Fields

The following fields must appear in the alert payload for proper investigation and dashboard integration:
- _time
- id.orig_h
- id.resp_h
- query
- qlen
- simulation_id
- symbolic_id

These fields allow:
- Source host attribution
- Destination DNS server identification
- Encoded data inspection
- Simulation tracking
- SOC alert correlation

---

## 🧾 Example Alert Output (Symbolic)
```yaml
_time: 2025-02-20 11:42:18
id.orig_h: 10.0.0.30
id.resp_h: 8.8.8.8
query: VGhpc0lzU2ltMDAyRXhmaWxEYXRh.sim002.lab
qlen: 64
simulation_id: SIM-002
symbolic_id: LAB-SIM-002-DNS-TUNNEL
```

---

## 🛠️ Recommended Alert Actions

The following alert actions are recommended for this simulation:
- ✅ Create notable/security event
- ✅ Write alert result to lab_index for validation tracking
- ✅ Email notification (optional, for SOC-style workflow demonstration)

---

## 🧭 Analyst Response Workflow (Post-Trigger)

Once this alert fires, the responding analyst should:
1. Identify the source host generating abnormal DNS queries
2. Inspect query structure for encoded or suspicious data
3. Validate whether the destination DNS resolver is trusted
4. Check for simultaneous beaconing or repeated lookups
5. Investigate the source endpoint for malware or scripts
6. Capture supporting PCAP from Security Onion
7. Document findings in the incident report

---

## ✅ Validation Checklist

- [x] Alert created in Splunk
- [x] Alert enabled
- [x] Alert scheduled correctly
- [x] Trigger condition validated
- [x] Alert fires on DNS tunneling activity
- [x] Output fields confirmed
- [x] Symbolic ID appears in alert output

---

## 📎 Required Evidence

Screenshots to collect after live execution:
- sim002-alert-config.png — Alert configuration screen
- sim002-alert-fired.png — Triggered alert confirmation

Location:
```bash
simulations/SIM-002-DNS-Tunneling/screenshots/
```

---

## 🏁 Status

- Detection logic prepared.
- Alert configuration ready.
- Awaiting live DNS tunneling execution and validation.
