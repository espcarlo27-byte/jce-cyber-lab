# Simulation 1 – Phishing Email (T1566.002)
## ⚠️ Issues & Resolutions

This document captures real operational issues encountered during **SIM-001**
and the structured methodology used to identify, resolve, and validate each one.

---

## 🧠 Lab Network Context (Important)

This simulation uses the following intentional network design:

- **pfSense** acts as the primary **DHCP server** and **DNS resolver**
- Endpoints and simulation hosts receive **dynamic IP addresses via DHCP**
- Detections rely on **user context, hostname, and process execution**
  rather than static IP assignments

This context is critical for interpreting network-related issues and resolutions
documented below.

---

### ***🧩 Issue 1: Logs Not Appearing in Splunk***

**Description:**  
During the initial execution of the phishing email simulation, expected Windows Security and endpoint logs were not appearing in Splunk searches, despite the attack process being executed on the Windows 11 endpoint.

**Impact:**  
This prevented validation of the detection logic and blocked confirmation that event ingestion from the endpoint to the SIEM was functioning correctly.

**Root Cause:**  
The Splunk Universal Forwarder on the Windows 11 endpoint was either:
- Not fully configured to monitor the required Event Log channels, or  
- Not successfully forwarding data to the Splunk Enterprise server on Ubuntu.

**Resolution:**  
1. Verified that the Splunk Universal Forwarder service was running on the Windows 11 endpoint.  
2. Confirmed that the forwarder was correctly pointed to the Splunk Enterprise server (`10.0.0.60`).  
3. Validated that the appropriate Windows Event Log inputs were enabled.  
4. Restarted the Splunk Forwarder service after configuration changes.  
5. Re-ran the phishing simulation to regenerate log activity.  
6. Confirmed successful ingestion using Splunk search queries.

**Validation:**  
After these corrections, Windows Security logs and related process execution events appeared correctly in Splunk, allowing the phishing detection to be fully validated.

**Lessons Learned:**  
> Log ingestion must always be validated before testing detection logic.  
> Endpoint activity alone is not sufficient—SOC visibility depends entirely on reliable telemetry flow into the SIEM.

---

### ***🧩 Issue 2: Forwarder Authentication Failure When Connecting to Splunk***

**Description:**  
While configuring the Splunk Universal Forwarder on the Windows 11 endpoint, the forwarder repeatedly failed to authenticate when attempting to connect to the Splunk Enterprise server. Even when using the same credentials that successfully logged into the Splunk Web UI, the CLI returned a “login failed” error.

**Impact:**  
The forwarder could not register with the Splunk deployment server or management APIs.  
This caused:
- Failure to automatically deploy inputs or configs  
- Missing ingestion status  
- Inability to manage the endpoint from Splunk Web  
- Delays in validating the phishing simulation detection  

**Root Cause:**  
Splunk forwarder authentication uses **Splunk’s internal user store**, not OS credentials.  
Common causes include:  
- Using the Splunk Web login instead of CLI-required credentials  
- Incorrect admin password stored during initial install  
- Deployment server not configured for forwarder registration  
- Missing or incorrect authentication token when using "renew" commands  

In this case, the issue resulted from a mismatch between the Splunk Web credentials and the forwarder’s expected authentication method.

**Resolution:**  
1. Confirmed that the forwarder was pointing to the correct Splunk Enterprise server (`10.0.0.60`).  
2. Verified the correct admin username and password inside Splunk’s internal account store.  
3. Reconfigured authentication using the forwarder CLI:  
   ```powershell
   splunk.exe set deploy-poll 10.0.0.60:8089
   splunk.exe login
   ```
4. Reset the Splunk admin password to ensure known-good credentials.
5. Restarted the Splunk Universal Forwarder service.
6. Validated successful connection using:
   ```powershell
   splunk.exe list forward-server
   ```

**Validation:**  
After correcting the authentication configuration, the forwarder successfully registered with the Splunk server, and endpoint logs began flowing consistently.

**Lessons Learned:**  
> Splunk authentication is platform-specific and separate from OS credentials.  
> Forwarder management issues can masquerade as detection failures if authentication is not validated early.

---

### ***🧩 Issue 3: Phishing Simulation Did Not Execute Expected Payload or Generate Artifacts***

**Description:**  
During the phishing simulation, the payload embedded in the test email did not execute as expected. Although the phishing message was delivered or simulated, no child process, script execution, or suspicious activity was generated on the Windows 11 endpoint. As a result, the simulation produced incomplete or missing telemetry.

**Impact:**  
- No malicious child process was recorded  
- No 4688 or Sysmon ProcessCreate events were generated  
- Splunk detections did not trigger because no activity occurred  
- The validation of phishing behavior was incomplete  
- Time was spent troubleshooting ingestion instead of verifying the attack fired  

**Root Cause:**  
The phishing payload or attachment did not execute due to one or more of the following common causes:
- The email was opened, but the attachment or script was never launched  
- Windows SmartScreen or endpoint protections silently blocked execution  
- The test payload required user interaction that did not occur  
- The simulation steps were not executed in the intended order  
- The phishing “artifact” was symbolic and required manual execution to generate telemetry  

**Resolution:**  
1. Confirmed whether the payload was actually run on the Windows 11 endpoint.  
2. Disabled or bypassed SmartScreen prompts (only within the lab environment) to allow controlled execution.  
3. Re-ran the phishing attachment manually to ensure it executed:  
   ```powershell
   Start-Process <payload-path>
   ```
4. Verified expected logs appeared locally in Event Viewer.
5. Confirmed Splunk Universal Forwarder was capturing new events after rerunning the payload.
6. Validated the simulation again with a successful execution path.

**Validation:**   
Once the payload was properly executed, Windows Security and Sysmon logs generated the expected process creation events. Splunk detections then triggered successfully, confirming the phishing scenario was executed end-to-end.

**Lessons Learned:**  
> A detection cannot trigger if the attack never truly executes.  
> Simulations must explicitly generate telemetry—symbolic artifacts require deliberate execution to be useful.

---

### ***🧩 Issue 4: Windows Event ID 4688 (Process Creation) Not Logging***

**Description:**  
During the phishing email simulation, the expected Windows Event ID **4688 (Process Creation)** did not appear in Splunk or in the local Windows Event Viewer. This event is required to capture the executed payload or script associated with the phishing activity.

**Impact:**  
Without Event ID 4688:
- Process lineage for the attack could not be validated  
- SPL detections relying on command-line visibility did not trigger  
- No evidence of the malicious child process was visible  
- Endpoint telemetry for the simulation was incomplete  

**Root Cause:**  
Process creation auditing was not enabled on the Windows 11 endpoint.  
On a default installation, Event ID 4688 may be missing due to:
- **Advanced Audit Policy** for process creation not being enabled  
- Group Policy settings not yet applied from the Windows Server domain  
- Sysmon not installed or not configured to capture process events  

**Resolution:**  
1. Enabled **Audit Process Creation** via Group Policy:  
   `Computer Configuration → Windows Settings → Security Settings → Advanced Audit Policy → Detailed Tracking → Audit Process Creation = Success`  
2. Forced GPO update on the endpoint with:  
   ```powershell
   gpupdate /force
   ```
3. Verified 4688 visibility in:   
   `Event Viewer → Windows Logs → Security`
4. Restarted the Splunk Universal Forwarder to confirm ingestion:
   ```powershell
   Restart-Service splunkforwarder
   ```
5. Re-ran the phishing simulation to regenerate process events.

**Validation:**  
Event ID 4688 began appearing consistently in both Event Viewer and Splunk searches, enabling the phishing simulation's detections to trigger successfully.

**Lessons Learned:**  
> Detection engineering depends on proper audit policy configuration.  
> Even common security events like 4688 are not guaranteed without explicit enablement.

---

### ***🧩 Issue 5: Expected Network Evidence (EVE.json / Packet Logs) Not Found During Validation***

**Description:**  
While validating the phishing email simulation, expected network artifacts such as
**Suricata EVE.json events** or packet logs were not initially located on the
Security Onion sensor.

**Impact:**  
- Network-side validation of the phishing simulation was delayed  
- Endpoint telemetry temporarily served as the primary validation source  
- Reduced confidence in network-layer visibility  

**Root Cause:**  
This issue was influenced by both **Security Onion Eval mode behavior** and
the lab’s **centralized DNS and routing design**:

- pfSense handled **DNS resolution and traffic routing**, meaning:
  - DNS visibility was centralized at the firewall
  - Not all phishing-related traffic produced Suricata HTTP events
- Security Onion Eval mode stores logs in structured, non-obvious paths
- Initial searches targeted incorrect directories or rotated files

**Resolution:**  
1. Verified correct Suricata and Zeek log locations for Eval mode:
   ```bash
   /opt/so/log/suricata/eve.json
   /nsm/suricata/<sensor-name>/eve.json
   ```
2. Confirmed Suricata service status and packet capture activity.
3. Re-ran the phishing simulation to generate fresh HTTP traffic.
4. Validated that network telemetry aligned with the expected simulation scope
(user-driven link click, not full payload delivery).

**Validation:**  
Correct log paths were confirmed, and network visibility was validated where
expected based on the simulation’s design.  

**Lessons Learned:**  
> Network telemetry availability depends on traffic type, sensor placement, and platform behavior, not just whether an action occurred.

---

### ***🧩 Issue 6: Splunk Search Returned No Results Due to Incorrect Time Range or Index Selection***

**Description:**  
After executing the phishing simulation and confirming that logs were being generated on the Windows endpoint, Splunk searches continued to return **“No results found.”** This occurred even though telemetry was confirmed to be present locally on the host and the Splunk Universal Forwarder was running.

**Impact:**    
- Delayed validation of detection logic  
- Misleading appearance that logs were still not ingesting  
- Duplicate troubleshooting on components that were functioning correctly  
- Difficulty confirming whether Event ID 4688 and other telemetry were present  

**Root Cause:**  
The issue was caused by incorrect Splunk search filters, specifically:
- Selecting the wrong **time range** during searches (e.g., “Last 24 hours” when events were only seconds old)  
- Not specifying the correct **index**, such as:  
  - `index=wineventlog`  
  - `index=sysmon`  
  - `index=main`  
- Running searches before Splunk completed indexing the event  
- Using filters that were too restrictive, unintentionally excluding the correct event source  

**Resolution:**  
1. Adjusted Splunk search to a broad time range:  
   ```spl
   index=* earliest=-15m latest=now
   ```
2. Confirmed correct Windows log index using:
   ```spl
   | metadata type=sourcetypes
   ```
3. Re-ran searches with explicit source paths, such as:
   ```spl
   index=wineventlog EventCode=4688
   ```
4. Verified the event timestamps aligned with when the phishing simulation occurred.
5. Allowed Splunk additional time to finish indexing newly forwarded events.

**Validation:**   
Once the correct time range and index were selected, Splunk displayed the expected 4688 events and related telemetry. This confirmed that the ingestion pipeline was functioning correctly and that prior “no results” responses were not ingestion failures but search configuration issues.

**Lessons Learned:**  
> “No results” does not always mean “no data.”  
> Time range, index selection, and search scope are critical skills in SOC investigations.

---

### ***🧩 Issue 7: Endpoint Network Connectivity or DNS Resolution Prevented Expected Callback Activity***

**Description:**  
During the phishing simulation, endpoint execution was confirmed; however, no
expected external callback or follow-on network activity was observed from the
Windows 11 endpoint.

**Impact:**  
- No outbound network indicators were visible in network telemetry  
- Initial concern about missing network visibility  
- Required clarification of simulation intent and expected outcomes  

**Root Cause:**  
This behavior was expected based on the lab’s **network and DNS architecture**:

- **pfSense** acted as the primary **DNS resolver**, centralizing name resolution  
- The phishing simulation used a **controlled internal HTTP destination**  
- The scenario was designed to validate **user interaction detection**, not
  external command-and-control behavior  
- Endpoints used **DHCP-assigned IP addresses**, reinforcing
  hostname- and user-based detection rather than IP-based assumptions  

**Resolution:**    
1. Verified endpoint network connectivity and DNS resolution through pfSense  
2. Confirmed firewall and NAT rules permitted outbound traffic  
3. Revalidated the simulation’s scope and success criteria  
4. Adjusted validation expectations to align with the intended detection objective  

**Validation:**  
Endpoint and network telemetry were correctly interpreted within the context of
the lab’s architecture and simulation goals.

**Lessons Learned:**  
> Not every phishing simulation produces full kill-chain network activity.  
> Detection objectives must align with **scenario scope and architectural design**.

---

## 🧠 Overall Takeaways

SIM-001 reinforced foundational SOC and detection engineering principles:

- Log ingestion and audit policy configuration are prerequisites for detection  
- Endpoint telemetry is often the **primary signal** for phishing activity  
- Network visibility depends on **sensor placement and DNS architecture**  
- Dynamic IP addressing requires **context-based correlation**  
- Troubleshooting methodology is as critical as detection logic itself  

These lessons align directly with the lab’s intentional design and real-world
SOC operating conditions.

---

## 🏁 Status

- Issues fully documented  
- Root causes identified  
- Resolutions validated  
- Detection logic confirmed  
- Simulation executed end-to-end  

> **SIM-001 is marked as ✅ Validated**  
> and is suitable for portfolio and interview discussion.
