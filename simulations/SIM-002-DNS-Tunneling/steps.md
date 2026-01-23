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
`sim002-evidence-001-zeek-dns-baseline-log.png`

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
`sim002-evidence-002-hunt-zeek-dns-baseline.png`

---

## 5. (Optional) Capture Baseline DNS PCAP for Wireshark

This step is optional but strongly recommended for interview-quality evidence.
Security Onion CLI does not provide Wireshark, so packet captures are generated using tcpdump
and analyzed in Wireshark from Kali or the host PC.

### 5.1 Identify the Monitoring Interface (Security Onion)
```bash
ip a
```

**Common names:**  
- ens160, ens192, eth0, etc.

### 5.2 Capture Baseline DNS Traffic (Security Onion)

Replace `<iface>` with your monitor interface and `<KALI_IP>` with Kali IP:  
```bash
sudo tcpdump -i <iface> -nn host <KALI_IP> and udp port 53 -w /tmp/sim002-baseline-dns.pcap
```

**Generate DNS queries again from Kali (Step 2), then stop capture:**  
- Press Ctrl + C

Verify file exists:
```bash
ls -lh /tmp/sim002-baseline-dns.pcap
```

### 5.3 Transfer PCAP to Kali for Wireshark

From Kali:
```bash
scp soadmin@<SECURITY_ONION_IP>:/tmp/sim002-evidence-003-baseline-dns.pcap ~/Downloads/
```

### 5.4 Wireshark Baseline Analysis (Kali)

Open the PCAP:
- Wireshark → File → Open → `sim002-evidence-003-baseline-dns.pcap`

#### Wireshark display filters (baseline)
- `dns`
- `dns.flags.response == 0` (queries only)
- `dns.flags.response == 1` (responses only)

#### What to validate (baseline)
- Queries are short and human-readable (example: `google.com`, `github.com`)
- Low volume / non-bursty traffic
- Normal query-response pattern

**📌 Evidence ID:** `E-SIM002-004`
📸 **Screenshot(s) to capture (baseline Wireshark evidence):**
- `sim002-evidence-004-wireshark-baseline-overview.png`
  **How:** Apply filter `dns`  
  **Capture:** Packet list showing normal DNS traffic volume + query/response activity

- `sim002-evidence-005-wireshark-baseline-query-details.png`
  **How:** Apply filter `dns.flags.response == 0`, click a query packet  
  **Capture:** Expanded DNS section showing `Queries` → `Name` (short readable domain)

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
- Long subdomain strings
- Repeated base domain
- Rapid query generation

**📌 Evidence ID:** `E-SIM002-002`  
**📸 Screenshot:**  
`sim002-evidence-006-zeek-dns-suspicious-log.png`

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
- Repeated query patterns
- Increased frequency

**📌 Evidence ID:** `E-SIM002-002`
**📸 Screenshot:**  
`sim002-evidence-007-hunt-zeek-dns-suspicious.png`

---

## 9. (Optional) Capture Suspicious DNS PCAP for Wireshark

### 9.1 Capture Suspicious DNS Traffic (Security Onion)

Replace `<iface>` with your monitor interface and `<KALI_IP>` with Kali IP:
```bash
sudo tcpdump -i <iface> -nn host <KALI_IP> and udp port 53 -w /tmp/sim002-suspicious-dns.pcap
```

While capture is running, execute Step 6 again (suspicious traffic), then stop capture:
- Press Ctrl + C

Verify file exists:
```bash
ls -lh /tmp/sim002-suspicious-dns.pcap
```

### 9.2 Transfer PCAP to Kali

From Kali:
```bash
scp soadmin@<SECURITY_ONION_IP>:/tmp/sim002-suspicious-dns.pcap ~/Downloads/
```

### 9.3 Wireshark Suspicious Analysis (Kali)

Open in Wireshark:
- Wireshark → File → Open →`sim002-suspicious-dns.pcap`

Useful display filters:
- `dns`
- `dns.qry.name contains "example.com"`
- `dns.qry.name`
- `dns.flags.response == 0` (queries only)

What to look for:
- unusually long DNS query names
- repeated base domain (example.com)
- bursty/high-frequency queries in short time windows
- randomized/high-entropy subdomains (tunneling indicator)

**📌 Evidence ID:** `E-SIM002-004`
📸 **Screenshot(s)** to capture (suspicious Wireshark evidence):

- `sim002-evidence-009-wireshark-suspicious-overview.png`
  **How:** Apply filter dns  
  **Capture:** Packet list showing high-frequency DNS traffic compared to baseline
  
- `sim002-evidence-010-wireshark-suspicious-long-query.png`
  **How:** Apply filter dns.flags.response == 0, click a suspicious query  
  **Capture:** Expanded DNS section clearly showing an unusually long query name
  
- `sim002-evidence-011-wireshark-suspicious-randomized-subdomain.png`
  **How:** Apply filter dns.qry.name contains "example.com"  
  **Capture:** Multiple DNS queries showing randomized subdomains but the same base domain
  
- `sim002-evidence-012-wireshark-suspicious-frequency.png`
  **How:** Keep dns.flags.response == 0 filter  
  **Capture:** Packet list timestamps showing repeated queries occurring rapidly (burst behavior)

---

## 10. Analyst Interpretation

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

## 11. Save Screenshots

Store all evidence in:  
`simulations/SIM-002-DNS-Tunneling/screenshots/`

### ***📸 Evidence Naming Standard (SIM-002)***

All evidence for this simulation follows a consistent naming convention to support audit traceability.

- Evidence ID format: `E-SIM002-###`
- Screenshot file format: `sim002-evidence-###-<short-description>.png`
- PCAP file format (optional): `sim002-evidence-###-<short-description>.pcap`

---

### ✅ Required (Zeek + Hunt Evidence)

- `sim002-evidence-001-zeek-dns-baseline-log.png`  
  **Purpose:** Zeek baseline DNS queries visible in `dns.log`

- `sim002-evidence-002-hunt-zeek-dns-baseline.png`  
  **Purpose:** Baseline DNS telemetry visible in Hunt (`event.dataset:"zeek.dns"`)

- `sim002-evidence-006-zeek-dns-suspicious-log.png`  
  **Purpose:** Zeek suspicious DNS queries (randomized subdomains / long names)

- `sim002-evidence-007-hunt-zeek-dns-suspicious.png`  
  **Purpose:** Suspicious DNS telemetry investigated in Hunt (pivot/anomaly review)

---

### 🟡 Optional (Wireshark Packet-Level Evidence)

**Baseline PCAP + Wireshark**
- `sim002-evidence-004-wireshark-baseline-overview.png`  
  **Purpose:** Show baseline DNS query traffic volume and query/response flow

- `sim002-evidence-005-wireshark-baseline-query-details.png`  
  **Purpose:** Highlight normal-length DNS query name details

**Suspicious PCAP + Wireshark**
- `sim002-evidence-009-wireshark-suspicious-overview.png`  
  **Purpose:** Show burst/high-frequency DNS traffic during tunneling-like activity

- `sim002-evidence-010-wireshark-suspicious-long-query.png`  
  **Purpose:** Highlight unusually long DNS query name length

- `sim002-evidence-011-wireshark-suspicious-randomized-subdomain.png`  
  **Purpose:** Highlight high-entropy/randomized subdomains under same base domain

- `sim002-evidence-012-wireshark-suspicious-frequency.png`  
  **Purpose:** Show repeated queries over short time window (burst behavior)

---

### 🟦 Optional (PCAP File Retention – Supporting Evidence)

If retaining PCAP files in the repo or evidence archive:
- `sim002-evidence-003-baseline-dns.pcap`
- `sim002-evidence-008-suspicious-dns.pcap`

---

## 12. Mark Simulation Completion

Update SIM-002 checklist:

### Core Validation (Required)
- [x] Baseline DNS generated
- [x] Suspicious DNS generated
- [x] Zeek DNS logs captured
- [x] Hunt telemetry validated
- [x] Evidence screenshots saved
- [x] Simulation marked **Validated**

### Packet-Level Validation (Optional – Wireshark Evidence)
- [ ] Baseline DNS PCAP captured using `tcpdump`
- [ ] Suspicious DNS PCAP captured using `tcpdump`
- [ ] PCAP files transferred to Kali / Host machine for analysis
- [ ] Wireshark inspection completed (query length, entropy, frequency)
- [ ] Wireshark evidence screenshots saved
