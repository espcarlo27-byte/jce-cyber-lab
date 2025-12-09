# SIM-001 – Phishing Email (T1566.002) – Steps

## 1. Prerequisites

Before running this simulation, ensure the following lab components are online and communicating:

- **Windows 11 Endpoint (10.0.0.50)**
  - Windows Security Auditing enabled
  - Process Creation logging (EventCode 4688)
  - Command-line auditing enabled
  - Splunk Universal Forwarder running
  - Log forwarding to Splunk confirmed

- **Security Onion (10.0.0.20)**
  - Suricata & Zeek active
  - Network visibility into Windows 11 subnet
  - Logs available in SO (optional for this sim)

- **Splunk Enterprise (10.0.0.60 – Ubuntu)**
  - Ingesting:
    - Windows Security Logs (`winevent_security`)
    - Windows System Logs (`winevent_system`)
  - Time synchronization (NTP) enabled across all hosts

- **pfSense (10.0.0.1)**
  - Proper routing
  - Internet access between Kali ↔ Windows 11

- **Kali Linux (10.0.0.30)**
  - Used as the phishing “attacker” host
  - Able to host HTTP content

Before you continue, verify Splunk sees recent Windows logs:

```spl
(index=winevent_security OR index=winevent_system)
| head 10
```

---

## 2. Prepare the Phishing Landing Page (Kali – 10.0.0.30)
A. Create a Fake Phishing Web Page

On Kali Terminal :
```bash
mkdir -p ~/sim001-phish
cd ~/sim001-phish

cat > index.html << 'EOF'
<html>
  <body>
    <h2>HR Policy Update – Action Required</h2>
    <p>Please review the attached HR policy update.</p>
    <p>This is a benign phishing simulation for LAB-SIM-001.</p>
  </body>
</html>
EOF
```

B. Start a Lightweight Web Server
```bash
python3 -m http.server 8080
```

C. Confirm Server Is Running
Open in a browser:
```cpp
http://10.0.0.30:8080/
```
***Take note — this URL will be used in the simulated phishing email.***

---

## 3. Simulate a Phishing Email on Windows 11
**Option A – With a Mail Client**
Send an email to your test user:
```text
Subject: HR Policy Update – Action Required

Body:
Dear user,

Please review the HR policy update:
http://10.0.0.30:8080/

Regards,
HR Team
```

**Option B – Without a Mail Server (Text-Based Simulation)**

1. Open Notepad on Windows 11
2. Paste the following:
```text
From: hr@lab.local
To: user@lab.local
Subject: HR Policy Update – Action Required

Dear user,

Please review the new HR policy:
http://10.0.0.30:8080/

Thanks,
HR Team
```
3. Save it as: 
```makefile
C:\Users\testuser\Desktop\sim001-email.txt
```

***The important element is the phishing URL that the user will click.***

---

## 4. User Clicks the Link (Windows 11) — Generate Telemetry

On Windows 11:
- Open the email or text file
- Click the link or copy/paste into Google Chrome
- The webpage hosted on Kali should load

This generates:
**Windows Endpoint Telemetry (Security Event 4688)**
- Browser process creation
- New_Process_Name = chrome.exe
- Command line contains http://10.0.0.30:8080

**Network Telemetry**
- HTTP connection from:
    - 10.0.0.50 → 10.0.0.30

***Capture approximate timestamps for easier hunting later.***

---

## 5. (Optional) Validate Network Telemetry (Security Onion)

On Security Onion:
```bash
sudo tail -f /nsm/sensor_data/*/suricata/eve.json | grep '"event_type":"http"'
```

Expected fields:
- src_ip: 10.0.0.50
- dest_ip: 10.0.0.30
- dest_port: 8080

***Symbolic versions are stored in logs.md.***

---

## 6. Validate Endpoint Telemetry in Splunk (FINAL WORKING QUERY)

In Splunk, run:
~~~spl
(index=winevent_security OR index=winevent_system)
EventCode=4688
New_Process_Name="*\\chrome.exe"
| table _time, host, user, New_Process_Name, Process_Command_Line
| sort - _time
~~~

Look for:
- New_Process_Name = chrome.exe
- Process_Command_Line contains:
```cpp
http://10.0.0.30:8080
```

Take screenshot:
- sim001-splunk-url-detection.png

---

## 7. Correlate Phishing Click in Splunk (FINAL CORRELATION)
~~~spl
(index=winevent_security OR index=winevent_system)
EventCode=4688
New_Process_Name="*\\chrome.exe"
Process_Command_Line="*http*"
| eval simulation_id="SIM-001"
| eval symbolic_id="LAB-SIM-001-PHISHING-ALERT"
| table _time, host, user, New_Process_Name, Process_Command_Line, simulation_id, symbolic_id
| sort - _time
~~~

***Take screenshot:***  sim001-splunk-correlation.png

This confirms:
***“User clicked phishing link → browser executed → URL captured → detection validated.”***

---

## 8. Configure Splunk Alert (LAB-SIM-001-PHISHING-ALERT)

Use the correlation query above to configure the alert:
- Schedule: Every 5 minutes
- Time Window: Last 15 minutes
- Trigger When: Number of Results > 0
- Trigger Type: Per Result
- Throttle: 10 minutes
- Severity: Medium

***Paste the final configuration into:***  alert-config.md

---

## 9. Save Evidence

In the screenshots/ folder, add:
- sim001-email-view.png
- sim001-splunk-url-detection.png
- sim001-splunk-correlation.png
- sim001-alert-config.png
- sim001-alert-fired.png

---

## 10. Mark Simulation Completion

Update the checklist in README.md:
- [x] Steps executed
- [x] Logs captured
- [x] Queries tested
- [x] Alert triggered
- [x] Screenshots saved
- [x] Validation matrix updated

---

## ✅ FINAL STATUS

**SIM-001 – Phishing Email Detection is COMPLETE and FULLY VALIDATED**
