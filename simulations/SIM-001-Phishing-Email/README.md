# SIM-001 – Phishing Email (T1566.002) – Spearphishing Link
## 🎯 Goal

Detect phishing-based initial access where a user receives a malicious email,
authenticates to their enterprise mailbox, clicks a link, and initiates outbound
communication to attacker-controlled infrastructure.

This simulation validates **multi-layer detection correlation** using:

- Identity telemetry (Active Directory)
- Endpoint execution telemetry (Windows Security / Sysmon)
- Network evidence (Apache access logs)
- SIEM-based cross-source investigation (Splunk)

---

## 🧬 MITRE ATT&CK Mapping

| Tactic | Technique | Description |
|-------|-----------|-------------|
| Initial Access | **T1566.002** | Phishing – Link |
| Execution | T1204 | User Execution |
| Command & Control (Simulated) | T1071.001 | Web Protocols |

---

## 🏗 Lab Components Used

| Component | Role |
|----------|------|
| Windows 11 Endpoint | Victim machine executing phishing link |
| Windows Server (AD) | Enterprise identity & authentication telemetry |
| Zimbra Mail Server | Phishing delivery & mailbox authentication |
| Splunk Enterprise | Log ingestion, correlation, alerting |
| Splunk Universal Forwarder | Endpoint log forwarding |
| Kali Linux | Hosts phishing landing page & tracking script |
| Security Onion (Optional) | Supplemental network telemetry |

---

## 📁 Repository Structure
```
SIM-001/
│
├── README.md
├── steps.md
├── queries.md
├── alert-config.md
├── issues-and-resolutions.md
│
├── phishing-files/
│   ├── index.html
│   ├── track.php
│   ├── phish_log.txt
│   └── hr_email.txt
│
└── screenshots/
```

---

### 🔎 Phishing Artifacts

Phishing infrastructure files are version-controlled in:

📁 `SIM-001/phishing-files/`

This enables:

- Reproducible deployment
- Version alignment with documentation
- Infrastructure-as-code style simulation management

---

## 🧠 Detection Architecture & Philosophy

SIM-001 follows a **Layered Evidence Model**:

| Layer | Role | Authority Level |
|------|------|----------------|
| Email System | Phishing delivery & mailbox access | Context |
| Identity (AD) | User authentication telemetry | Attribution |
| Endpoint (Windows) | Process execution visibility | Execution Signal |
| Network | Outbound HTTP confirmation | **Primary Click Confirmation** |
| SIEM | Cross-source correlation | Investigation Platform |

---

### ⚠ Browser Behavior Clarification

When phishing is delivered via webmail:

- The link opens within an existing browser session.
- The phishing URL may **not** appear in `Process_Command_Line`.
- Child browser processes may lack full navigation context.

Therefore, detection authority is established through:

1. Event ID 4688 / Sysmon Event ID 1 (browser execution)
2. Time-aligned outbound HTTP request to attacker host
3. Server-side confirmation via Apache access logs or tracking script

This mirrors real-world SOC methodology where detection depends on **correlation**, not string matching.

---

## 🧪 Attack Scenario Overview

1. Enterprise identity authenticates to mailbox  
2. Phishing email delivered  
3. User opens message (webmail)  
4. User clicks malicious link  
5. Existing browser session initiates outbound HTTP request  
6. Endpoint logs record browser process activity  
7. Attacker infrastructure logs inbound request  
8. SIEM correlates identity, endpoint, and network evidence  

---

## 🔄 Deployment Model

Phishing artifacts may be deployed using:

### Option A – Version-Controlled Deployment (Recommended)

```bash
git clone https://github.com/<your-username>/<your-repo>.git
cd <repo>/SIM-001/phishing-files
sudo cp * /var/www/html/
```

This ensures:

- Version consistency  
- Reproducibility  
- Clean lab lifecycle management  

---

## Option B – Manual File Download

Download individual files from GitHub and place them in:
```css
/var/www/html/
```

---

## 📸 Evidence Capture Standard

Each validation phase requires screenshot evidence including:

- Timestamp visible  
- Hostname visible  
- Username visible (if applicable)  
- Event ID visible (for Windows logs)  
- IP address visible (for network logs)  
- Splunk query visible  

Evidence must be stored in:
```css
SIM-001/screenshots/
```

Naming format:
```shell
sim001-A-evidence-###-description.png
```

---

## 📊 Detection Logic Summary

Primary detection identifies:

- Browser execution (`chrome.exe`, `msedge.exe`, etc.)  
- User context (`it.helpdesk1`)  
- Time-aligned outbound HTTP request to Kali server  
- Server-side log confirmation  

Detection relies on:

> **Multi-Layer Correlation** (Identity + Endpoint + Network)

Email and identity logs provide investigation context and user attribution.

---

## 🛡 Governance & Control Alignment

This simulation validates endpoint phishing detection controls.

Control validation documentation:

📄 `CV-SIM001 – Endpoint Phishing Link Detection`

---

## ⚠ Issues & Resolutions

All operational challenges, telemetry corrections, index adjustments, and detection tuning are documented in:  
👉 **[SIM-001 – Issues & Resolutions](../../issues-and-resolutions/sim-001-phishing-email.md)**  

Each issue entry follows:  

**Issue → Root Cause → Resolution → Verification → Overall Takeaway → Status**  

This ensures reproducibility and professional troubleshooting documentation.

---

## 📁 Simulation Files

- **README.md** — Simulation overview, detection architecture, and validation philosophy  
- **steps.md** — Detailed execution and SOC investigation procedure  
- **queries.md** — SPL detection queries and correlation logic  
- **alert-config.md** — Alert configuration and trigger validation  
- **issues-and-resolutions.md** — Troubleshooting log and telemetry corrections  
- **phishing-files/** — Version-controlled phishing artifacts (`index.html`, `track.php`, `phish_log.txt`, `hr_email.txt`)  
- **screenshots/** — Evidence images and validation proof  

---

## ✅ Success Criteria

| Validation Requirement              | Expected Result                          |
|-------------------------------------|------------------------------------------|
| Phishing email delivered            | Confirmed in mailbox                     |
| User interaction observed           | Browser execution logged                 |
| Event ID 4688 recorded              | Visible in Splunk                        |
| Outbound HTTP observed              | Apache access log confirms               |
| Server-side confirmation            | `track.php` logs request                 |
| Correlation query returns results   | Detection validated                      |
| Alert generated                     | Alert triggers successfully              |
| Identity telemetry present          | AD authentication events visible         |

---

## 🏁 Final Status

| Category            | Result                                 |
|---------------------|------------------------------------------|
| Detection Authority | Multi-Layer Correlation                |
| MITRE Technique     | T1566.002 – Spearphishing Link          |
| Simulation Status   | COMPLETE & REPRODUCIBLE                |

---

## 🎓 Outcome

SIM-001 demonstrates the ability to:

- Detect phishing-triggered browser interaction  
- Correlate identity, endpoint, and network telemetry  
- Confirm attacker infrastructure communication  
- Apply SOC investigative methodology  
- Align detection to MITRE ATT&CK  

This simulation reflects enterprise-grade detection validation practices.

