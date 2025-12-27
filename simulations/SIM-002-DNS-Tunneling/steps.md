# SIM-002 – DNS Tunneling (T1071.004) – Steps

## 1. Prerequisites

Before starting, verify the following systems are online and healthy:

- **Kali Linux**
  - Internet access through pfSense
  - DNS resolution functional
  - `dnsutils` installed:
    ```
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

📸 **Screenshot:**  
`sim002-hunt-zeek-dns-baseline.png`

---

## 5. Generate Suspicious DNS Traffic (Tunneling-Like Behavior)

On **Kali Linux**, generate high-frequency DNS queries with long, randomized subdomains:
```bash
for i in {1..50}; do nslookup $(head /dev/urandom | tr -dc a-z | head -c 25).example.com; done
```

This simulates:
- Abnormally long DNS queries
- High query frequency
- Patterns consistent with DNS tunneling behavior

---

## 6. Observe Suspicious DNS in Zeek Logs

On **Security Onion**:
```bash
sudo tail -f /nsm/zeek/logs/current/dns.log
```

Expected:
- Long subdomain strings
- Repeated base domain
- Rapid query generation

📸 **Screenshot:**  
`sim002-zeek-dns-suspicious-log.png`

---

## 7. Analyze Suspicious DNS in Hunt

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
- Repeated query patterns
- Increased frequency

📸 **Screenshot:**  
`sim002-hunt-zeek-dns-suspicious.png`

---

## 8. Analyst Interpretation

At this stage, the analyst should be able to distinguish:

- **Normal DNS**
  - Short, human-readable domains
  - Low frequency

- **Suspicious DNS**
  - Long, high-entropy subdomains
  - Repeated base domains
  - Elevated query volume

This satisfies the detection objective for DNS tunneling–style behavior.

---

## 9. Save Screenshots

Store all evidence in:  
`simulations/SIM-002-DNS-Tunneling/screenshots/`

Required screenshots:
- `sim002-zeek-dns-baseline-log.png`
- `sim002-hunt-zeek-dns-baseline.png`
- `sim002-zeek-dns-suspicious-log.png`
- `sim002-hunt-zeek-dns-suspicious.png`

---

## 10. Mark Simulation Completion

Update SIM-002 checklist:
- [x] Baseline DNS generated
- [x] Suspicious DNS generated
- [x] Zeek DNS logs captured
- [x] Hunt telemetry validated
- [x] Evidence screenshots saved
- [x] Simulation marked **Validated**



