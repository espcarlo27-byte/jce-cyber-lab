# SIM-001 – Phishing Email (T1566.002)
## Spearphishing Link – Multi-Layer SOC Validation

## 🎯 Simulation Objective

Simulate phishing-based initial access where:

1. A malicious link is delivered via enterprise email
2. An authenticated AD user clicks the link
3. Browser execution telemetry is recorded
4. Outbound communication to attacker infrastructure occurs
5. Multi-layer correlation validates the event in SIEM

This simulation models real-world SOC investigative methodology.

---

## 1. Prerequisites

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

## 📸 Evidence Capture Standard

For every validation step, capture screenshots that clearly show:

- Timestamp visible
- Hostname visible
- Username visible (if applicable)
- Event ID visible (for Windows logs)
- IP address visible (for network logs)
- Query used (for Splunk validation)

Screenshot Naming Convention:

sim001-A-evidence-###-description.png

Example:
`sim001-A-evidence-005-4688-browser.png`

All screenshots must be stored in:

`/SIM-001/screenshots/`

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

## 2. Environment Validation (Pre-Execution Checks)
### 2.1 Verify Windows Endpoint Logging

On Windows 11 (Administrator PowerShell):

```powershell
auditpol /get /category:"Detailed Tracking"
```

**Confirm:**  

**Process Creation** = ***Success***  

**Verify Event `ID 4688` exists:**  

**Event Viewer** → **Windows Logs** → **Security**  
**Filter Current Log** → **Event ID** = `4688`

**📸 Evidence Required:**  
`sim001-A-evidence-001-4688-eventviewer.png`

Screenshot must show:
- Event ID 4688
- Event Viewer path (Windows Logs → Security)
- Timestamp column visible

---

### 2.2 Verify Splunk Ingestion

**In Splunk:**
```spl
| tstats count where index=* by index
```

**Confirm:**  

- winevent_security
- winevent_sysmon (if enabled)

**📸 Evidence Required:**  
`sim001-A-evidence-002-index-validation.png`

Screenshot must show:
- winevent_security index
- Event count
- Host field visible

---

### 2.3 Verify Splunk Universal Forwarder

**On Windows 11:**
```powershell
Get-Service splunkforwarder
```

**Status must be:**
```sql
Running
```

---

### 2.4 Verify Kali Web Server

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

**📸 Evidence Required:**  
`sim001-A-evidence-003-apache-status.png`

Screenshot must show:
- apache2 service running
- Port 80 listening

---

## 3. Prepare Phishing Landing Page (Kali Linux)
### 3.1 Confirm Apache Document Root
```bash
sudo cat /etc/apache2/sites-enabled/000-default.conf | grep DocumentRoot
```

**Expected:**
```text
DocumentRoot /var/www/html
```

---

### 3.2 Place Phishing Files

The phishing simulation requires the following files to be deployed on the Kali web server:

- index.html
- track.php
- phish_log.txt

These files are located in this repository under:  
`SIM-001/phishing-files/`

---

#### 3.2.1 – Download Files from GitHub

Option A – Download Entire Repository (Recommended)

1. On your Kali VM, open Terminal.
2. Clone the repository:
   ```bash
   git clone <YOUR_GITHUB_REPO_URL>
   ```
3. Navigate to the phishing files directory:
   ```bash
   cd <repo-name>/SIM-001/phishing-files
   ```

Option B – Download Individually  

You may also:  

- Open each file in GitHub
- Click Raw
- Right-click → Save As
- Save to Kali Desktop

---

#### 3.2.2 – Move Files to Apache Web Root

Apache default web directory:
```css
/var/www/html/
```

---

#### 3.2.3 – Set Proper Permissions

Ensure Apache can write to phish_log.txt:
```bash
sudo chown www-data:www-data /var/www/html/phish_log.txt
sudo chmod 664 /var/www/html/phish_log.txt
```

---

#### 3.2.4 – Restart Apache
```bash
sudo systemctl restart apache2
```

---

#### 3.2.5 – Validate Deployment

From Windows 11 browser:
```cpp
http://<KALI_IP>/
```

Confirm:

- Landing page loads
- No directory listing
- "Continue" button visible

Test click:

- Click Continue
- You should be redirected to Microsoft
- Check log file:
   ```bash
   cat /var/www/html/phish_log.txt
   ```

You should see a log entry similar to:
   ```yaml
   TIME: 2026-02-16 23:57:12 | IP: 192.168.1.25 | ...
   ```

> ⚠ Important Notes
- Do NOT expose this server to the internet.
- This simulation is for controlled lab use only.
- Do NOT commit runtime logs (with IP addresses) back to GitHub.
- If permissions are incorrect, track.php will fail silently.

**📸 Evidence Required:**  
`sim001-A-evidence-003-landing-page.png`

Screenshot must show:

- Phishing landing page loaded
- URL visible in browser
- Correct Kali IP in address bar

---

### 3.3 Restart Apache
```bash
sudo systemctl restart apache2
```

---

### 3.4 Validate Landing Page

**From Windows 11 browser:**
```cpp
http://<KALI_IP>/
```

**Confirm:**

- Landing page loads
- No directory listing
- Continue button visible

**📸 Evidence Required:**  
`sim001-A-evidence-004-landing-page.png`

Screenshot must show:
- Phishing landing page loaded
- URL visible in browser address bar

---

## 4. Send Phishing Email (Zimbra)
### 4.1 Compose Email

- Log into Zimbra
- Send email to it.helpdesk1
- Insert link:
```cpp
http://<KALI_IP>/
```

***Record timestamp of delivery.***

---

### 4.2 Confirm Email Receipt

**On Windows 11:**

- Log in as it.helpdesk1
- Open mailbox
- Confirm phishing email present
- Capture screenshot BEFORE clicking

**📸 Evidence Required:**  
`sim001-A-evidence-005-email-received.png`

Screenshot must show:
- Email subject
- Sender
- Timestamp
- Link visible (do not click yet)

---

## 5. Identity Context Validation

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

**📸 Evidence Required:**  
`sim001-A-evidence-006-4624-logon.png`

Screenshot must show:
- EventCode = 4624
- user = it.helpdesk1
- host = Windows11Pro
- Timestamp visible

---

## 6. Execute Phishing Link Click
### 6.1 Click Link

- Click phishing link in webmail
- Landing page loads
- Click “Continue”
- Browser redirects to Microsoft

***Record exact timestamp.***

**📸 Evidence Required:**  
`sim001-A-evidence-007-link-clicked.png`

Screenshot must show:
- Landing page loaded
- Continue button clicked
- Timestamp recorded manually

---

## 7. SOC Investigation Phase
### 7.1 Endpoint Telemetry – Process Creation

***Search in Splunk:**
```spl
index=winevent_security EventCode=4688
user="it.helpdesk1"
| table _time host user new_process_name parent_process_name
| sort - _time
```

**Confirm the Following Indicators**

| Field              | Expected Value        |
|--------------------|-----------------------|
| new_process_name   | `chrome.exe`          |
| user               | `it.helpdesk1`        |
| host               | `Windows11Pro`        |
| Timestamp          | Aligns with click time |

> ⚠ **Note:**  
> When phishing is delivered via webmail inside an existing browser session,  
> the URL may **not** appear in `Process_Command_Line`.  
> This is expected browser behavior and does not indicate detection failure.

**📸 Evidence Required:**  
`sim001-A-evidence-008-4688-browser.png`

Screenshot must show:
- EventCode = 4688
- new_process_name = chrome.exe
- user = it.helpdesk1
- Timestamp visible
- Splunk query visible

---

### 7.2 Sysmon Validation (If Enabled)

If Sysmon is configured, validate browser execution telemetry:
```spl
index=winevent_sysmon EventCode=1
Image="*chrome.exe"
| table _time host user Image ParentImage CommandLine
| sort - _time
```

---

### 7.3 Network Telemetry – Authoritative Confirmation

On Kali:
```bash
sudo tail -n 20 /var/log/apache2/access.log
```

**Confirm:**

- Windows IP present
- GET / or GET /track.php
- Timestamp aligns with click

**📸 Evidence Required:**  
`sim001-A-evidence-009-apache-access-log.png`

Screenshot must show:
- Windows IP address
- GET / or GET /track.php
- Timestamp aligned with click

**Optional:**
```bash
cat /var/www/html/phish_log.txt
```

**Confirm:**

- IP logged
- Timestamp logged
- User agent present

**📸 Evidence Required:**  
`sim001-A-evidence-010-phish-log.png`

Screenshot must show:
- IP logged
- Timestamp logged
- User agent visible

---

## 8. Timeline Correlation

Construct the following correlation table based on collected evidence:

| Time     | Source              | Observation                    |
|----------|--------------------|--------------------------------|
| HH:MM:SS | Event ID 4624      | User session active            |
| HH:MM:SS | Event ID 4688      | Browser execution              |
| HH:MM:SS | Apache access.log  | HTTP request from victim host  |
| HH:MM:SS | phish_log.txt      | Click event logged             |

If all timestamps align within a 1–2 minute window, detection is considered validated.

This confirms the chain:

**Identity → Endpoint Execution → Outbound Connection → Attacker Receipt**

---

## 9. Detection & Alert Configuration

### Primary Detection Logic

The detection model for SIM-001 is based on correlation of:

- Browser process execution  
- Outbound connection to attacker-controlled IP  
- Timestamp proximity

**Example SPL:**
```spl
(
    index=winevent_security EventCode=4688 new_process_name="chrome.exe"
)
OR
(
    index=network_logs dest_ip="<KALI_IP>"
)
| sort - _time
```

### Alert Configuration

Create the following alert in Splunk:

| Setting        | Value                              |
|---------------|--------------------------------------|
| **Name**       | LAB-SIM-001-PHISHING-CLICK           |
| **Time Range** | Last 15 minutes                     |
| **Trigger**    | Number of results > 0               |
| **Severity**   | Medium                              |

Validate that the alert successfully triggers during simulation execution.

**📸 Evidence Required:**  
`sim001-A-evidence-011-alert-triggered.png`

Screenshot must show:
- Alert name
- Triggered status
- Time fired

---

## 10. Simulation Completion Checklist

| Validation Requirement              | Status |
|-------------------------------------|--------|
| Phishing email delivered            | ✅     |
| User interaction observed           | ✅     |
| Browser execution logged            | ✅     |
| Outbound HTTP request validated     | ✅     |
| Server-side confirmation present    | ✅     |
| SIEM correlation reproducible       | ✅     |
| Alert triggered                     | ✅     |

---

## 🏁 Final Status

| Category            | Result |
|---------------------|--------|
| Detection Authority | Multi-Layer Correlation |
| MITRE Technique     | T1566.002 – Spearphishing Link |
| Simulation Status   | COMPLETE & REPRODUCIBLE |




