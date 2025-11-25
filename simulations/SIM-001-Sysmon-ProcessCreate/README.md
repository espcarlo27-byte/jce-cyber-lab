# SIM-001 — Sysmon ProcessCreate Detection

## 🎯 Goal
Validate ingestion and detection of suspicious process execution events using Sysmon EventCode 1.

## 🧪 Scenario
Triggered `powershell.exe` launching `whoami.exe` to simulate suspicious activity.

## 🔧 Steps
1. Corrected ACLs for Sysmon channel using `wevtutil sl`
2. Restarted Splunk Forwarder
3. Verified Sysmon events flowing into `index=winlog`
4. Ran SPL query to detect `whoami.exe` execution

## ✅ Results
- Sysmon ProcessCreate events successfully ingested
- SPL query matched suspicious process chain
- Forwarder confirmed active

## 📎 Related Files
- [queries.md](queries.md)
- [logs.md](logs.md)
- [alert-config.md](alert-config.md)
- Screenshots in `/screenshots/`
