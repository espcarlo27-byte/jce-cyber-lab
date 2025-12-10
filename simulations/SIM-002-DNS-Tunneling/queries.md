
# SIM-002 – DNS Tunneling – Detection Queries

Due to Elastic SIEM authentication failure on Security Onion (HTTP 401), DNS detection was validated using **sensor-level packet capture** instead of Suricata → Elastic → Hunt.

These queries remain valid **once Elastic ingest is repaired**.

---

## ✅ 1. Sensor-Level DNS Validation (Primary Method Used)

Command used on Security Onion host:

```bash
sudo tcpdump -i any -n port 53
```

✅ Confirms:
- DNS traffic is reaching sensor
- Source system generating queries
- Target DNS server responding
- Query contents visible in plaintext

---

## ✅ 2. Post-Recovery Suricata DNS Query (Future Use)
```lucene
event.dataset:"suricata.dns"
```

Used inside:
```nginx
Security Onion → Hunt
```

---

## ✅ 3. Splunk Windows DNS Validation (Optional)

Once enabled:
```spl
index=winevent_system EventCode=22
| table _time, host, QueryName, QueryStatus
| sort -_time
```

---

## ✅ Interpretation Guide
| Result              | Meaning                       |
| ------------------- | ----------------------------- |
| tcpdump shows DNS   | ✅ Network detection confirmed |
| Suricata Hunt blank | ❌ Elastic ingest failure      |
| Splunk empty        | ❌ Downstream ingest blocked   |

---

## ✅ Detection Status
- ✔️ DNS activity confirmed at sensor layer
- ✔️ SOC fallback method validated
- ✔️ SIEM outage handled professionally

