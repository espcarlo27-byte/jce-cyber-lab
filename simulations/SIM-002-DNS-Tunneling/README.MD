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

## ✅ Status

- ✅ DNS traffic generated and captured
- ✅ Packet-level visibility validated
- ✅ Security Onion sensor confirmed operational
- ❌ Suricata → Elastic ingest blocked due to authentication failure
- ✅ SIEM outage documented
- ✅ Fallback detection successfully executed
- ✅ Screenshots captured

