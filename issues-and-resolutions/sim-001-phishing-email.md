# Simulation 1 – Phishing Email  
## Issues & Resolutions

---

### ***Issue 1: Logs Not Appearing in Splunk***

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

---

### ***Issue 2: Forwarder Authentication Failure When Connecting to Splunk***

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

---

### ***Issue 3: Phishing Simulation Did Not Execute Expected Payload or Generate Artifacts***

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

---

### ***Issue 4: Windows Event ID 4688 (Process Creation) Not Logging***

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

---

### ***Issue 5: Expected Network Evidence (EVE.json / Packet Logs) Not Found During Validation***

**Description:**  
While validating the phishing email simulation, expected network artifacts such as **Suricata EVE.json events** or packet captures were not found in the expected directories on the Security Onion sensor. Commands such as:
```bash
find / -name eve.json 2>/dev/null
```
returned no results, leading to uncertainty about whether Suricata had logged any related network activity.

**Impact:**   
- Network-side validation of the phishing simulation was delayed
- No correlated Suricata or Zeek events were available for cross-analysis
- Detection validation relied solely on endpoint logs until the issue was resolved
- Reduced visibility into the initial command-and-control or payload retrieval behavior

**Root Cause:**  
Security Onion’s Eval mode places restrictions and structured paths on where logs are stored. Common contributing factors include:
- Incorrect search paths for Suricata/Zeek logs
- Suricata not generating EVE events for this simulation type
- Using the wrong directory level (/nsm/ vs /opt/so/log/)
- Logs being rotated or stored under a timestamped subfolder
- Running commands without proper privileges

**Resolution:**
1. Verified the correct log locations for Security Onion (Eval Mode):
   ```bash
   /opt/so/log/suricata/eve.json
   /nsm/suricata/<sensor-name>/eve.json
   ```
2. Confirmed Suricata was actively running and capturing traffic:
   ```bash
   systemctl status suricata
   ```
3. Validated that the phishing simulation produced traffic types that Suricata is expected to log.
4. Adjusted search queries to account for rotated or compressed logs when needed:
   ```bash
   ls -lah /opt/so/log/suricata/
   ```
5. Re-ran the simulation to generate fresh network activity.

**Validation:**   
Correct log paths were confirmed, and Suricata events were successfully located, allowing network-side validation of the phishing simulation to proceed.

---

### ***Issue 6: Splunk Search Returned No Results Due to Incorrect Time Range or Index Selection***

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

---

### ***Issue 7: Endpoint Network Connectivity or DNS Resolution Prevented Expected Callback Activity***

**Description:**  
During the phishing email simulation, the payload executed successfully and endpoint logs were generated; however, no expected outbound network activity or callback behavior was observed from the Windows 11 endpoint. This made it unclear whether the simulated phishing payload attempted any external communication.

**Impact:**  
- No outbound network indicators were observed  
- Suricata and Zeek did not record related traffic  
- Network-based validation of the phishing scenario was incomplete  
- Detection relied only on endpoint telemetry rather than full kill-chain visibility  

**Root Cause:**  
The issue was caused by network or name-resolution constraints within the lab environment, including one or more of the following:
- DNS resolution not functioning correctly on the Windows 11 endpoint  
- pfSense firewall rules blocking outbound traffic  
- NAT configuration preventing egress from the internal network  
- The phishing payload using a hostname that could not be resolved  
- The simulation being designed to generate endpoint-only telemetry without real external callbacks  

**Resolution:**  
1. Verified network connectivity from the Windows 11 endpoint using:
   ```powershell
   ping 8.8.8.8
   nslookup google.com
   ```
2. Confirmed correct default gateway and DNS server configuration.
3. Reviewed pfSense firewall and NAT rules to ensure outbound traffic was permitted.
4. Validated whether the phishing simulation was intended to generate real network callbacks or endpoint-only artifacts.
5. Adjusted expectations and validation criteria accordingly based on the simulation design.

**Validation:**   
After confirming network connectivity and understanding the simulation’s intended behavior, endpoint and network visibility were correctly interpreted. This clarified that the absence of network callbacks was due to environment constraints or simulation scope rather than a logging failure.
