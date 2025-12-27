# SIM-004 – Sysmon Process Create (T1059)

## 🎯 Goal

Simulate and validate **process execution behavior** on a Windows 11 endpoint by establishing a **clean execution baseline** before privilege escalation.

This simulation validates that:

- Standard user processes execute normally
- Parent → child process relationships are visible
- Command-line arguments are captured
- Encoded PowerShell execution is observable
- Telemetry is reliably ingested into Splunk via **Sysmon Event ID 1**

This simulation intentionally serves as a **prerequisite baseline** for  
**SIM-005 – Privilege Escalation**, ensuring elevated execution detections are
built on understood normal behavior.

This simulation validates the **Execution** row in the  
[Detection Validation Matrix](../../detection-matrix/detection-validation-matrix.md).

---

## 🧩 MITRE ATT&CK Mapping

- **Technique:** T1059 – Command and Scripting Interpreter  
- **Tactic:** Execution (TA0002)

---

## 🏗 Lab Components Used

| Component | Role |
|---------|-----|
| **Windows 11 Endpoint** (`Windows11Pro`) | Execution host |
| **Standard User** (`local.lab\labuser`) | Process execution |
| **Sysmon** | Endpoint telemetry |
| **Splunk Enterprise (Ubuntu)** | SIEM / Detection |
| **Windows Server** | SOC console (Splunk UI access) |

> ❌ Kali, Security Onion, and pfSense are **not required** for this simulation.

---

## 📂 Simulation Files

| File | Purpose |
|----|--------|
| `steps.md` | Exact, reproducible execution steps |
| `queries.md` | SPL queries for execution visibility |
| `logs.md` | Symbolic + representative Sysmon events |
| `screenshots/` | Visual proof of telemetry and validation |

> ⚠️ No alert is defined in this simulation by design.  
> Alerting is introduced in **SIM-005** after baseline behavior is established.

---

## 🧪 What Was Simulated

1. **Baseline execution**
   - Standard user launches `cmd.exe`
   - Normal command execution (`whoami`)
   - Integrity level remains **Medium**

2. **Scripted execution**
   - `cmd.exe` spawns `powershell.exe`
   - PowerShell executes benign commands
   - Parent → child relationship observed

3. **Suspicious-looking but benign execution**
   - PowerShell launched with `-EncodedCommand`
   - No payload executed
   - Telemetry generated for encoded execution patterns

These steps generate **high-signal execution telemetry** without triggering alerts.

---

## 🔍 Detection Strategy

This simulation focuses on **visibility and validation**, not alerting.

### Primary Signals
- **Sysmon Event ID 1** (Process Create)
- `Image`
- `ParentImage`
- `CommandLine`
- `User`
- `IntegrityLevel`

### Key Detection Principles
- Execution context must be understood **before** escalation detection
- Parent–child relationships are more reliable than process name alone
- Encoded execution is observable even when benign
- Baseline noise must be measured before alerting

> Sysmon Event ID 1 is available at the endpoint and used as supplemental validation.
> Primary detection authority remains Windows Security Event ID 4688.

---

## 📸 Evidence Captured

The following screenshots were collected and stored in `screenshots/`:

- `sim004-sysmon-cmd-baseline.png` – Baseline cmd.exe execution
- `sim004-sysmon-parent-child.png` – cmd → powershell relationship
- `sim004-sysmon-encoded-command.png` – Encoded PowerShell execution
- `sim004-spl-execution-results.png` – SPL execution visibility

---

## ✅ Success Criteria

| Requirement | Status |
|-----------|-------|
| Sysmon Event ID 1 captured | ✅ |
| Parent–child execution visible | ✅ |
| Command-line logging verified | ✅ |
| Encoded execution observed | ✅ |
| SPL queries validated | ✅ |
| Screenshots captured | ✅ |
| Detection matrix updated | ⏳ |

---

## ⚠️ Issues & Resolutions

During execution of SIM-004, several **real-world detection engineering challenges**
were encountered related to execution telemetry volume and signal clarity.

These issues included:
- High baseline process noise
- Benign encoded PowerShell usage
- Parent–child process ambiguity
- Initial over-reliance on process names

All issues were **documented and addressed** as part of the baseline tuning process.

👉 **Full technical breakdown:**  
[SIM-004 – Issues & Resolutions](../../issues-and-resolutions/sim-004-sysmon-process-create.md)

---

## 🧠 Key Takeaways

- Execution detection requires **baseline understanding**
- Sysmon provides rich, high-fidelity execution telemetry
- Encoded execution is not inherently malicious
- Parent–child relationships are foundational for escalation detection
- Establishing execution context strengthens all downstream detections

---

## 🔗 Simulation Progression

This simulation directly enables:

➡️ **SIM-005 – Privilege Escalation (T1055)**  
Where elevated execution and UAC behavior are detected **on top of this baseline**.

---

## 🏁 Status

**Simulation Status:** 🧪 **Baseline Validated**

Execution telemetry is fully captured and understood.  
Alerting and escalation detection are introduced in **SIM-005**.

This simulation is suitable for
**SOC analyst / detection engineering portfolio presentation**.
