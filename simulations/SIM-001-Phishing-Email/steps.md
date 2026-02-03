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

📸 **Evidence:**  
`sim001-A-evidence-001-kali-http-server.png`

---

### 2.2 Send Phishing Email (Zimbra Mail Server)

This step simulates a **realistic internal phishing scenario** where an email
appears to originate from **HR**.

The email is sent from an existing HR user to a target internal user.

On the **Zimbra Web Interface**:

1. Log in as an HR user  
   *(example: `hr.generalist1`)*
2. Create a new email
3. Populate the fields:
   ```text
   From: HR user mailbox  
   To: `it.helpdesk1`  
   Subject: `Action Required: Benefits Review`  
   Body:
     
     Please review your updated benefits information at the link below:

     http://<KALI_IP>:8080
   ```
4. Send the email  
5. Confirm the email is successfully delivered  

📸 **Evidence:**  
`sim001-A-evidence-002-email-sent.png`

---

### 2.3 User Receives Phishing Email (Windows 11)

This step confirms successful delivery of the phishing message.

On the **Windows 11 endpoint**:

- Log in as `it.helpdesk1`  
- Open the mailbox  
- Confirm the phishing email is present  
- Open the email **without clicking the link**

📸 **Evidence:**  
`sim001-A-evidence-003-email-received.png`

---

### 2.4 User Clicks Phishing Link (Windows 11)

This step generates the **authoritative endpoint telemetry** used for detection.

On the **Windows 11 endpoint**:

- Open the phishing email  
- Click the embedded URL  

This action launches the browser and initiates endpoint execution.

📸 **Evidence:**  
`sim001-A-evidence-004-email-link-click.png`

---

### 2.5 Validate Endpoint Telemetry (Windows)

This step confirms that the phishing link interaction generated **authoritative endpoint telemetry**.

The user’s browser execution must produce a **Windows Security Event ID 4688** (Process Creation) containing the phishing URL in the command line.

---

#### Step 2.5.1 — Capture Time of Click

Immediately after clicking the phishing link, note the approximate time (within 1–2 minutes).  
This helps narrow searches and confirms event alignment.

---

#### Step 2.5.2 — Search for Process Creation Event

On the **Splunk Search Head**, navigate to **Search & Reporting** and run:

```spl
(index=winevent_security OR index=winevent_system)
EventCode=4688
| table _time, host, user, New_Process_Name, Process_Command_Line, Parent_Process_Name
| sort - _time
```

---

#### Step 2.5.3 — Confirm Required Indicators

Locate the event corresponding to the click and verify:

| Field | Expected Value |
|------|----------------|
| **New_Process_Name** | `chrome.exe` |
| **Process_Command_Line** | Contains phishing URL (`http://<KALI_IP>:8080`) |
| **host** | Windows 11 endpoint hostname |
| **user** | `it.helpdesk1` |
| **Parent_Process_Name** | Typically `explorer.exe` or mail client process |

This confirms that user interaction directly resulted in browser execution.

---

#### Step 2.5.4 — Validate URL Visibility

Ensure the phishing URL is clearly visible in the `Process_Command_Line` field.  
This proves:

> **User action → process execution → URL artifact captured**

This is the core detection signal for **MITRE ATT&CK T1566.002**.

📸 **Evidence:**  
`sim001-A-evidence-005-endpoint-4688.png`

> Endpoint telemetry is the authoritative detection source for SIM-001.  
> Network logs may supplement context but are not required for validation.

---

### 2.6 (Optional) Validate Network Telemetry (Security Onion)

This step is optional and not required for SIM-001 completion.

If Security Onion is available, observe outbound HTTP traffic.

On **Security Onion**:

```bash
sudo tail -f /nsm/sensor_data/*/suricata/eve.json | grep '"event_type":"http"'
```

Expected fields (if available):

- **src_ip** – Windows 11 endpoint  
- **dest_ip** – Kali host  
- **dest_port** – 8080  
- **http.method** – GET  

📸 **Optional Evidence:**  
`sim001-A-evidence-006-network-http.png`

> Network telemetry is supplemental and used only to support endpoint validation.  
> Detection logic does **not** depend on network data.

---

### 2.7 Validate Detection in Splunk

This step confirms that the phishing activity is detectable using SIEM search logic
based on **endpoint process telemetry**.

---

#### Step 2.7.1 — Open Splunk Search & Reporting

- Navigate to the Splunk web interface
- Open the **Search & Reporting** app

---

#### Step 2.7.2 — Run Detection Query

Execute the validated search:

```spl
(index=winevent_security OR index=winevent_system)
EventCode=4688
New_Process_Name="*\\chrome.exe"
| table _time, host, user, New_Process_Name, Process_Command_Line
| sort - _time
```

---

 #### Step 2.7.3 — Confirm Detection Conditions

Locate the event generated by the phishing click and confirm:

| Detection Signal | Expected Observation |
|------------------|----------------------|
| **Process** | `chrome.exe` execution |
| **User** | `it.helpdesk1` |
| **Host** | Windows 11 endpoint |
| **Command Line** | Contains phishing URL (`http://<KALI_IP>:8080`) |
| **Timestamp** | Matches user click timeframe |

> This confirms that the detection logic successfully identifies browser-based URL execution.

---

#### Step 2.7.4 — Validate Detection Reproducibility

Repeat the phishing click if necessary and confirm that:

- The query consistently returns results
- Detection is not dependent on manual filtering

This ensures the detection is **reliable**, not incidental.

📸 **Evidence:**  
`sim001-A-evidence-007-splunk-url-detection.png`

---

### 2.8 Correlate Event in Splunk

This step ties the phishing execution to the lab simulation using correlation logic.

#### Step 2.8.1 — Run Correlation Query
```spl
(index=winevent_security OR index=winevent_system)
EventCode=4688
New_Process_Name="*\\chrome.exe"
Process_Command_Line="*http*"
| eval simulation_id="SIM-001"
| eval symbolic_id="LAB-SIM-001-PHISHING-ALERT"
| table _time, host, user, New_Process_Name, Process_Command_Line, simulation_id, symbolic_id
| sort - _time
```

---

#### Step 2.8.2 — Validate Correlation Fields

Confirm the following appear in the results:

| Field | Purpose |
|------|---------|
| **simulation_id** | Ties event to SIM-001 |
| **symbolic_id** | Represents alert identifier |
| **host** | Affected endpoint |
| **user** | User who executed the link |
| **Process_Command_Line** | Shows phishing URL |

---

#### Step 2.8.3 — Confirm Attack Chain Integrity

Ensure the data supports a logical detection chain:

> Email delivery → user interaction → browser execution → URL artifact captured

This demonstrates **behavior-based detection**, not just a static indicator match.

---

#### Step 2.8.4 — Validate Alert Readiness

Confirm this correlation query can be used as the basis for a Splunk alert  
without modification.

📸 **Evidence:**  
`sim001-A-evidence-008-splunk-correlation.png`

---

### 2.9 Configure Splunk Alert

Configure the alert using the correlation query.

- **Alert Name:** `LAB-SIM-001-PHISHING-ALERT`  
- **Schedule:** Every 5 minutes  
- **Time Window:** Last 15 minutes  
- **Trigger:** Number of Results > 0  
- **Trigger Type:** Per Result  
- **Throttle:** 10 minutes  
- **Severity:** Medium  

📸 **Evidence:**  
`sim001-A-evidence-009-alert-config.png`  
`sim001-A-evidence-010-alert-fired.png`

---

### 2.10 Save Evidence & Mark Completion

Store all evidence in:
```text
simulations/SIM-001-Phishing-Email/screenshots/
```
   
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

