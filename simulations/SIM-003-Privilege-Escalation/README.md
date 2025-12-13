# SIM-003 – Privilege Escalation (T1055)

## 🎯 Goal

Simulate a **local privilege escalation attempt** on a Windows 11 endpoint and validate that:

- Endpoint telemetry (Sysmon + Windows Security logs) records elevated process execution
- Splunk ingests and correlates the events
- Detection logic identifies abnormal privilege elevation behavior
- An alert is triggered to represent SOC detection of a privilege escalation attempt

This simulation demonstrates **endpoint-focused detection engineering** without reliance on network sensors.

---

## 🧩 MITRE ATT&CK Mapping

- **Tactic:** Privilege Escalation (TA0004)
- **Technique:** T1055 – Process Injection / Privilege Escalation (Simulated)

> Note: This lab uses a **safe, non-exploit-based simulation** to generate realistic telemetry without modifying the kernel or system integrity.

---

## 🏗 Lab Components Used

- **Victim Endpoint:**  
  - Windows 11 (10.0.0.50)  
  - Sysmon installed and configured  
  - Splunk Universal Forwarder running  

- **SIEM:**  
  - Splunk Enterprise (Ubuntu – 10.0.0.60)

- **SOC Console / Management:**  
  - Windows Server (used to access Splunk Web UI)

---

## 📂 Files in This Simulation

- `steps.md` – Exact, reproducible steps to execute the simulation
- `logs.md` – Symbolic and observed log samples (Sysmon + Security)
- `queries.md` – SPL used to detect and validate escalation behavior
- `alert-config.md` – Splunk alert definition and symbolic ID
- `screenshots/` – Evidence of execution, detection, and alerting

---

## 🧪 Attack Simulation Overview

The simulation generates telemetry by:

- Launching elevated processes using built-in Windows mechanisms
- Creating abnormal parent/child process relationships
- Triggering:
  - Sysmon **Event ID 1** (Process Create)
  - Windows Security **Event ID 4688**
- Capturing execution context differences between standard and elevated sessions

No malware, exploits, or system changes are introduced.

---

## ✅ Success Criteria

The simulation is considered successful when:

- An elevated process is executed on the Windows 11 endpoint
- Sysmon logs reflect the elevated process creation
- Windows Security logs confirm privileged execution
- Splunk queries return correlated results
- A Splunk alert fires with the symbolic ID:
  - `LAB-SIM-003-PRIVESC-ALERT`

---

## 🧾 Status

- [ ] Steps executed
- [ ] Sysmon and Security logs captured
- [ ] Detection queries validated in Splunk
- [ ] Alert configured and triggered
- [ ] Screenshots captured and saved
- [ ] Detection matrix updated

Update this checklist as the simulation is completed.

---

## 📌 Notes

This simulation focuses on **host-based privilege escalation detection**, which is a critical SOC capability when network telemetry is limited or unavailable.

It is intentionally designed to be:
- Safe
- Repeatable
- Interview-defensible
- Aligned with real SOC workflows

