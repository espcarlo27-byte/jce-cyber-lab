# SIM-002 – DNS Tunneling (T1071.004) – SPL Queries

This file documents all Splunk searches used to detect, validate, and correlate DNS tunneling behavior for SIM-002 using Zeek DNS telemetry.

The purpose of these queries is to:
- Confirm DNS tunneling traffic generation
- Identify long and encoded DNS subdomains
- Detect high-frequency DNS activity
- Correlate suspicious DNS behavior
- Support alerting and dashboarding

---

## 1. Raw DNS Traffic Validation (Zeek Ingest Check)

**Purpose:**  
Verify that Zeek DNS logs are successfully ingested into Splunk.

```spl
sourcetype=zeek:dns
| head 10
```
What This Confirms:
- Zeek DNS logs are flowing into Splunk
- DNS query fields are present
- Splunk index and sourcetype are correct

---

## 2. SIM-002 Domain Activity Validation

Purpose:
Confirms that DNS queries generated during the simulation are present.
```spl
sourcetype=zeek:dns
query="*.sim002.lab"
| table _time, id.orig_h, id.resp_h, query
| sort - _time
```

What This Confirms:
- DNS exfiltration script successfully generated traffic
- The simulated exfil domain is visible in Zeek
- High-frequency lookups are present

---

## 3. Long Subdomain Detection (Primary DNS Tunneling Indicator)

Purpose:
Detects unusually long DNS queries consistent with tunneling and exfiltration.
```spl
sourcetype=zeek:dns
| eval qlen=len(query)
| where qlen > 50
| table _time, id.orig_h, id.resp_h, query, qlen
| sort - qlen
```

What This Confirms:
- Abnormally long DNS subdomain usage
- Encoded data exfil patterns
- High entropy DNS traffic

---

## 4. High-Frequency DNS Request Detection

Purpose:
Detects rapid DNS request bursts from a single source.
```spl
sourcetype=zeek:dns
| stats count by id.orig_h
| where count > 20
| sort - count
```

What This Confirms:
- Beaconing behavior
- Automated DNS traffic generation
- Possible C2 communications

---

## 5. ✅ PRIMARY CORRELATION QUERY (ALERT-READY – SIM-002)

Purpose:
This is the main detection query for SIM-002.
It is used to:
- Validate DNS tunneling activity
- Drive dashboards
- Trigger the Splunk DNS tunneling alert
```spl
sourcetype=zeek:dns
| eval qlen=len(query)
| where qlen > 50
| eval simulation_id="SIM-002"
| eval symbolic_id="LAB-SIM-002-DNS-TUNNEL"
| table _time, id.orig_h, id.resp_h, query, qlen, simulation_id, symbolic_id
| sort - _time
```

What This Confirms:
- DNS tunneling behavior detected
- Long encoded subdomains observed
- Events are tagged with:
    - simulation_id = SIM-002
    - symbolic_id = LAB-SIM-002-DNS-TUNNEL

***✅ This query is directly reused in the alert configuration.***

---

## 6. Last 15 Minutes Validation (Post-Execution Check)

Purpose:
Ensures real-time detection and confirms simulation success.
```spl
sourcetype=zeek:dns
| eval qlen=len(query)
| where qlen > 50
earliest=-15m
| table _time, id.orig_h, id.resp_h, query, qlen
| sort - _time
```

Expected Outcome:
- Results appear → SIM-002 executed successfully
- No results → Zeek ingestion or DNS flow issue exists

---

## ✅ Interpretation Guide
- Results in Section 2 → DNS tunneling execution confirmed
- Results in Section 3 → High-risk tunneling signature detected
- Results in Section 4 → Beaconing behavior present
- Results in Section 5 → Alert should fire
- Results in Section 6 → Real-time validation successful

***✅ This file serves as the core detection engineering artifact for SIM-002.***

