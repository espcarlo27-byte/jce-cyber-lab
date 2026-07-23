# ⚠️ Issues & Resolutions – Index
This section provides a centralized index of operational issues, limitations, and resolutions
encountered while executing the Enterprise Security Operations Environment (JCE) simulations.

Rather than hiding failures, this Enterprise Security Operations Environment (JCE) documents real-world challenges transparently, reflecting
how security operations teams troubleshoot, adapt, and validate detections in imperfect environments.

Each simulation has a dedicated Issues & Resolutions document with:
- Description of the issue
- Impact on detection or validation
- Root cause analysis
- Resolution steps taken
- Validation evidence
- Lessons learned

---

## 📂 Issues & Resolutions Repository Structure

```powershell
issues-and-resolutions/
├── sim-001-phishing-email.md
├── sim-002-dns-tunneling.md
├── sim-003-sql-injection.md
├── sim-004-sysmon-process-create.md
└── sim-005-privilege-escalation.md
```

---

## 🧩 Simulation Issue Index

| Simulation ID | Scenario | Status | Key Issue Themes | Link |
|---------------|----------|--------|------------------|------|
| SIM-001 | T1566.002 – Phishing Email | ✅ Resolved | Forwarder configuration, logging gaps, audit policy tuning, network visibility | [View](sim-001-phishing-email.md) |
| SIM-002 | T1071.004 – DNS Tunneling | ✅ Resolved | ECS query methodology, Zeek DNS visibility, behavioral detection, threshold tuning | [View](sim-002-dns-tunneling.md) |
| SIM-003 | T1190 – SQL Injection | ✅ Resolved | Network-only detection, IDS alert tuning, lack of app logs, black-box testing | [View](sim-003-sql-injection.md) |
| SIM-004 | T1059 – Sysmon Process Create | ✅ Resolved | Baseline process noise, parent–child relationships, command-line context | [View](sim-004-sysmon-process-create.md) |
| SIM-005 | T1055 – Privilege Escalation | ✅ Resolved | Sysmon indexing, host context mismatch, UAC elevation, disk exhaustion | [View](sim-005-privilege-escalation.md) |


---

## 🧠 Why This Section Exists
Security labs rarely operate in perfect conditions.
This index demonstrates:
- ✅ Real troubleshooting under constraints
- ✅ Evidence-based root cause analysis
- ✅ Understanding of SIEM, endpoint, and NSM limitations
- ✅ Honest validation (no forced success)
- ✅ SOC-ready documentation practices

This mirrors real incident response and detection engineering workflows, where:
- Tooling breaks
- Data is missing
- Engineers adapt and document

---

## 🏁 Status
- Issues fully documented per simulation
- Linked from each simulation README
- Aligned with Detection Validation Matrix
- Maintained as a living operational record

> “Good detections don’t come from perfect labs — they come from engineers who know how to troubleshoot broken ones.”
