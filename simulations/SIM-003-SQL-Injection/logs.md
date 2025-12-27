# SIM-003 – SQL Injection (T1190) – Log Evidence

This file contains **symbolic and representative telemetry** captured during
SIM-004, demonstrating **successful SQL injection exploitation** against a
deliberately vulnerable web application (DVWA).

The logs and observations below reflect **actual behavior observed during execution**
and are used to validate:
- Network-based detection logic
- IDS alerting behavior
- Detection effectiveness in the absence of SIEM correlation

Network and IDS telemetry are treated as the **primary authoritative sources**
for this simulation due to lab design constraints.

---

## 🧾 Log Sources Used

- **Network / IDS Telemetry (Primary)**
  - Security Onion
  - Suricata (ET ruleset)
  - Inline HTTP traffic inspection via `ens192`

- **Application Behavior (Observed)**
  - DVWA response behavior (black-box testing)

- **SIEM Telemetry**
  - ❌ Not present by design (no forwarder installed)

> ⚠️ **Important Design Note**  
> This simulation intentionally operates under a **network-centric detection model**.
> Absence of endpoint, database, or SIEM logs does not invalidate detection when
> exploit behavior is observable and alertable at the network layer.

---

## 🔄 Field Normalization Notes

The following fields were reliably observed via network inspection and IDS alerts:

### Network / IDS Telemetry
- `src_ip`
- `dest_ip`
- `protocol`
- `http.method`
- `http.payload`
- `signature`
- `severity`

Application-layer logs were not collected; validation was performed via
observable response behavior.

---

## 1. Baseline Application Behavior (Non-Injection Input)

**Source:** DVWA Application  
**Input Context:** User-supplied ID parameter

```text
Input: 1
Result: Single database record returned
```

Interpretation:
- Normal application behavior
- No query manipulation
- Establishes baseline response pattern

📸 Evidence:  
`sim004-dvwa-baseline-query.png`

---

## 2. SQL Injection Execution (Application-Layer Exploitation)

Source: DVWA Application
Attack Context: Crafted user input
```text
Payloads:
1' OR '1'='1
1' OR 1=1-- -
1' UNION SELECT user, password FROM users-- -
```

Observed Behavior:
- Multiple database records returned
- Disclosure of user credential data
- Confirms backend SQL query manipulation

Interpretation:
- Successful SQL injection
- Injection point operates in a string-based context

📸 Evidence:  
`sim004-dvwa-sqli-success.png`

---

## 3. Payload Validation (Non-Effective Injection Attempt)

Source: DVWA Application
Attack Context: Numeric-based payload
```text
Payload:
1 OR 1=1#
```

Interpretation:
- No change in application response
- Indicates injection point does not evaluate numeric-only logic
- Confirms SQL context specificity

---

## 4. Network Traffic Observed (Inline Visibility)

Source: Security Onion Sensor
Protocol: HTTP
```text
Source IP: 10.0.0.30 (Kali)
Destination IP: 10.0.0.60 (DVWA)
Protocol: HTTP
Observed Payloads: SQL operators and comment markers
```

Interpretation:
- Malicious HTTP traffic observed inline
- Confirms correct sensor placement
- Network path visibility validated

📸 Evidence:  
`sim004-securityonion-traffic-visible.png`

---

## 5. IDS Detection (Suricata Alert)

Source: Suricata  
Signature Category: ET WEB_SERVER  
Classification: Generic Web Policy Violation   
Severity: Medium

Interpretation:
- Alert fired immediately following injection execution
- Signature not explicitly labeled “SQL Injection”
- Payload content, timing, and attribution confirm exploit detection

📸 Evidence:  
`sim004-suricata-alert.png`

---

## 6. Alert Attribution & Context Validation

Source: Suricata Alert Details
```text
Source IP: 10.0.0.30 (Attacker – Kali)
Destination IP: 10.0.0.60 (Victim – DVWA)
Context: HTTP request containing SQL operators
```

Interpretation:
- Accurate attacker → victim attribution
- Clear exploit context at the network layer
- Supports investigative confidence despite generic signature naming

📸 Evidence:  
`sim004-suricata-alert-details.png`

---

## 🔗 Correlated Exploitation Timeline
```text
Baseline input submitted → single record returned
Injection payload submitted → multiple records disclosed
Malicious HTTP payload observed inline
Suricata alert triggered immediately post-execution
```

Conclusion:
Application response behavior, network payload inspection, and IDS alerting
collectively confirm a validated SQL injection exploitation scenario.

---

## 🧠 Detection Relevance
These observations directly support:
- Network-based SQL injection detection
- IDS alerting effectiveness
- Validation of detection in SIEM-absent conditions

Detection confidence is derived from:
- Observable application impact
- Inline network visibility
- Timely IDS alerting
   - —not from signature naming alone.

---

## 🏁 Status

- [x] SQL injection behavior confirmed
- [x] Application impact validated
- [x] Network traffic observed inline
- [x] IDS alert triggered
- [x] Attacker attribution confirmed
- [x] SIEM correlation (out of scope by design)
- [x] Simulation complete
