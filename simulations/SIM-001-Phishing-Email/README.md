# SIM-001 – Phishing Email (T1566.002) – Spearphishing Link

---

## 🎯 Goal

Detect phishing-based initial access where a user receives a malicious email, clicks a link, and launches a browser process containing a suspicious URL in the command line.

The simulation validates **endpoint-authoritative detection** enhanced with **email telemetry context**, modeling how real SOC investigations trace phishing attacks from delivery to execution.

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
| Windows Server (AD) | User identity & authentication context |
| Mail Server (Zimbra) | Phishing email delivery & mailbox access logs |
| Splunk Enterprise | Log ingestion, correlation, alerting |
| Splunk Universal Forwarder | Endpoint log forwarding |
| Kali Linux | Hosts phishing landing page |
| Security Onion (Optional) | Supplemental network telemetry |

---

## 🧠 Detection Architecture & Philosophy

SIM-001 follows a **layered evidence model**:

| Layer | Role | Authority Level |
|------|------|----------------|
| Email System | Phishing delivery & mailbox activity | Context |
| Identity (AD) | User attribution | Attribution |
| Endpoint (Windows) | Process execution visibility | **Primary Detection Signal** |
| SIEM | Cross-source correlation | Investigation Platform |
| Network (Optional) | DNS / HTTP traces | Supplemental |

**Authoritative Detection Source:**  
Windows Security Event ID **4688** and/or **Sysmon Event ID 1** containing suspicious URL data in the process command line.

Network telemetry may supplement investigation when available, but endpoint process telemetry remains the detection authority.

---

## 🧪 Attack Scenario Overview

1. Phishing email delivered to user mailbox  
2. User logs into mail and opens message  
3. User clicks malicious link  
4. Browser launches with URL in command line  
5. Endpoint logs record process creation  
6. SIEM correlates activity for detection and investigation  

---

## 🧭 Simulation Paths

### Path A – Full Email Chain (Recommended)
Mail delivery → Mail login → Link click → Browser execution → Endpoint logs → SIEM detection

### Path B – Endpoint-Only Execution
Manual browser launch with URL when mail layer is offline → Endpoint logs → SIEM detection

Both paths validate the same detection logic.

---

## 📊 Detection Logic Summary

Primary detection identifies:

- Browser process (chrome.exe, msedge.exe, firefox.exe)
- Command line containing HTTP/HTTPS URL
- User context
- Time correlation

Email logs provide investigation context:

- Message delivery
- Mailbox access
- Pre-execution user activity

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

Operational challenges encountered during SIM-001 setup, telemetry validation, and detection tuning are documented in the dedicated log:

👉 **[SIM-001 – Issues & Resolutions](../../issues-and-resolutions/sim-001-phishing-email.md)**

This log includes:

- Telemetry ingestion issues  
- Field parsing and normalization problems  
- Detection logic tuning adjustments  
- Lab environment constraints  
- Validation re-tests after fixes  

Each issue entry follows a structured format:

**Issue → Root Cause → Resolution → Verification → Overall Takeaway → Status**

Maintaining a formal Issues & Resolutions log ensures:

- Reproducibility  
- Change tracking  
- Detection reliability  
- Professional troubleshooting documentation practices  

---

## ✅ Success Criteria

| Validation Requirement | Expected Result |
|------------------------|-----------------|
| Phishing email delivered | Mail server logs confirm message |
| User interaction | Mail access or browser launch observed |
| Process execution logged | Event ID 4688 / Sysmon EID 1 recorded |
| URL present in command line | Suspicious URL visible in logs |
| Log ingestion | Events searchable in Splunk |
| Detection logic triggered | Correlation query returns results |
| Alert generated | Detection alert fires successfully |

---

## 🛡 Governance & Control Alignment

This simulation supports formal detection control validation.  
Full governance, framework alignment, and compliance documentation are maintained in:

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
| Correlation search validated | ✅ |
| Detection alert fired | ✅ |
| Evidence captured | ✅ |
| Simulation reproducible | ✅ |

---

## 🏁 Outcome

This simulation demonstrates the ability to:

- Detect phishing-triggered execution at the endpoint  
- Attribute activity to a user  
- Investigate multi-layer telemetry in a SIEM  
- Map detection to MITRE ATT&CK  

**Status:** Detection validated and reproducible  
**Detection Authority:** Endpoint Telemetry (4688 / Sysmon)

