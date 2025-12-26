# SIM-002 – DNS Tunneling (T1071.004) – Steps

## 1. Prerequisites

Before starting, verify the following systems are online and healthy:

- **Kali Linux**
  - Internet access through pfSense
  - DNS resolution functional
  - `dnsutils` installed:
    ```bash
    sudo apt install dnsutils -y
    ```

- **Security Onion**
  - Zeek running and healthy
  - Monitor interface capturing traffic
  - SOC UI accessible

- **pfSense**
  - Acting as the default gateway
  - DNS resolver enabled
  - All endpoint traffic routed through firewall

- **Splunk Enterprise**
  - Splunk Web accessible
  - Index receiving Zeek logs from Security Onion
  - Time synchronization aligned with Security Onion

### Health Check (Security Onion)

On Security Onion:
```bash
sudo so-status
```

Ensure:
- `so-zeek` = running
- Elasticsearch and SOC services = running

---

## 2. Generate Baseline DNS Traffic (Normal Behavior)

On **Kali Linux**, generate standard DNS queries:
```bash
nslookup example.com
nslookup google.com
nslookup github.com
```
Optional short burst:
```bash
for i in {1..5}; do nslookup example.com; done
```

Purpose:
- Establish normal DNS query patterns
- Validate DNS resolution path
- Confirm baseline DNS visibility

---

## 3. Verify DNS Capture at the Sensor (Zeek)

On **Security Onion**, confirm DNS logs are being written:
```bash
sudo tail -f /nsm/zeek/logs/current/dns.log
```

Expected:
- DNS queries from Kali
- Human-readable domain names
- `NOERROR` responses

📸 **Screenshot:**  
`sim002-zeek-dns-baseline-log.png`

---

## 4. Verify Baseline DNS Telemetry in Hunt

In **Security Onion → SOC → Hunt**:

- Time range: **Last 24 hours**
- Search (KQL):
```so
event.dataset: "zeek.dns"
```

Expected:
- DNS events visible
- Source IP = Kali
- Normal domain lengths
- Low query frequency

📸 **Screenshot:**  
`sim002-hunt-zeek-dns-baseline.png`

---

## 5. Verify Baseline DNS Visibility in Splunk

In **Splunk Web** → Search:
- Set time range: Last 24 hours
- Run a basic search to confirm Zeek DNS ingestion:
```spl
index=* sourcetype=zeek:dns
```

Expected:
- DNS events visible
- Fields such as query name, source IP, and destination IP present
- Baseline (human-readable) domain names

📸 **Screenshot:**  
`sim002-splunk-dns-baseline.png`

---

## 6. Generate Suspicious DNS Traffic (Tunneling-Like Behavior)

On **Kali Linux**, generate high-frequency DNS queries with long, randomized subdomains:
```bash
for i in {1..50}; do nslookup $(head /dev/urandom | tr -dc a-z | head -c 25).example.com; done
```

This simulates:
- Abnormally long DNS queries
- High query frequency
- Patterns consistent with DNS tunneling behavior

---

## 7. Observe Suspicious DNS in Zeek Logs

On **Security Onion**:
```bash
sudo tail -f /nsm/zeek/logs/current/dns.log
```

Expected:
- Long, randomized subdomain strings
- Repeated base domain (`example.com`)
- Rapid query generation

📸 **Screenshot:**  
`sim002-zeek-dns-suspicious-log.png`

---

## 8. Analyze Suspicious DNS in Hunt

In **Security Onion → Hunt**:

Search (KQL):
```so
event.dataset: "zeek.dns"
```
Optional refinement:
```so
event.dataset: "zeek.dns" and dns.question.name:*
```

Look for:
- Long or random-looking subdomains
- Repeated base domains
- Increased query frequency from the same source IP

📸 **Screenshot:**  
`sim002-hunt-zeek-dns-suspicious.png`

---

## 9. Analyze Suspicious DNS in Splunk

In **Splunk Web** → Search:

Example SPL to identify tunneling-like behavior:
```spl
index=* sourcetype=zeek:dns
| eval query_length=len(query)
| where query_length > 40
| stats count by src, query
```

Look for:
- Unusually long DNS query names
- Repeated queries to the same base domain
- High volume of DNS requests from a single host

📸 **Screenshot:**  
sim002-splunk-dns-suspicious.png

> Splunk is used here as an analysis and visibility layer, consuming Zeek telemetry centrally rather than performing primary detection.

---

## 10. Analyst Interpretation

At this stage, the analyst should be able to distinguish:

- **Normal DNS**
  - Short, human-readable domains
  - Low frequency
  - Predictable access patterns

- **Suspicious DNS**
  - Long, high-entropy subdomains
  - Repeated base domains
  - Elevated query volume

> This satisfies the detection objective for DNS tunneling–style behavior.
> and this behavior aligns with MITRE ATT&CK T1071.004 (DNS).

---

## 11. Save Screenshots

Store all evidence in:  
`simulations/SIM-002-DNS-Tunneling/screenshots/`

Required screenshots:
- `sim002-zeek-dns-baseline-log.png`
- `sim002-hunt-zeek-dns-baseline.png`
- `sim002-splunk-dns-baseline.png`
- `sim002-zeek-dns-suspicious-log.png`
- `sim002-hunt-zeek-dns-suspicious.png`
- `sim002-splunk-dns-suspicious.png`

---

## 10. Mark Simulation Completion

Update SIM-002 checklist:
- [x] Baseline DNS generated
- [x] Suspicious DNS generated
- [x] Zeek DNS logs captured
- [x] Hunt telemetry validated
- [x] Zeek logs ingested into Splunk
- [x] Splunk analysis completed
- [x] Evidence screenshots saved
- [x] Simulation marked **Validated**




