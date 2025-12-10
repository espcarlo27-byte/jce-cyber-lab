# Simulation 1 – Phishing Email  
## Issues & Resolutions

---

### Issue 1: Logs Not Appearing in Splunk

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

