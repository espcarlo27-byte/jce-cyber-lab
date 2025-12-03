# SIM-001 – Phishing Email (T1566.002) – Steps

## 1. Prerequisites

Before running this simulation, ensure the following lab components are online and communicating:

- **Windows 11 Endpoint (10.0.0.50)**  
  - Sysmon installed & configured  
  - Splunk Universal Forwarder running  
  - Log forwarding to Splunk confirmed  

- **Security Onion (10.0.0.20)**  
  - Suricata & Zeek active  
  - Network visibility into Windows 11 subnet  
  - Logs flowing to Splunk OR stored within SO  

- **Splunk Enterprise (10.0.0.60 – Ubuntu)**  
  - Ingesting:  
    - Sysmon logs  
    - Windows Event Logs  
    - Suricata / Zeek network logs  
  - Time synchronization (NTP) enabled across all hosts  

- **pfSense (10.0.0.1)**  
  - Proper routing  
  - Traffic mirroring (SPAN / TAP) configured if needed  

- **Kali Linux (10.0.0.30)**  
  - Used as the phishing “attacker” host  
  - Able to serve HTTP content  

Before you continue, verify Splunk sees *any* new logs from:  
- index=winevent_sysmon  
- index=winevent_security  
- index=network_suricata  

---

## 2. Prepare the Phishing Landing Page (Kali – 10.0.0.30)

On your Kali VM, create a fake phishing webpage:


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

Start a lightweight web server:
   python3 -m http.server 8080

Confirm the server is running at:
   http://10.0.0.30:8080/

### Take note — this URL will be used in the simulated phishing email.

---

## 3. Simulate a Phishing Email on Windows 11

   ### Option A – If you have a mail client (Outlook or Thunderbird)

      Send an email to your test user:

      **Subject:**  
      HR Policy Update – Action Required  

      **Body:**

          Dear user,

          Please review the HR policy update:
          http://10.0.0.30:8080/

      Regards,
      HR Team

---

   ### Option B – If you don't have a mail server

      Use a simulated email:

      1. Open Notepad on Windows 11  
      2. Paste the following:

          From: hr@lab.local
          To: user@lab.local
          Subject: HR Policy Update – Action Required

          Dear user,

          Please review the new HR policy:
          http://10.0.0.30:8080/

          Thanks,
          HR Team

      3. Save it as:
         C:\Users\testuser\Desktop\sim001-email.txt

### The important part is the phishing URL that the user will click.

---

## 4. User Clicks the Link (Windows 11) — Generate Telemetry

On Windows 11:
- Open the “email” or text file  
- Click the link or copy/paste into Chrome/Edge  
- The webpage hosted by Kali should load  

### This generates:

### Sysmon Events
- Event ID 1 – Browser process start  
- Parent process: Outlook.exe / explorer.exe  
- Command line containing the suspicious URL  

### Suricata/Zeek Events
- HTTP connection to Kali  
- Optional Suricata alert depending on rule set  

Capture timestamps for easier hunting later.

---

## 5. Validate Network Telemetry (Security Onion)

On Security Onion:

Use SO dashboard or SSH to inspect Suricata logs.

Check Suricata HTTP logs:

sudo tail -f /nsm/sensor_data/*/suricata/eve.json | grep '"event_type":"http"'

You should see an entry similar to:
src_ip: 10.0.0.50
dest_ip: 10.0.0.30
dest_port: 8080
(You will paste symbolic versions into logs.md.)


## 6. Validate Sysmon Telemetry in Splunk

In Splunk, run:

index=winevent_sysmon EventCode=1 host=WIN11-LAB
(Image="*\\chrome.exe" OR Image="*\\msedge.exe")
| table _time, Computer, User, Image, ParentImage, CommandLine

Look for:
ParentImage = Outlook.exe OR explorer.exe
CommandLine contains the phishing URL

Take a screenshot for screenshots/.

## 7. Correlate Network + Endpoint Activity in Splunk

Use the correlation SPL in your `queries.md`:

- Join HTTP requests (Suricata)  
- With Sysmon browser process  
- Based on timestamps and IP  

This produces evidence of:

**“User clicked a phishing link → network request → browser process executed.”**

If results appear, capture a screenshot for the simulation folder.


## 8. Configure Splunk Alert (LAB-SIM-001-PHISHING-ALERT)

Create an alert using the final correlation SPL.

### Alert requirements:

- Triggers if: ≥ 1 correlated event  
- Runs every 5 minutes  
- Time window: last 15 minutes  
- Severity: Medium  

### Output fields must include:

- user  
- host  
- process  
- url  
- symbolic_id = LAB-SIM-001-PHISHING-ALERT  

Paste the alert configuration details into `alert-config.md`.


## 9. Save Evidence

In the `screenshots/` folder, add:

- sim001-email-view.png  
- sim001-suricata-hit.png  
- sim001-splunk-search.png  
- sim001-alert-fired.png  


## 10. Mark Simulation Completion

Update the SIM-001 checklist in the simulation’s README.md:

- Steps executed  
- Logs captured  
- Queries tested  
- Alert triggered  
- Screenshots saved  
- Validation matrix updated  

