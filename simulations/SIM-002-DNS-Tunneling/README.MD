# SIM-002 – DNS Tunneling (T1071.004)

## 🎯 Goal

Simulate DNS tunneling traffic to validate zeek-based DNS anomaly detection, Suricata DNS rules, and Splunk correlation for covert command-and-control channels.

This simulation validates the **LAB-SIM-002** entry in the detection validation matrix.

---

## 🧩 MITRE ATT&CK Mapping

- **Technique:** T1071.004 – Application Layer Protocol: DNS
- **Tactic:** Command and Control (TA0011)

---

## 🏗 Lab Components Used

- **Attacker:** Kali Linux (10.0.0.30)
- **Victim Host:** Windows 11 (10.0.0.50) *(optional DNS tunneling client)*
- **Network Sensor:** Security Onion (Zeek + Suricata)
- **SIEM:** Splunk Enterprise (Ubuntu – 10.0.0.60)
- **Gateway:** pfSense (10.0.0.1)

---

## 📂 Files in This Simulation

- `steps.md` – Full DNS tunneling execution steps
- `logs.md` – Symbolic Zeek + Suricata + Splunk events
- `queries.md` – SPL detection logic
- `alert-config.md` – Splunk alert configuration
- `screenshots/` – Visual detection evidence

---

## ✅ Success Criteria

- Abnormally long or encoded DNS queries generated
- Zeek logs detect suspicious DNS behavior
- Suricata flags DNS anomalies (if enabled)
- Splunk correlates events
- Symbolic alert fires:
    LAB-SIM-002-DNS-TUNNEL

---

## ⚠️ Known Limitations & Environment Constraints

SIM-002 (DNS Tunneling) was executed in a **constrained lab environment** using the
Security Onion **Evaluation** deployment. While partial telemetry was successfully
observed, full end-to-end validation was not achievable due to platform limitations.

This section documents those constraints transparently.

---

### 1. Security Onion Evaluation Mode Limitations

The Security Onion instance used in this lab is running in **Evaluation mode**, which
introduced the following constraints during execution:

- Elasticsearch services were intermittently unavailable
- Zeek DNS logs were not consistently indexed
- SO Grid components reported partial readiness
- Docker containers were running, but data ingestion was incomplete

As a result:
- DNS tunneling activity could not be reliably queried via Zeek dashboards
- DNS logs were not forwarded consistently to Splunk

---

### 2. Elasticsearch Authentication & Indexing Issues

During validation, the following issues were observed:

- Elasticsearch returned `401 Unauthorized` responses
- Elastic credentials could not be reset using standard SO utilities
- Several Security Onion helper commands were unavailable or not present
- Index creation and retention could not be verified

These issues prevented confirmation of:
- Zeek DNS event indexing
- Long-term DNS telemetry persistence

---

### 3. Partial Detection Coverage Achieved

Despite the above constraints, the following **partial validation** was successful:

- Network traffic generation consistent with DNS tunneling was executed
- Suricata containers were operational
- Windows-based DNS telemetry was available in Splunk
- SPL detection logic for DNS anomalies was authored and tested where data existed

This confirms that **detection logic is sound**, but telemetry ingestion was incomplete.

---

### 4. Why This Simulation Is Marked “Partial”

SIM-002 is intentionally marked as **⚠️ Partial** in the Detection Validation Matrix because:

- Detection logic exists
- Simulation steps were executed
- Alerts were defined
- **But** full evidence (Zeek DNS logs + alerts firing) could not be captured

This reflects **real-world SOC conditions**, where:
- Tooling limitations exist
- Not all detections can be validated immediately
- Analysts must document blockers clearly

---

### 5. Planned Remediation

This simulation will be revalidated once one of the following is implemented:

- Full Security Onion (non-evaluation) deployment
- Stable Elasticsearch indexing confirmed
- Zeek DNS logs reliably available
- Alternate DNS telemetry source integrated

At that point, SIM-002 will be upgraded from **Partial → Validated**.

---

## ⚠️ Issues Encountered & Resolutions with Environment Constraints

SIM-002 was executed in a constrained lab environment using Security Onion
Evaluation mode. While detection logic was developed, full validation was not
possible due to platform limitations.

These issues included:
- DNS telemetry visible at the sensor but not indexed
- Elasticsearch authentication and indexing failures
- Zeek DNS logs not consistently available in Evaluation mode
- Partial SIEM ingestion preventing alert confirmation
- Detection logic existing without reliable telemetry

All observed issues, limitations, and planned remediation steps are documented here:  

👉 **Full technical breakdown:**  
[SIM-002 – Issues & Resolutions](../../issues-and-resolutions/sim-002-dns-tunneling.md)

This simulation is intentionally marked as **Partial** to reflect real SOC
conditions where tooling limitations must be documented transparently.

---

## 🧠 Analyst Takeaway

Documenting incomplete detection coverage is a **professional SOC practice**.

Rather than forcing false validation, this simulation demonstrates:
- Honest assessment of tooling limits
- Strong troubleshooting effort
- Clear documentation of blockers
- A defensible reason for partial status

This approach prioritizes **accuracy over optics**, which is critical in security operations.

---

## 🏁 Status

Simulation Status: ✅ Validated (IDS Layer)  

SQL injection execution and detection were successfully validated at the network intrusion detection layer. SIEM ingestion was intentionally excluded and documented as a known limitation.

