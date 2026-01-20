# SIM-001 – Phishing Email (T1566.002) – Spearphishing Link

## 🎯 Goal

Simulate a phishing email that delivers a suspicious URL to a user, then validate that:

- **Endpoint telemetry** (Windows Security Event 4688 with command-line logging)
  records browser execution
- **Splunk** correlates endpoint execution and URL activity into a detection
- A **validated alert** is generated for analyst review

> **Note:**  
> Network telemetry via Security Onion (Suricata/Zeek) is **optional** and
> used only for supplemental validation.  
> The authoritative detection signal for SIM-001 is **endpoint telemetry**.

This simulation supports the **LAB-SIM-001** row in the
`detection-validation-matrix.md`.

---

## 🧩 MITRE ATT&CK Mapping

- **Technique:** T1566.002 – Spearphishing Link  
- **Tactic:** Initial Access (TA0001)

---

## 🏗 Lab Components Used

### Attacker (Simulation Host)
- **Kali Linux**
  - IP address assigned via **DHCP**
  - Hosts a phishing landing page using a lightweight Python HTTP server
  - *(Optional — may be replaced with a symbolic or local URL for endpoint-only validation)*

### Victim Endpoint
- **Windows 11**
  - IP address assigned via **DHCP**
  - Windows Security Auditing enabled (EventCode 4688)
  - Command-line process auditing enabled
  - Splunk Universal Forwarder installed and running

### Security & Infrastructure Stack
- **pfSense (10.0.0.1)**
  - Primary **DHCP server**
  - Primary **DNS resolver**
  - Routing, NAT, and traffic visibility point

- **Ubuntu Server – Splunk Enterprise**
  - IP address assigned via **DHCP**
  - Central log ingestion, correlation, and alerting platform

### Optional Network Validation
- **Security Onion (Optional)**
  - Suricata + Zeek for passive network visibility
  - Used only for **supplemental confirmation**
  - Not required for SIM-001 detection or alert validation

---

## 🧠 Network & DNS Design Rationale

This simulation intentionally uses **pfSense as the DHCP server and DNS resolver**
to reflect real-world SOC environments where:

- Endpoint IP addresses are **dynamic**
- DNS visibility is centralized at the **network layer**
- Detections do not rely on domain-joined behavior
- Correlation is based on **user context, hostnames, and process execution**
  rather than fixed IP addresses

This ensures detection logic remains **portable, resilient, and environment-agnostic**.

---

## 📂 Files in This Simulation

- `steps.md` – Exact steps used to perform the simulation
- `logs.md` – Symbolic sample logs (endpoint and alert evidence)
- `queries.md` – SPL used to hunt and detect this activity
- `alert-config.md` – Splunk alert configuration and symbolic ID
- `screenshots/` – Evidence:
  - Email being viewed / phishing content
  - Chrome execution evidence
  - URL detection in Splunk
  - Correlation query
  - Alert configuration
  - Alert firing

---

## ✅ Success Criteria (Validated)

- A user on the Windows 11 endpoint opens a **test phishing email**
  and clicks a suspicious URL ✅  
- Windows generates **EventCode 4688** for `chrome.exe` ✅  
- The phishing URL appears in **`Process_Command_Line`** ✅  
- Splunk correlates endpoint execution and URL activity ✅  
- Splunk triggers an alert with symbolic ID:

### LAB-SIM-001-PHISHING-ALERT

✅ **All success criteria met and validated with screenshots.**

---

## 🔍 Detection Summary

| Component | Evidence |
|----------|----------|
| Endpoint Execution | Windows Security EventCode 4688 |
| Command-Line Capture | `Process_Command_Line` |
| Phishing URL | URL passed to browser |
| Primary Detection | Endpoint telemetry |
| SIEM Correlation | Splunk SPL |
| Alerting | `LAB-SIM-001-PHISHING-ALERT` |
| Validation | Screenshots + Symbolic Logs |

> Network telemetry (Suricata/Zeek) may be used as **optional supporting evidence**
> when available, but is not required for detection validity.

---

## 🛡 GRC Control Validation (Governance / Risk / Compliance)

This simulation is also treated as a **security control test** to support audit readiness
and continuous improvement in the JCE Cyber Lab security program.

### 🎯 Control Objective

Ensure the environment can **detect and alert on phishing link execution**
using **authoritative endpoint telemetry** with repeatable evidence.

### 🧩 Applicable Framework Mapping (NIST CSF)

| Function | Category | Mapping |
|---------|----------|---------|
| Protect | PR.AT | Security Awareness & user-risk simulation validation |
| Detect | DE.CM | Continuous monitoring via endpoint telemetry + SIEM correlation |
| Detect | DE.AE | Detect anomalous/suspicious activity (URL invocation via browser execution) |
| Respond | RS.AN | Analyst validation using logs + SIEM evidence |

### ✅ Control(s) Validated

| Control Area | Control Statement | Validation Method | Result |
|-------------|-------------------|------------------|--------|
| Logging & Monitoring | Process execution events are logged with command-line details | Windows Security EventCode 4688 with command-line logging | Pass ✅ |
| Detection Engineering | Suspicious URL invocation is detectable via correlation logic | Splunk SPL correlation search | Pass ✅ |
| Alerting | A validated alert is generated for review | Splunk alert firing with symbolic ID | Pass ✅ |

### 📌 Evidence Collected (Audit-Ready)

| Evidence ID | Description | Source | Location |
|------------|-------------|--------|----------|
| E-SIM001-001 | Email viewed / phishing content | Windows 11 | `screenshots/` |
| E-SIM001-002 | Browser execution log (EventCode 4688) | Windows Security Logs | `logs.md` + `screenshots/` |
| E-SIM001-003 | URL present in `Process_Command_Line` | Windows Security Logs | `logs.md` |
| E-SIM001-004 | Splunk correlation query results | Splunk | `queries.md` + `screenshots/` |
| E-SIM001-005 | Alert configuration + alert firing evidence | Splunk | `alert-config.md` + `screenshots/` |

### 🧾 Compliance/Audit Readiness Notes

- This simulation produces defensible evidence suitable for **audit validation**
  of monitoring and detection controls in a SOC/SIEM environment.
- Optional network telemetry (Zeek/Suricata) may support investigation but is
  **not required** for control validity in this SIM.

### 👤 Control Ownership & Governance

| Item | Value |
|------|-------|
| Control Owner | JCE (Lab Owner / Security Program Owner) |
| Control Type | Preventive + Detective |
| Test Frequency | Quarterly (or after major environment changes) |
| Evidence Retention | 90 days minimum (lab standard) |
| Exception Handling | If telemetry/alert fails → record issue in Issues & Resolutions and re-test after remediation |

### 🟢 Control Test Status

**Control Test Result:** Pass ✅  
**Control Status:** Implemented and Verified  
**Linked Detection ID:** `LAB-SIM-001-PHISHING-ALERT`

---

## 🧪 Final Validation

End-to-end validation confirmed that phishing-related **endpoint activity**
was successfully captured, correlated, and surfaced through the detection pipeline.

- **Endpoint Validation:**  
  Windows Security EventCode **4688** with command-line logging confirmed browser
  execution and URL invocation by the user.

- **Correlation Validation:**  
  Splunk successfully correlated endpoint execution events and produced
  a validated detection.

- **Alert Validation:**  
  The detection alert (`LAB-SIM-001-PHISHING-ALERT`) fired as expected and was
  confirmed with supporting screenshots and symbolic logs.

**Result:**  
SIM-001 successfully validates phishing link detection using authoritative
endpoint telemetry, with optional network confirmation when available.

---

## 🧾 Status Checklist (Final)

- [x] Steps executed  
- [x] Logs captured and saved to `logs.md`  
- [x] SPL queries tested and refined  
- [x] Splunk alert configured and tested  
- [x] Screenshots captured and saved to `screenshots/`  
- [x] Detection matrix updated to “Validated”  

---

## ⚠️ Issues Encountered & Resolutions

During execution of this simulation, multiple real-world operational and detection
engineering challenges were encountered and resolved, including:

- Log ingestion failures
- Forwarder authentication problems
- Missing audit policies
- Endpoint execution gaps
- Network visibility constraints

Each issue was investigated, root-caused, and resolved using standard SOC
troubleshooting techniques.

👉 **Full technical breakdown:**  
[SIM-001 – Issues & Resolutions](../../issues-and-resolutions/sim-001-phishing-email.md)

---

## ✅ FINAL STATUS

**SIM-001 – Phishing Email Detection is COMPLETE, VALIDATED, and PRODUCTION-READY.**

This simulation demonstrates a realistic SOC workflow:

- User-driven attack execution  
- Authoritative endpoint telemetry validation  
- Detection engineering and alerting  
- Operational troubleshooting  
- Defensible analyst conclusions  

It serves as a **defensible, interview-ready detection project** in the
JCE Cyber Lab.
