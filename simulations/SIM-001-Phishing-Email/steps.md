# SIM-001 – Phishing Email (T1566.002) – Execution Steps

**Detection Focus:** Endpoint Execution via Browser Command Line  
**Primary Telemetry:** Windows Security Event ID 4688  
**MITRE ATT&CK:** T1566.002 – Spearphishing Link

---

## 1. Prerequisites

Ensure the following components are operational before starting SIM-001.

| Component | Required? | Purpose in SIM-001 |
|-----------|-----------|-------------------|
| **Windows 11 Endpoint** | ✅ | Generates authoritative telemetry (Event 4688, process command line) |
| **Splunk Enterprise** | ✅ | Ingests logs, validates detection, and triggers alerts |
| **Splunk Universal Forwarder** | ✅ | Sends Windows logs to Splunk |
| **Kali Linux** | Optional | Hosts phishing landing page used in URL |
| **Mail Server (Zimbra)** | Optional | Delivers phishing email for realistic attack path |
| **Security Onion** | Optional | Provides supplemental network telemetry (not required) |

> Detection for SIM-001 depends on **endpoint process telemetry**, not network logs.

---

## 2. Execution – Option A: Phishing via Mail Server (Zimbra)

This execution path validates phishing detection using a **realistic email delivery flow**
while maintaining **endpoint telemetry as the authoritative detection source**.

Email infrastructure is used only as the **delivery mechanism**.

---

### 2.1 Prepare Phishing Landing Page (Kali Linux)

This step prepares the **phishing destination** that the user will later access
after clicking the link in the email.

The Kali host simulates a **malicious external web server**.

On the **Kali Linux VM**:

1. Open a terminal
2. Create a working directory for the phishing page:
```text
mkdir ~/phishing
cd ~/phishing
```
3. Create a simple HTML file:
```html
nano index.html
```
4. Insert basic content, for example:
```html
<html>
  <head>
    <title>HR Benefits Update</title>
  </head>
  <body>
    <h2>Benefits Update</h2>
    <p>Your benefits information is being reviewed.</p>
  </body>
</html>
```
5. Save and exit the editor
6. Start a simple HTTP server:
```yaml
python3 -m http.server 8080
```
7. Confirm the server is listening on port 8080

**This URL will be used in the phishing email:** `http://<KALI_IP>:8080`


   
***📌 Option A Status:*** **COMPLETE**

---

## 3. Execution – Option B: Endpoint-Only Phishing (No Mail Server)

This execution path validates phishing detection **without relying on email infrastructure**.  
It simulates a user interacting with a malicious link delivered through an out-of-band method
(e.g., chat, SMS, shared document), while still generating **authoritative endpoint telemetry**.

---

### 3.1 Prepare Symbolic Phishing Message

On the **Windows 11 endpoint**:

1. Log in as a standard user (e.g., `it.helpdesk1`)
2. Open **Notepad**
3. Create a new text file containing a phishing-style message, for example:
```text
HR Notice: Please review your updated benefits information.
https://<KALI_IP>:8080/hr-benefits
```
4. Save the file to the Desktop as: `phishing_message.txt`

📸 **Evidence:**  
`sim001-B-evidence-001-phishing-text.png`

---

### 3.2 User Clicks Phishing Link

1. Open `phishing_message.txt`
2. Highlight the phishing URL
3. Either:
- Click the URL directly, **or**
- Copy and paste the URL into **Google Chrome**
4. Press **Enter** to navigate to the link

This action represents a user responding to a phishing lure delivered outside of email.

📸 **Evidence:**  
`sim001-B-evidence-002-link-click.png`

---

### 3.3 Validate Endpoint Telemetry

This user action must generate **authoritative endpoint telemetry**.

On the **Splunk Search Head**:

1. Navigate to **Search & Reporting**
2. Run a search similar to:
```spl
index=wineventlog EventCode=4688
```
3. Confirm the following fields are present:
- `New_Process_Name = chrome.exe`
- `Process_Command_Line` contains the phishing URL
- Timestamp aligns with the user click

This confirms successful process execution tied to user interaction.  

📸 **Evidence:**  
`sim001-B-evidence-003-endpoint-4688.png`

---

### 3.4 (Optional) Validate Network Telemetry

If **Security Onion** or network monitoring is available:

1. Review HTTP or connection logs
2. Look for outbound traffic from the Windows endpoint to the Kali host
3. Validate:
- Source IP = Windows endpoint
- Destination IP = Kali host
- Destination port = `8080`
- HTTP method = `GET`

📸 **Optional Evidence:**  
`sim001-B-evidence-004-network-http.png`

> Network telemetry is **supplemental** and not required for SIM-001 validation.

---

### 3.5 Validate Detection in Splunk

1. In Splunk, search for detections related to:
- URL execution
- Suspicious browser activity
2. Confirm that:
- The phishing URL is visible
- The executing process is `chrome.exe`
- The event is indexed correctly

📸 **Evidence:**  
`sim001-B-evidence-005-splunk-url-detection.png`

---

### 3.6 Correlate Event in Splunk

1. Correlate the following data points:
- User account
- Endpoint hostname
- Process execution event
- URL accessed
2. Confirm the activity forms a coherent attack chain:
- User action → browser execution → URL access

📸 **Evidence:**  
`sim001-B-evidence-006-splunk-correlation.png`

---

### 3.7 Configure Splunk Alert

Create a new alert in Splunk with the following configuration:

- **Alert Name:** `LAB-SIM-001-PHISHING-ALERT`
- **Schedule:** Every 5 minutes
- **Time Range:** Last 15 minutes
- **Trigger Condition:** Number of Results > 0
- **Trigger Type:** Per Result
- **Throttle:** 10 minutes
- **Severity:** Medium

Verify that the alert fires when the phishing activity is detected.

📸 **Evidence:**  
`sim001-B-evidence-007-alert-config.png`  
`sim001-B-evidence-008-alert-fired.png`

---

### 3.8 Save Evidence & Mark Completion

1. Store all screenshots using the approved naming convention
2. Verify evidence completeness
3. Confirm alert functionality

📌 ***Option B Status:*** **COMPLETE**

---

## 4. Evidence Naming Convention

- **Option A:** `sim001-A-evidence-###-description.png`
- **Option B:** `sim001-B-evidence-###-description.png`

---

## 5. Final Status

**SIM-001 – Phishing Email Detection** is **COMPLETE and FULLY VALIDATED**.

- Endpoint telemetry is **authoritative**
- Network telemetry is **optional and supplemental**

