# SIM-001 – Phishing Email (T1566.002)
## Detection Engineering & Correlation Logic (SPL)

This document defines the finalized detection logic used in SIM-001.

Detection is based on **multi-layer correlation** and does NOT rely on
`Process_Command_Line` URL visibility.

SIM-001 follows this evidence chain:

Identity → Endpoint Execution → Network Confirmation → SIEM Correlation

---

## 🧠 Detection Philosophy

Authoritative detection is established through:

1. Endpoint process execution (Event ID 4688 / Sysmon Event ID 1)
2. Time-aligned outbound HTTP request to attacker infrastructure
3. Identity authentication context (AD logon events)
4. Optional server-side confirmation (Apache access logs)

URL visibility in `Process_Command_Line` is unreliable in webmail-based phishing
and is not required for detection validation.

---

## 1. Identity Context – AD Logon Validation

**Purpose:** Confirm enterprise user authentication context.

```spl
index=winevent_security EventCode=4624 Account_Name="it.helpdesk1"
| table _time host Account_Name Logon_Type Authentication_Package_Name
| sort - _time
```

> Role: Identity attribution layer.

---

## 2. Endpoint Execution – Browser Process Creation

Purpose: Detect browser execution during phishing click window.
```spl
index=winevent_security EventCode=4688
user="it.helpdesk1"
new_process_name="*chrome.exe"
| table _time host user parent_process_name new_process_name
| sort - _time
```

What This Confirms:
- Browser executed
- Correct user context
- Timestamp aligns with click window

> This confirms execution, not URL content.

---

## 3. Sysmon Execution Context (If Enabled)
```spl
index=network_logs dest_ip="<KALI_IP>"
| table _time src_ip dest_ip dest_port action
| sort - _time
```

> Role: Enhanced endpoint telemetry.

---

## 4. Network Correlation – Outbound HTTP Confirmation

If firewall or network logs are ingested:
```spl
index=network_logs dest_ip="<KALI_IP>"
| table _time src_ip dest_ip dest_port action
| sort - _time
```

What This Confirms:
- Victim initiated outbound connection
- Destination equals attacker infrastructure
- Timestamp proximity to browser execution

> This is primary click confirmation.

---

## 5. Identity + Endpoint Correlation
```spl
(
    index=winevent_security EventCode=4624 Account_Name="it.helpdesk1"
) OR (
    index=winevent_security EventCode=4688
    user="it.helpdesk1"
    new_process_name="*chrome.exe"
)
| eval event_type=case(EventCode==4624,"AD Logon", EventCode==4688,"Process Execution")
| table _time host Account_Name user event_type new_process_name
| sort - _time
```

> This demonstrates authentication followed by execution.

---

## 6. Full Correlation Logic (Authoritative Detection Query)
```spl
(
    index=winevent_security EventCode=4688
    user="it.helpdesk1"
    new_process_name="*chrome.exe"
)
OR
(
    index=network_logs dest_ip="<KALI_IP>"
)
| eval simulation_id="SIM-001"
| table _time host user dest_ip new_process_name simulation_id
| sort - _time
```

Detection Model:
```txt
Browser Execution
AND
Outbound Connection
Within Short Time Window
```

---

## 7. 15-Minute Post-Execution Validation
```spl
(
    index=winevent_security EventCode=4688 new_process_name="*chrome.exe"
) OR (
    index=network_logs dest_ip="<KALI_IP>"
)
earliest=-15m
| table _time host user dest_ip new_process_name
| sort - _time
```

> Used immediately after simulation execution.

---

## 8. False Positive Considerations

Possible benign causes:

- User manually browsing Kali IP  
- Internal web testing  
- Automated browser background activity  

### Mitigation Strategies

- Restrict detection to known attacker IP  
- Apply 1–2 minute correlation window  
- Require identity + endpoint + network signals  

---

## 9. Enterprise Scaling Considerations

In a real enterprise SOC, this detection would scale using:

- EDR telemetry (process + network)  
- Proxy logs (URL visibility at perimeter)  
- DNS logs (domain resolution tracking)  
- Firewall logs (egress monitoring)  
- Threat intelligence enrichment  
- Risk scoring engines  

Enterprise detection would typically:

- Correlate user identity  
- Detect outbound request to suspicious domain/IP  
- Cross-reference threat intelligence feeds  
- Assign risk score to user/session  
- Trigger automated containment if threshold met  

SIM-001 models this workflow at lab scale using:

> Active Directory + Windows Logs + Apache Logs + Splunk Correlation  

---

## 🏁 Detection Authority

**Detection Authority:** Multi-Layer Correlation  

This approach reflects enterprise SOC methodology and avoids brittle  
single-field string matching.





