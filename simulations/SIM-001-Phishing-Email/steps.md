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

# 1. Prerequisites

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

# 2. Environment Validation (Pre-Execution Checks)

---

## 2.1 Verify Windows Endpoint Logging

On Windows 11 (Administrator PowerShell):

```powershell
auditpol /get /category:"Detailed Tracking"
```

**Confirm:**  

**Process Creation** = ***Success***  

**Verify Event `ID 4688` exists:**  

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

---

## 2.4 Verify Kali Web Server

**On Kali:**
```bash
sudo systemctl status apache2
```

**Confirm:**

- Active (running)

**Confirm listening port:**
```bash
sudo netstat -tulnp | grep apache
```

**Should show:**
```makefile
0.0.0.0:80
```

---

# 3. Prepare Phishing Landing Page (Kali Linux)
## 3.1 Confirm Apache Document Root
```bash
sudo cat /etc/apache2/sites-enabled/000-default.conf | grep DocumentRoot
```

**Expected:**
```text
DocumentRoot /var/www/html
```

---

## 3.2 Place Phishing Files

**Ensure the following files exist in:**
```text
/var/www/html/
```

- index.html
- track.php
- phish_log.txt

**If needed:**
```bash
sudo touch /var/www/html/phish_log.txt
sudo chmod 666 /var/www/html/phish_log.txt
```

---

## 3.3 Restart Apache
```bash
sudo systemctl restart apache2
```

---

## 3.4 Validate Landing Page

**From Windows 11 browser:**
```cpp
http://<KALI_IP>/
```

**Confirm:**

- Landing page loads
- No directory listing
- Continue button visible

---

# 4. Send Phishing Email (Zimbra)
## 4.1 Compose Email

- Log into Zimbra
- Send email to it.helpdesk1
- Insert link:
```cpp
http://<KALI_IP>/
```

***Record timestamp of delivery.***

---

## 4.2 Confirm Email Receipt

**On Windows 11:**

- Log in as it.helpdesk1
- Open mailbox
- Confirm phishing email present
- Capture screenshot BEFORE clicking

---

# 5. Identity Context Validation

**In Splunk:**
```spl
index=winevent_security EventCode=4624 user="it.helpdesk1"
| table _time host user Logon_Type
| sort - _time
```

**Confirm:**

- Successful AD logon
- Correct hostname
- Timestamp aligns with session

**This establishes:**  

**Enterprise Identity** → **Active Session** → **User Context**

---

6️⃣ Execute Phishing Link Click
6.1 Click Link

Click phishing link in webmail

Landing page loads

Click “Continue”

Browser redirects to Microsoft

Record exact timestamp.

7️⃣ SOC Investigation Phase
7.1 Endpoint Telemetry – Process Creation

Search in Splunk:
