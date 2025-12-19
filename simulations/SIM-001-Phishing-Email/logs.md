# SIM-001 – Phishing Email (T1566.002) – Log Evidence

This file contains **symbolic and representative log evidence** captured during
SIM-001, demonstrating **user-driven phishing link execution** resulting in
endpoint process creation and outbound network access.

The logs below reflect **actual telemetry observed during execution** and are
used to validate:
- Detection logic in `queries.md`
- Alert logic in `alert-config.md`

Windows Security Event ID **4688** is treated as the **primary authoritative
endpoint source**, while network telemetry provides **supplemental confirmation**
of phishing-driven web access.

---

## 🧾 Log Sources Used

- **Windows Security Log (Event ID 4688)** – Primary process creation telemetry
- **Network / Web Server Logs (HTTP)** – Supplemental confirmation of link access
- **SIEM Alert Event** – Detection validation artifact

> ⚠️ **Important Behavior Note**  
> Phishing detections frequently rely on **benign parent processes**
> (e.g., browsers or email clients). Detection logic must therefore
> correlate **user context + destination + timing**, not process name alone.

---

## 🔄 Field Normalization Notes

The following field mappings were confirmed as reliable in this lab environment:

### Windows Security (Event ID 4688)
- `user` → normalized as **actor**
- `New_Process_Name`
- `Process_Command_Line`
- `host`

### Network Telemetry (HTTP)
- `src_ip`
- `dest_ip`
- `dest_port`
- `method`
- `user_agent`

Alert metadata fields were preserved to support simulation traceability.

---

## 1. Baseline User Activity (Browser Execution)

**Source:** Windows Security  
**Event ID:** 4688  
**User Context:** `LAB\testuser`

```text
Time: 2025-12-09 01:13:03
Host: Windows11Pro
Account_Name: LAB\testuser
New_Process_Name: C:\Program Files\Google\Chrome\Application\chrome.exe
Process_Command_Line: "C:\Program Files\Google\Chrome\Application\chrome.exe"
```

Interpretation:
- Standard browser execution by a non-privileged user
- No immediate indication of malicious activity
- Establishes baseline user behavior

---

## 2. Phishing Link Execution (Suspicious Command-Line)

Source: Windows Security
Event ID: 4688
User Context: LAB\testuser
```text
Time: 2025-12-09 01:13:03
Host: Windows11Pro
Account_Name: LAB\testuser
New_Process_Name: C:\Program Files\Google\Chrome\Application\chrome.exe
Process_Command_Line: "C:\Program Files\Google\Chrome\Application\chrome.exe" http://10.0.0.30:8080
```

Interpretation:
- Browser launched with direct URL argument
- URL points to non-standard internal web server
- Primary endpoint-based phishing execution signal

---

## 3. Network Access to Phishing Infrastructure

Source: Network Telemetry
Protocol: HTTP
Client Context: Windows 11 Endpoint
```text
Source IP: 10.0.0.50
Destination IP: 10.0.0.30
Destination Port: 8080
Protocol: HTTP
Method: GET
User-Agent: Chrome
```

Interpretation:
- Outbound HTTP request following browser execution
- Confirms successful link access
- Correlates directly with endpoint process creation event

---

## 4. Alert Trigger Confirmation

Source: SIEM Alert Event
Alert Name: LAB-SIM-001-PHISHING-ALERT
```text
Trigger Time: 2025-12-09 01:13:10
Host: Windows11Pro
User: LAB\testuser
Process: chrome.exe
URL: http://10.0.0.30:8080
Severity: Medium
Status: Triggered
```

Interpretation:
- Detection logic successfully correlated endpoint and network signals
- Alert fired within seconds of user interaction
- Confirms operational detection pipeline

---

## 🔗 Correlated Phishing Execution Timeline
```text
01:13:03 – User launches Chrome (baseline execution)
01:13:03 – Chrome executed with phishing URL argument
01:13:05 – HTTP GET request sent to attacker-controlled server
01:13:10 – SIEM alert triggered
```

Conclusion:  
A user-initiated phishing link resulted in browser execution with a malicious
destination, followed by confirmed network access and successful alerting.

---

# 🧠 Detection Relevance

These log events directly support:
- Detection queries in `queries.md`
- Alert logic in `alert-config.md`
- Symbolic ID: LAB-SIM-001-PHISHING-ALERT

The combination of:
- User context
- Suspicious command-line parameters
- Correlated network access

provides a high-confidence phishing execution signal suitable for SOC alerting
and triage.

---

## 🏁 Status
- [x] Endpoint process execution captured
- [x] Network access validated
- [x] Correlated timeline established
- [x] Alert successfully triggered
- [x] Simulation complete
