# SIM-001 – Phishing Email – Symbolic Logs

All logs below are symbolic representations of real telemetry captured during live validation.

---

## Endpoint Process Creation (Windows Event 4688)

```text
_time: 2025-12-09 01:13:03
host: Windows11Pro
user: LAB\testuser
New_Process_Name: C:\Program Files\Google\Chrome\Application\chrome.exe
Process_Command_Line: "C:\Program Files\Google\Chrome\Application\chrome.exe" http://10.0.0.30:8080
EventCode: 4688
simulation_id: SIM-001
symbolic_id: LAB-SIM-001-PHISHING-ALERT
```

---
---

## Network Access (Kali Web Server)

```text
src_ip: 10.0.0.50
dest_ip: 10.0.0.30
dest_port: 8080
protocol: HTTP
method: GET
user_agent: Chrome
simulation_id: SIM-001
```

---
---

## Alert Trigger Event

```text
alert_name: LAB-SIM-001-PHISHING-ALERT
severity: Medium
trigger_time: 2025-12-09 01:13:10
host: Windows11Pro
user: LAB\testuser
process: chrome.exe
url: http://10.0.0.30:8080
status: Triggered
```

---
---

## Status
- ✅ Symbolic logs validated
- ✅ Mapped to real telemetry
- ✅ Alert firing confirmed
