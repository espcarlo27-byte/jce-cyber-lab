# SIM-004 – Sysmon Process Create (T1059)

## 🎯 Goal

Simulate and detect **command and scripting interpreter execution**
on a Windows 11 endpoint by validating that:

- Process creation events are captured via **Sysmon Event ID 1**
- Command-line arguments are fully visible
- Parent → child process relationships are observable
- Splunk can reliably detect suspicious execution behavior
- Execution telemetry establishes a baseline for post-exploitation detection

This simulation validates the **Execution** phase of the attack lifecycle
and serves as a foundation for subsequent privilege escalation analysis.

---

## 🔗 Simulation Progression Context

This simulation focuses on **baseline execution visibility**.

It answers the fundamental SOC questions:
- What executed?
- How was it executed?
- From which parent process?

The **next simulation (SIM-005 – Privilege Escalation)** builds directly on
this telemetry by introducing **elevated integrity context**, validating how
the same process creation signals indicate **post-exploitation escalation**
once security boundaries are crossed.

---

## 🧩 MITRE ATT&CK Mapping

- **Technique:** T1059 – Command and Scripting Interpreter  
- **Tactic:** Execution (TA0002)

---

## 🏗 Lab Components Used

| Component | Role |
|---------|-----|
| **Windows 11 Endpoint** | Victim host |
| **Local User** | Command execution |
| **Sysmon** | Process creation telemetry |
| **Splunk Enterprise (Ubuntu)** | SIEM / Detection |
| **Windows Server** | SOC console |

> ❌ Kali, Security Onion, and pfSense are **not required** for this simulation.

---

## 📂 Simulation Files

| File | Purpose |
|----|--------|
| `steps.md` | Exact, reproducible execution steps |
| `queries.md` | SPL detection logic |
| `alert-config.md` | Splunk alert definition |
| `logs.md` | Representative log evidence |
| `screenshots/` | Visual proof of execution and detection |

---

## 🔍 Detection Strategy

Detection is based on **behavioral execution signals**, not malware signatures.

### Primary Signals
- **Sysmon Event ID 1 – Process Create**
- `Image`
- `CommandLine`
- `ParentImage`
- `User`

### Key Detection Principles
- Command interpreters are high-risk by nature
- Parent process context provides execution intent
- Command-line visibility dramatically improves fidelity
- Execution telemetry is foundational for later escalation detection

---

## 🏁 Status

**Simulation Status:** 🧪 In Progress
