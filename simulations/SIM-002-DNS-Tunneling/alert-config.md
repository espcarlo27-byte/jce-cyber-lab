# SIM-002 – DNS Tunneling Detection Alert (Planned)

**Symbolic ID:** LAB-SIM-002-DNS-TUNNEL  
**MITRE Technique:** T1071.004 – DNS Tunneling  
**Severity:** Medium  
**Status:** Deferred – SIEM Ingest Outage

---

## ✅ Alert Purpose

This alert will detect tunneled or suspicious DNS traffic once Elastic ingest is restored.

---

## ✅ Planned Detection Logic

```lucene
event.dataset:"suricata.dns" AND dns.question.name:*
```

---

## ✅ Scheduling (Post-Recovery)

- Alert Type: Scheduled
- Frequency: Every 5 minutes
- Time Window: 15 minutes

---

## ✅ Trigger Conditions

- Results ≥ 1
- Throttle: 10 minutes

---

## ✅ Current Status

- ✅ DNS traffic confirmed via tcpdump
- ❌ Elastic ingestion broken
- ❌ Alert cannot be deployed yet
- ✅ Officially documented as SIEM Degradation Event

---

## ✅ Evidence Collected

- sim002-dns-tcpdump-proof.png
- sim002-hunt-no-data.png

---

## ✅ SOC Note

This simulation intentionally demonstrates:
- Detection continuity during SIEM outage
- Network-layer validation as fallback
- Incident documentation under degraded visibility
