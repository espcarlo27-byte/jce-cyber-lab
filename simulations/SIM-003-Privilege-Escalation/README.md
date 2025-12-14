# SIM-003 – Privilege Escalation via UAC (T1055)

## 🎯 Goal

Demonstrate detection of privilege escalation by comparing
non-admin and elevated process execution on a Windows endpoint.

This simulation validates that:
- A standard domain user executes processes in a **medium integrity** context
- The same host later spawns processes in a **high integrity (administrator)** context
- Windows Security Event ID 4688 captures this transition
- Splunk can reliably detect and differentiate the escalation

---

## 🧩 MITRE ATT&CK Mapping

- **Technique:** T1055 – Privilege Escalation
- **Tactic:** Privilege Escalation (TA0004)

---

## 🏗 Lab Components Used

- **Windows 11 Endpoint** (10.0.0.50)
  - Domain joined: `local.lab`
  - Sysmon enabled
  - Splunk Universal Forwarder installed
- **Splunk Enterprise** (Ubuntu – 10.0.0.60)
- **Windows Server (AD)** – Domain Controller

---

## 📂 Files in This Simulation

- `steps.md` – Reproducible execution steps
- `queries.md` – Detection and validation SPL
- `alert-config.md` – Alert logic and configuration
- `logs.md` – Symbolic event samples
- `screenshots/` – Evidence of execution and detection

---

## ✅ Success Criteria

- Non-admin user (`labuser`) executes a process
- Elevated process is executed via UAC
- Windows logs Event ID 4688 for both contexts
- Elevated execution is logged under `administrator`
- Splunk detects and displays the privilege escalation

---

## ⚠️ Lessons Learned

- Windows Security logs often use `Account_Name` instead of `user`
- Elevated domain admins may be logged as **local `administrator`**
- Detection logic must normalize identity fields
- GUI-based elevation provides clearer telemetry than CLI reuse

---

## 🧾 Status

- [x] Baseline execution captured
- [x] Elevated execution captured
- [x] Detection queries validated
- [x] Alert logic prepared
- [x] Screenshots collected
