# ⚠️ Issues & Resolutions – Index
This section provides a centralized index of operational issues, limitations, and resolutions
encountered while executing the JCE Cyber Lab simulations.

Rather than hiding failures, this lab documents real-world challenges transparently, reflecting
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
├── sim-003-privilege-escalation.md
└── sim-004-sql-injection.md
```

---

## 📋 Simulation Issue Index

| Simulation ID | Scenario                   | Status        | Key Issue Themes                                                                 | Link |
|---------------|----------------------------|---------------|----------------------------------------------------------------------------------|------|
| SIM-001       | T1566.002 – Phishing Email | ✅ Resolved   | Forwarder config, logging gaps, audit policy, network visibility                  | [View](sim-001-phishing-email.md) |
| SIM-002       | T1071.004 – DNS Tunneling  | ⚠️ Partial    | SO Eval limits, Elasticsearch auth failure, DNS ingest gaps, SIEM degradation    | [View](sim-002-dns-tunneling.md) |
| SIM-003       | T1055 – Privilege Escalation | ✅ Resolved | Sysmon indexing, host mismatch, UAC context, disk exhaustion                      | [View](sim-003-privilege-escalation.md) |
| SIM-004       | T1190 – SQL Injection      | ✅ Resolved   | Network-only detection, generic IDS alerts, lack of SIEM logs, black-box testing | [View](sim-004-sql-injection.md) |

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
