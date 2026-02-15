# SIM-001 – Phishing Email (T1566.002) – Spearphishing Link

## 🎯 Goal

Detect phishing-based initial access where a user receives a malicious email,
authenticates to their enterprise mailbox, clicks a link, and initiates outbound
communication to attacker-controlled infrastructure.

The simulation validates **endpoint-authoritative detection** enhanced with
**identity (IAM) telemetry**, **email workflow context**, and **network correlation**,
modeling how real SOC investigations trace phishing attacks from identity
authentication through endpoint execution and external connection.

---

## 🧬 MITRE ATT&CK Mapping

| Tactic | Technique | Description |
|-------|-----------|-------------|
| Initial Access | **T1566.002** | Phishing – Link |
| Execution | T1204 | User Execution |
| Command & Control (simulated) | T1071.001 | Web Protocols |

---

## 🏗 Lab Components Used

| Component | Purpose |
|----------|---------|
| Windows 11 Endpoint | Victim machine executing phishing link |
| Windows Server (AD) | Identity provider and authentication telemetry |
| Mail Server (Zimbra) | Phishing email delivery and mailbox access logging |
| Splunk Enterprise | Log ingestion, correlation, and alerting |
| Splunk Universal Forwarder | Endpoint log forwarding |
| Kali Linux | Hosts phishing landing page |
| Security Onion (Optional) | Supplemental network telemetry |

---

## 🧠 Detection Architecture & Philosophy

SIM-001 follows a **layered evidence model**:

| Layer | Role | Authority Level |
|------|------|----------------|
| Email System | Phishing delivery & mailbox authentication | Context |
| Identity (AD) | User identity & authentication telemetry | Attribution |
| Endpoint (Windows) | Process execution visibility | Primary Execution Signal |
| Network | Outbound HTTP/DNS telemetry | **Primary Click Confirmation** |
| SIEM | Cross-source correlation | Investigation Platform |

**Authoritative Detection Model:**

Phishing link clicks delivered via webmail typically open within an existing
browser session. In this scenario:

- The phishing URL may **not** appear in `Process_Command_Line`
- Browser child processes may not contain navigation context
- Endpoint telemetry alone may not reveal the full URL

Therefore, detection authority is established through:

1. Browser execution evidence (Event ID 4688 / Sysmon Event ID 1)
2. Time correlation with outbound HTTP connection to attacker host
3. Server-side confirmation (Apache access logs / tracking script logs)

This mirrors real-world SOC methodology where **multi-layer correlation**
confirms user interaction.

---

## 🧪 Attack Scenario Overview

1. Enterprise identity authenticates to mailbox  
2. Phishing email delivered  
3. User opens message (webmail)  
4. User clicks malicious link  
5. Existing browser session initiates outbound HTTP request  
6. Endpoint logs record browser process activity  
7. Attacker infrastructure logs inbound request  
8. SIEM correlates identity, endpoint, and network activity  

---

## 🧭 Simulation Paths

### Path A – Full Email Chain (Recommended)
Mail delivery → Mail login → Link click → Outbound connection → Endpoint logs → SIEM correlation

### Path B – Endpoint-Only Execution
Manual browser launch → Outbound connection → Endpoint logs → SIEM detection

Both paths validate phishing click detection logic.

---

## 📊 Detection Logic Summary

Primary detection identifies:

- Browser process execution (`chrome.exe`, `msedge.exe`, `firefox.exe`)
- User context (`it.helpdesk1`)
- Time-aligned outbound HTTP request to Kali server
- Server-side receipt of request (Apache access log / track.php)

Detection relies on **correlation**, not URL string presence in command line.

Email logs provide investigation context:

- Message delivery
- Mailbox authentication
- Pre-execution user behavior

Identity telemetry strengthens attribution:

- Verified enterprise account context  
- Authentication event history  
- Alignment between identity activity and endpoint execution  

This reflects enterprise SOC workflows.

---

## 📁 Simulation Files

- **README.md** — Overview & detection logic  
- **steps.md** — Reproduction procedure  
- **queries.md** — SPL detection queries  
- **logs.md** — Evidence artifacts  
- **alert-config.md** — Detection alert configuration  
- **issues-and-resolutions.md** — Troubleshooting log  
- **screenshots/** — Evidence images and validation proof  

---

## ⚠️ Issues Encountered & Resolutions

Operational challenges encountered during SIM-001 setup, telemetry validation,
index creation, and detection tuning are documented in:

👉 **[SIM-001 – Issues & Resolutions](../../issues-and-resolutions/sim-001-phishing-email.md)**

Each issue entry follows:

**Issue → Root Cause → Resolution → Verification → Overall Takeaway → Status**

This ensures reproducibility and professional troubleshooting documentation.

---

## ✅ Success Criteria

| Validation Requirement | Expected Result |
|------------------------|-----------------|
| Phishing email delivered | Mail server logs confirm message |
| User interaction | Browser execution observed |
| Process execution logged | Event ID 4688 / Sysmon EID 1 recorded |
| Outbound connection observed | HTTP request to Kali IP confirmed |
| Server-side confirmation | Apache access log / track.php log entry |
| Log ingestion | Events searchable in Splunk |
| Detection logic triggered | Correlation query returns results |
| Alert generated | Detection alert fires successfully |
| Identity telemetry present | AD and/or mail authentication events observable |

---

## 🛡 Governance & Control Alignment

This simulation supports detection control validation.  
Full governance alignment is maintained in:

**[CV-SIM001 — Endpoint Phishing Link Detection Control Validation](../../GRC-Program/control-validations/CV-SIM001-Endpoint-Execution-Control.md)**

---

## 📁 Evidence Naming Convention

| Evidence ID Format | Example |
|-------------------|---------|
| Execution Evidence | `E-SIM001-###` |
| Screenshot | `sim001-evidence-###-description.png` |

---

## 🔍 Final Validation & Status Check

| Check | Status |
|------|--------|
| Endpoint telemetry confirmed | ✅ |
| SIEM ingestion verified | ✅ |
| Network correlation validated | ✅ |
| Detection alert fired | ✅ |
| Evidence captured | ✅ |
| Simulation reproducible | ✅ |

---

## 🏁 Outcome

This simulation demonstrates the ability to:

- Detect phishing-triggered browser interaction  
- Correlate identity, endpoint, and network telemetry  
- Confirm outbound communication to attacker infrastructure  
- Perform multi-layer SOC investigation  
- Map detection to MITRE ATT&CK  

**Status:** Detection validated and reproducible  
**Detection Authority:** Multi-Layer Correlation (Endpoint + Network)
