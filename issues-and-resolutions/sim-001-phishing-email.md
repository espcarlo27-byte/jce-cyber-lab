# Simulation 1 – Phishing Email (T1566.002)

## ⚠️ Issues & Resolutions

This document captures real operational issues encountered during **SIM-001**
and the structured methodology used to identify, resolve, and validate each one.

SIM-001 is designed as an **endpoint-driven detection simulation**.
Windows Security Event ID **4688** is treated as the **authoritative source**.
Network telemetry (Security Onion / Suricata / Zeek), when available, is treated
as **optional supplemental validation**.

---

## 🧾 Evidence & Naming Convention Notes

This simulation follows the standardized evidence naming convention:

- Evidence IDs: `E-SIM001-###`
- Screenshot files: `sim001-evidence-###-<short-description>.png`

### 🧾 Key Evidence Referenced Throughout This I&R

- **E-SIM001-001** – Baseline Event ID 4688 visibility + log ingestion proof  
- **E-SIM001-002** – Phishing click execution evidence (`chrome.exe` with URL in command line)  
- **E-SIM001-003** – User workflow context (mail interaction timeframe aligned with execution)  
- **E-SIM001-004** – Parent process context supporting user-driven execution  
- **E-SIM001-005** – User-focused timeline view of activity  
- **E-SIM001-006** – Optional PowerShell follow-on activity check  
- **E-SIM001-007** – Optional network HTTP confirmation (Security Onion)  
- **E-SIM001-008** – Correlation query result showing tagged detection event  
- **E-SIM001-009** – Alert configuration screen evidence  
- **E-SIM001-010** – Alert fired confirmation screenshot

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
2. Confirmed that the forwarder was correctly pointed to the Splunk Enterprise server.  
3. Validated that the appropriate Windows Event Log inputs were enabled.  
4. Restarted the Splunk Forwarder service after configuration changes.  
5. Re-ran the phishing simulation to regenerate log activity.  
6. Confirmed successful ingestion using Splunk search queries.

**Validation:**  
After these corrections, Windows Security logs and related process execution events appeared correctly in Splunk, allowing the phishing detection to be fully validated.  

**Evidence Reference:**  
- `E-SIM001-001` (Ingestion proof: 4688 events visible in Splunk)
  
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

**Evidence Reference:**
- `E-SIM001-001` (Forwarder health + logs flowing into Splunk)

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

**Evidence Reference:**
- `E-SIM001-002` (chrome.exe execution with URL argument / 4688 proof)

**Lessons Learned:**  
> A detection cannot trigger if the attack never truly executes.  
> Validation requires deliberate execution of behavior that produces log evidence.

---

### ***🧩 Issue 4: Windows Event ID 4688 (Process Creation) Not Logging***

**Description:**  
During the phishing email simulation, the expected Windows Event ID **4688 (Process Creation)** did not appear in Splunk or in the local Windows Event Viewer. This event is required to capture the executed payload or script associated with the phishing activity.

**Impact:**  
Without Event ID 4688:
- Process lineage for the attack could not be validated  
- SPL detections relying on command-line visibility did not trigger  
- Endpoint telemetry for the simulation was incomplete  

**Root Cause:**  
Process creation auditing was not enabled on the Windows 11 endpoint.  
On a default installation, Event ID 4688 may be missing due to:
- **Advanced Audit Policy** for process creation not being enabled  
- Group Policy / GPO settings not applied from the Windows Server domain (if domain-joined)  

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
Event ID 4688 began appearing consistently in both Event Viewer and Splunk searches, enabling SIM-001 detections to trigger successfully.  

**Evidence Reference:**
- `E-SIM001-001` (4688 enabled + visible)
- `E-SIM001-002` (phishing click evidence tied to 4688)

**Lessons Learned:**  
> Detection engineering depends on proper audit policy configuration.  
> Even common security events like 4688 are not guaranteed without explicit enablement.

---

### ***🧩 Issue 5: Optional Network Evidence Was Unavailable or Not Required for Validation***

**Description:**  
While validating SIM-001, expected optional network artifacts such as
Suricata EVE.json events were not initially located or were not available
due to lab resource constraints.

**Impact:**  
- Network-side validation of the phishing simulation was delayed or skipped
- Endpoint telemetry served as the primary validation source  
- Required correct interpretation of detection scope  

**Root Cause:**  
SIM-001 is designed to be endpoint-driven, and network evidence is
supplemental only. In addition, network evidence may be unavailable due to:

- Security Onion not running (VM resource limitations)
- Sensor placement limitations
- TLS encryption reducing visibility
- Suricata/Zeek logging behavior and storage paths (Eval mode)  

**Resolution:**  
1. Confirmed SIM-001 success criteria are met using endpoint telemetry:
   - Windows Security Event ID 4688
   - URL present in Process_Command_Line

2. If Security Onion was available, validated correct Suricata log locations:
   ```bash
   /opt/so/log/suricata/eve.json
   /nsm/suricata/<sensor-name>/eve.json
   ```
3. Re-ran simulation for fresh HTTP activity if network confirmation was desired.

**Validation:**  
SIM-001 was considered fully validated once endpoint telemetry and Splunk alerting
were confirmed. Optional network evidence was treated as supporting context only.  

**Evidence Reference (Optional):**
- `E-SIM001-005` (network confirmation, if captured)

**Lessons Learned:**  
> Network telemetry is valuable, but not always available.
> Detections must remain valid even when network visibility is partial.

---

### ***🧩 Issue 6: Splunk Search Returned No Results Due to Incorrect Time Range or Index Selection***

**Description:**  
After executing the phishing simulation and confirming that logs were being generated on the Windows endpoint, Splunk searches continued to return **“No results found.”** This occurred even though telemetry was confirmed to be present locally on the host and the Splunk Universal Forwarder was running.

**Impact:**    
- Delayed validation of detection logic  
- Misleading appearance that logs were still not ingesting  
- Duplicate troubleshooting on components that were functioning correctly  

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
2. Confirmed available sourcetypes:
   ```spl
   | metadata type=sourcetypes
   ```
3. Re-ran searches with correct index and EventCode.
   ```spl
   index=wineventlog EventCode=4688
   ```

**Validation:**   
After adjusting time range and index, expected 4688 events appeared and the alert logic validated successfully.  

**Evidence Reference:**
- `E-SIM001-003` (query results validating data present + searchable)

**Lessons Learned:**  
> “No results” does not always mean “no data.”  
> Time range, index selection, and search scope are critical skills in SOC investigations.

---

### ***🧩 Issue 7: No External Callback Activity Observed (Expected in Endpoint-Driven Scope)***

**Description:**  
During the phishing simulation, endpoint execution was confirmed; however, no
expected external callback or follow-on network activity was observed from the
Windows 11 endpoint.

**Impact:**  
- No outbound network indicators were visible in network telemetry  
- Required clarification of simulation scope and expectations
  
**Root Cause:**  
This behavior was expected based on the lab’s **architecture and SIM-001 scope:**
- **pfSense** acted as the primary **DNS resolver**, centralizing name resolution  
- The simulation uses a controlled URL click and browser execution
- SIM-001 validates user interaction detection, not C2/payload behavior
- Endpoints use DHCP, reinforcing hostname/user/process based detection

**Resolution:**    
1. Verified endpoint network connectivity and DNS resolution through pfSense  
2. Confirmed firewall and NAT rules permitted (if needed)
3. Revalidated SIMM-001’s scope and success criteria  
4. Confirmed endpoint telemetry (4688 + URL command line) was sufficient for validation  

**Validation:**  
Endpoint and alert telemetry were correctly interpreted within the simulation’s
intent and architecture.  

**Evidence Reference:**
- `E-SIM001-002` (endpoint execution evidence)
- `E-SIM001-004` (alert fired confirming detection success)

**Lessons Learned:**  
> Not every phishing simulation produces full kill-chain network activity.  
> Detection objectives must align with **scenario scope and visibility**.

---

## 🧩 Issue 8: Phishing Email Delivered but User Click Was Not Generating Browser Telemetry

**Description:**  
The phishing email was successfully delivered to the target mailbox (`it.helpdesk1`), and the user opened the message, but clicking the embedded URL did not consistently generate the expected browser process execution logs.

**Impact:**  
- No Event ID 4688 for `chrome.exe` was generated  
- Splunk correlation queries returned no results  
- Alert did not trigger despite successful email delivery  
- Confusion between email delivery success vs detection failure  

**Root Cause:**  
The issue stemmed from **user interaction flow differences** in a lab environment:

- The link was opened in preview mode without full browser launch  
- Default browser association was temporarily changed  
- The link opened inside a mail client sandbox instead of Chrome  
- The URL was copied but not executed  

SIM-001 detection depends on **actual browser process creation**, not email artifacts.

**Resolution:**  
1. Verified default browser was set to Google Chrome.  
2. Confirmed the link was clicked in a way that launched a new browser process.  
3. Ensured the email client did not open links inside a restricted preview mode.  
4. Re-executed the click and confirmed Chrome launched.  
5. Verified Event ID 4688 appeared locally and in Splunk.

**Validation:**  
Once Chrome executed normally, process creation telemetry was captured and detection logic validated successfully.

**Evidence Reference:**  
- `E-SIM001-002` (Chrome execution with phishing URL)

**Lessons Learned:**  
> Email delivery does not equal detection.  
> Detection requires **behavioral execution**, not just message receipt.

---

## 🧩 Issue 9: Alert Triggered but Analyst Interpretation Was Initially Incorrect

**Description:**  
The Splunk alert triggered as expected, but initial interpretation suggested a broader compromise rather than a controlled phishing simulation.

**Impact:**  
- Alert appeared more severe than intended  
- Required manual review to confirm it was lab-generated activity  
- Highlighted the need for simulation tagging  

**Root Cause:**  
The alert output did not initially include clear **simulation identifiers**, making it indistinguishable from real malicious activity.

**Resolution:**  
1. Added `simulation_id="SIM-001"` to correlation query.  
2. Added `symbolic_id="LAB-SIM-001-PHISHING-ALERT"` for traceability.  
3. Updated alert configuration to preserve these fields.  

**Validation:**  
Subsequent alerts clearly displayed simulation identifiers, preventing confusion during review.

**Evidence Reference:**  
- `E-SIM001-008` (Correlation event with simulation_id + symbolic_id)

**Lessons Learned:**  
> Simulation environments must include tagging to distinguish lab activity from real incidents.  
> Detection engineering includes **alert clarity**, not just triggering logic.

---

## 🧠 Overall Takeaways

SIM-001 reinforced foundational SOC and detection engineering principles:

- Log ingestion and audit policy configuration are prerequisites for detection  
- Endpoint telemetry is often the **primary signal** for phishing activity  
- Network visibility is valuable but often partial or unavailable  
- Dynamic IP addressing requires **context-based correlation**  
- Troubleshooting methodology is as critical as detection logic itself  

These lessons align directly with the lab’s intentional design and real-world
SOC operating conditions.

---

## 🛡 GRC Note (Control Impact)

These issues affected the lab’s ability to Detect and Respond to phishing link execution
using endpoint telemetry and SIEM correlation.

- Impacted Control Area: Endpoint Logging / SIEM Monitoring / Alerting
- Control Status: Restored ✅
- Retest Required: Yes
- Retest Result: Pass ✅ (validated via 4688 evidence + Splunk correlation + alert trigger)

---

## 🏁 Status

- [x] Issues fully documented  
- [x] Root causes identified  
- [x] Resolutions validated  
- [x] Detection logic confirmed  
- [x] Simulation executed end-to-end  

> **SIM-001 is marked as ✅ Validated**  
> and is suitable for portfolio and interview discussion.
