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

## 8. Packet Analysis with Wireshark (Validation)

This step validates suspicious DNS behavior at the **packet level** to confirm that Zeek telemetry accurately reflects traffic observed on the wire.

Wireshark is used strictly for **validation and analyst confirmation**, not continuous monitoring or alerting.

---

***8.1 Start Packet Capture***  

On **Security Onion**:

- Launch **Wireshark**
- Select the **monitoring interface** (same interface used by Zeek)
- Start capture **before or during** suspicious DNS generation

---

***8.2 Apply Display Filter***  

Apply the following Wireshark display filter to isolate DNS traffic:

```text
udp.port == 53
```

This limits visibility to DNS queries and responses relevant to tunneling analysis.

---

***8.3 Observe DNS Packet Characteristics***  

Inspect DNS packets generated during **Step 5 – Suspicious DNS Traffic**.

Look for:

- **Unusually long DNS query names**
- **High-entropy subdomains** that appear randomized
- **Repeated queries** to the same base domain
- **Elevated query frequency** over a short time window

These characteristics should contrast clearly with baseline DNS traffic observed earlier.

---

***8.4 Validate Against Zeek Telemetry***  

Confirm that packet-level observations align with:

- DNS entries in Zeek `dns.log`
- ECS-normalized DNS events visible in Hunt

Key fields to correlate include:
- `dns.question.name`
- `source.ip`
- `destination.port`

This confirms that Zeek parsing and indexing accurately represent raw packet activity.

---

***8.5 Capture Evidence***  

📸 **Screenshots to capture:**
- Wireshark view showing long or randomized DNS query names
- Multiple repeated DNS queries to the same base domain

📁 **Save screenshots to:**  
`simulations/SIM-002-DNS-Tunneling/screenshots/wireshark/`


**Suggested filenames:**
- `sim002-wireshark-dns-baseline.png`
- `sim002-wireshark-dns-suspicious.png`

---

### Notes

- Packet capture is used for **ground-truth validation**
- In production SOC environments, Zeek provides scalable DNS monitoring
- Wireshark is most valuable during **detection development and investigation**

---

## 9. Analyst Interpretation

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

## 10. Save Screenshots

Store all evidence in:  
`simulations/SIM-002-DNS-Tunneling/screenshots/`

Required screenshots:
- `sim002-zeek-dns-baseline-log.png`
- `sim002-hunt-zeek-dns-baseline.png`
- `sim002-zeek-dns-suspicious-log.png`
- `sim002-hunt-zeek-dns-suspicious.png`

---

## 11. Mark Simulation Completion

Update SIM-002 checklist:
- [x] Baseline DNS generated
- [x] Suspicious DNS generated
- [x] Zeek DNS logs captured
- [x] Hunt telemetry validated
- [x] Evidence screenshots saved
- [x] Simulation marked **Validated**



