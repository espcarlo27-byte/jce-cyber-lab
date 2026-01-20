# SIM-001 – Phishing Email (T1566.002) – Log Evidence

This file contains **symbolic and representative log evidence** captured during
SIM-001, demonstrating **user-driven phishing link execution** resulting in
endpoint process creation.

SIM-001 is designed as an **endpoint-driven detection**.
Windows Security Event ID **4688** is treated as the **authoritative telemetry source**.
Network telemetry, when available, is included as **optional supplemental context**.

The logs below are used to validate:
- Detection logic in `queries.md`
- Alert logic in `alert-config.md`

---

## 🧾 Log Sources Used

### Required (Authoritative)
- **Windows Security Log (Event ID 4688)** – Process creation and command-line telemetry

### Optional (Supplemental)
- **Network / Web Server Logs (HTTP)** – Supporting confirmation of link access
- **SIEM Alert Event** – Detection validation artifact

> ⚠️ **Important Behavior Note**  
> Phishing detections commonly involve **benign parent processes**
> (e.g., browsers or email clients).  
> Detection logic must therefore rely on **user context, command-line parameters,
> and execution timing**, not process name alone.

---

## 🔄 Field Normalization Notes

The following field mappings were confirmed as reliable in this lab environment:

### Windows Security (Event ID 4688)
- `user` → normalized as **actor**
- `New_Process_Name`
- `Process_Command_Line`
- `host`

### Optional Network Telemetry (HTTP)
- `src_ip` (endpoint, DHCP-assigned)
- `dest_ip` (phishing host, DHCP-assigned)
- `dest_port`
- `http.method`
- `user_agent`

Alert metadata fields were preserved to support simulation traceability.

---

## 1. Baseline User Activity (Browser Execution)

**Evidence ID:** E-SIM001-002  
**Source:** Windows Security  
**Event ID:** 4688  
**User Context:** `LAB\labuser`

```text
Time: 2025-12-09 01:13:03
Host: Windows11Pro
Account_Name: LAB\labuser
New_Process_Name: C:\Program Files\Google\Chrome\Application\chrome.exe
Process_Command_Line: "C:\Program Files\Google\Chrome\Application\chrome.exe"
```

Interpretation:
- Standard browser execution by a non-privileged user
- No immediate indication of malicious activity
- Establishes baseline user behavior

---

## 2. Phishing Link Execution (Primary Detection Signal)

**Evidence ID:** E-SIM001-003
**Source:** Windows Security
**Event ID:** 4688
**User Context:** `LAB\labuser`
```text
Time: 2025-12-09 01:13:03
Host: Windows11Pro
Account_Name: LAB\labuser
New_Process_Name: C:\Program Files\Google\Chrome\Application\chrome.exe
Process_Command_Line: "C:\Program Files\Google\Chrome\Application\chrome.exe" http://phish-sim.local/policy
```

Interpretation:
- Browser launched with direct URL argument
- Indicates a user-initiated link click
- Serves as the authoritative phishing execution signal

---

## 3. (Optional) Network Access Confirmation

**Evidence ID:** E-SIM001-006
**Source:** Network Telemetry (Optional)
**Protocol:** HTTP
```text
Source IP: <Windows endpoint – DHCP-assigned>
Destination IP: <Phishing host – DHCP-assigned>
Destination Port: 8080
Protocol: HTTP
Method: GET
User-Agent: Chrome
```

Interpretation:
- Outbound HTTP request consistent with a phishing link click
- Provides supporting context when network telemetry is available
- Not required for SIM-001 detection validation

> Note:
> Network telemetry may not always be present due to TLS encryption,
> sensor placement, or lab resource constraints.

---

## 4. Alert Trigger Confirmation

**Evidence ID:** E-SIM001-005
**Source:** SIEM Alert Event
**Alert Name:** `LAB-SIM-001-PHISHING-ALERT`
```text
Trigger Time: 2025-12-09 01:13:10
Host: Windows11Pro
User: LAB\labuser
Process: chrome.exe
URL: http://phish-sim.local/policy
Severity: Medium
Status: Triggered
```

Interpretation:
- Detection logic successfully correlated endpoint and network signals
- Alert fired within seconds of user interaction
- Confirms operational detection and alerting pipeline
  
---

## 🔗 Correlated Phishing Execution Timeline
```text
01:13:03 – User launches Chrome (baseline execution)
01:13:03 – Chrome executed with phishing URL argument
01:13:10 – SIEM alert triggered
```

Conclusion:  
A user-initiated phishing link resulted in browser execution with a
suspicious destination, followed by successful detection and alerting
based on authoritative endpoint telemetry.

---

# 🧠 Detection Relevance

These log events directly support:
- Detection queries in `queries.md`
- Alert logic in `alert-config.md`
- Symbolic ID: LAB-SIM-001-PHISHING-ALERT

The combination of:
- User context
- Browser execution
- URL presence in command line

provides a high-confidence phishing execution signal suitable for SOC alerting
and triage.

---

## 🏁 Status
- [x] Endpoint process execution captured
- [x] Network access validated
- [x] Correlated timeline established
- [x] Alert successfully triggered
- [x] Simulation complete

> Endpoint telemetry is authoritative.
> Network telemetry is optional and supplemental.
