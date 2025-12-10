# SIM-002 – DNS Tunneling – Symbolic & Observed Logs

## ✅ Data Capture Method
Due to a Security Onion Elastic authentication failure (HTTP 401), Suricata DNS logs were unavailable in the Hunt interface. DNS activity was instead validated directly at the **sensor packet level using tcpdump**.

This represents a realistic SOC fallback scenario where **packet capture confirms activity despite SIEM pipeline degradation**.

---

## ✅ Live DNS Packet Evidence (tcpdump)

Command used on Security Onion host:

```bash
sudo tcpdump -i any -n port 53
```

Observed DNS traffic:
- Source IP: 10.0.0.50
-Destination IP: 10.0.0.10
- Query: google.com
-Record Types: A, AAAA, PTR
- Transport: UDP/53

---

## ✅ Environment Validation
| Component                      | Status                      |
| ------------------------------ | --------------------------- |
| Windows 11 DNS Client          | ✅ Generating Queries        |
| Windows Server DNS (10.0.0.10) | ✅ Responding                |
| pfSense Routing                | ✅ Functional                |
| Security Onion Sensor          | ✅ Capturing                 |
| Suricata Engine                | ✅ Running                   |
| Elastic Ingest                 | ❌ Broken (401 Auth Failure) |
| Splunk SO Ingest               | ❌ Blocked                   |

---

## ✅ Detection Conclusion

- DNS traffic was positively validated at the sensor level
- Lack of Suricata DNS visibility in Hunt was caused by Elastic authentication failure
- This scenario documents a real-world SIEM ingestion outage with successful network-layer verification

---

## ✅ Screenshots Captured

- sim002-dns-tcpdump-proof.png – Live DNS query capture
- sim002-so-containers-running.png – Security Onion container status
- sim002-hunt-no-data.png – Hunt showing no suricata.dns data

---

## ✅ Status

- ✅ Sensor traffic verified
- ✅ Network telemetry proven
- ✅ SIEM failure documented
- ✅ Simulation completed using fallback validation
