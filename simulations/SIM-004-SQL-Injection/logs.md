# SIM-004 – SQL Injection (T1190) – Logs & Telemetry

This document describes the actual telemetry generated during SIM-004 execution, including what evidence was observed, where it originated, and what was not available due to lab design.

---

## 1. Telemetry Sources in Scope

The following log sources were available and evaluated during this simulation:

✅ Network / IDS Telemetry (PRIMARY)
- Security Onion
   - Suricata (ET rule set)
   - HTTP traffic inspection
   - Inline packet visibility via ens192

- ❌ SIEM Telemetry (NOT PRESENT)
- Splunk Enterprise
   - No Suricata events ingested
   - No Splunk Universal Forwarder installed on Security Onion
   - SIEM correlation intentionally out of scope

---

## 2. Application-Layer Behavior (DVWA)

Although application logs were not directly collected, observable behavior confirmed SQL injection execution.

**Observed Behavior**
- Baseline input:
   ```text
   1
   ```
     - Returned a single database record
- Injection payloads:
   ```pgsql
   1' OR '1'='1
   1' OR 1=1-- -
   1' UNION SELECT user, password FROM users-- -
  ```
     - Returned multiple user records
     - Confirmed backend query manipulation

**Payloads That Did NOT Work**
- Numeric-based payload:
   ```csharp
   1 OR 1=1#
   ```
     - Did not alter application behavior
     - Indicates SQL injection point operates in a string-based context

📸 Evidence:
- `sim004-dvwa-baseline-query.png`
- `sim004-dvwa-sqli-success.png`

---

## 3. Network Traffic Observed (Inline Visibility)
**Packet-Level Visibility**
Traffic between Kali and DVWA was observed traversing Security Onion’s monitoring interface.
- Source IP: 10.0.0.30 (Kali)
- Destination IP: 10.0.0.60 (DVWA)
- Protocol: HTTP
- Payloads contained SQL operators and comment markers

This confirms correct sensor placement and network-level observability.

📸 Evidence:
- `sim004-securityonion-traffic-visible.png (optional)`

---

## 4. IDS Detection (Suricata)
**Alert Type Observed**
- Signature: ET WEB_SERVER
- Classification: Generic web policy violation
- Severity: Medium
- Detection Timing: Immediately following SQL injection execution

Although the alert was not explicitly labeled “SQL Injection,” the timing, payload content, and IP attribution confirm detection of exploit-related activity.

📸 Evidence:
- `sim004-suricata-alert.png`

--- 

## 5. Alert Attribution & Context
Alert detail view confirmed:
- Source IP: 10.0.0.30 (Attacker – Kali)
- Destination IP: 10.0.0.60 (Victim – DVWA)
- HTTP context present
- Payload indicators consistent with SQL injection logic

This validates attacker → victim attribution at the network layer.

📸 Evidence:
- `sim004-suricata-alert-details.png`

---

## 6. Telemetry NOT Present (By Design)

The following artifacts were not available during this simulation:

| Artifact                | Reason         |
|-------------------------|----------------|
| Windows Security Logs	  | Linux-based application |
| Sysmon                  | No endpoint malware or Windows host |
| Database audit logs     |	Out of scope    |
| Application source logs	| Black-box testing model |
| Splunk SIEM events	    | No forwarder installed |

These gaps were identified, validated, and documented, not ignored.

---

## 7. Detection Confidence Assessment
| Layer                | Confidence |
|----------------------|------------|
| Application behavior | High       |
| Network visibility   | High       |
| IDS detection	High   | High       |
| SIEM correlation     | Not applicable |

Detection confidence is based on correlated application behavior and network alerts, not signature naming alone.

---

## 8. Key Observations
- SQL injection detection occurred via generic web alerts
- Payload effectiveness depended on string-based query context
- Network architecture was critical to visibility
- Detection existed even without SIEM ingestion

---

## 9. Log Summary
- SQL injection execution confirmed via DVWA behavior
- Malicious HTTP traffic observed inline
- Suricata detected exploit-related activity
- Attacker attribution validated
- SIEM ingestion gap documented intentionally
