# SIM-001 – Phishing Email (T1566.002)
## Spearphishing Link – Multi-Layer SOC Validation

# 🎯 Simulation Objective

Simulate phishing-based initial access where:

1. A malicious link is delivered via enterprise email
2. An authenticated AD user clicks the link
3. Browser execution telemetry is recorded
4. Outbound communication to attacker infrastructure occurs
5. Multi-layer correlation validates the event in SIEM

This simulation models real-world SOC investigative methodology.

---

# 1️⃣ Prerequisites

Ensure the following components are operational before starting SIM-001.

| Component | Required? | Purpose in SIM-001 |
|------------|------------|--------------------|
| Windows Server (Active Directory) | ✅ | Provides enterprise identity, authentication telemetry, and user attribution |
| Windows 11 Endpoint | ✅ | Generates authoritative process execution telemetry (Event ID 4688 / Sysmon Event ID 1) |
| Splunk Enterprise | ✅ | Ingests logs, enables multi-layer correlation, and triggers alerts |
| Splunk Universal Forwarder | ✅ | Sends Windows logs to Splunk |
| Kali Linux | ✅ | Hosts phishing landing page and records inbound HTTP requests |
| Mail Server (Zimbra) | Optional | Delivers phishing email and generates mailbox authentication logs |
| Security Onion | Optional | Provides supplemental network telemetry |

---

## Detection Model Clarification

Detection for SIM-001 relies on **multi-layer correlation**, including:

- Endpoint process execution telemetry (Event ID 4688 / Sysmon)
- Identity telemetry (Active Directory logon events)
- Outbound HTTP connection to attacker infrastructure
- Server-side confirmation via Apache access logs

⚠ Note:

When phishing is delivered via webmail in an existing browser session, the phishing URL may **not** appear in `Process_Command_Line`. Detection authority is established through timestamp-aligned correlation across endpoint and network layers.

---

# 2️⃣ Environment Validation (Pre-Execution Checks)

---

## 2.1 Verify Windows Endpoint Logging

On Windows 11 (Administrator PowerShell):

```powershell
auditpol /get /category:"Detailed Tracking"
```

**Confirm:**  

**Process Creation** = ***Success***  

**Verify Event ID 4688 exists:**  

**Event Viewer** → **Windows Logs** → **Security**  
**Filter Current Log** → **Event ID** = `4688`

---

## 2.2 Verify Splunk Ingestion

**In Splunk:**
```spl
| tstats count where index=* by index
```

**Confirm:**  

- winevent_security
- winevent_sysmon (if enabled)

---

## 2.3 Verify Splunk Universal Forwarder

**On Windows 11:**
```powershell
Get-Service splunkforwarder
```

**Status must be:**
```sql
Running
```
