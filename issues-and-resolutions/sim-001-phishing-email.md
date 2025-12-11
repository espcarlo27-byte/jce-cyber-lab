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

### ***Issue 3: Windows Event ID 4688 (Process Creation) Not Logging***

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
