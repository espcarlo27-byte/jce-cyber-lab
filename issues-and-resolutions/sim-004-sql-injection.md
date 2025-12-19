# Simulation 4 – SQL Injection (T1190)
## ⚠️ Issues & Resolutions
This document captures real operational issues encountered during SIM-003 and the structured methodology used to identify, resolve, and validate each one.

---

### ***🧩 Issue 1 – DVWA Security Level Reverted to “Impossible”***
**Description**  
Initial SQL injection payloads (1 OR 1=1, UNION SELECT) produced no visible change in DVWA output, despite the application being reachable and responsive.

**Impact**  
SQL injection execution could not be validated, creating a false assumption that:
- The attack failed, or
- Network detection was not functioning
This blocked progression of SIM-004 testing.

**Root Cause**  
DVWA security level had silently reverted from Low to Impossible, which prevents SQL injection by design. DVWA does not reliably persist security levels across sessions.

**Resolution**
- Navigated to DVWA Security
- Reset security level to Low
- Logged out and logged back in to ensure session persistence
- Retested SQL injection successfully

**Validation**
- Payload 1 OR 1=1# returned multiple user records
- SQL injection behavior confirmed at the application layer

**Lessons Learned**
> Application security controls must always be validated before concluding an attack or detection failure. Configuration drift can invalidate testing assumptions.

---

### ***🧩 Issue 2 – Security Onion Initially Had No Traffic Visibility***
**Description**  
Suricata and Zeek services were running, but no alerts were generated during early SQL injection attempts.

**Impact**  
IDS appeared non-functional, preventing validation of network-based detection for SIM-004.

**Root Cause**  
Traffic between Kali and DVWA was traversing the same L2 network and bypassing Security Onion. The sensor was not inline with the traffic path.

**Resolution**
- Identified Security Onion monitoring interface (ens192)
- Forced Kali’s default gateway to Security Onion
- Enabled IP forwarding on Security Onion
- Verified packet flow using tcpdump

**Validation**
- Kali → DVWA traffic observed on ens192
- Consumption EPS increased
- Suricata alerts generated following SQL injection

**Lessons Learned**
> IDS effectiveness depends on correct network placement. Visibility failures are often architectural, not rule-based.

---

### ***🧩 Issue 3 – SQL Injection Detected as Generic Web Alert***
**Description**  
SQL injection executed successfully but did not trigger a Suricata alert explicitly labeled “SQL Injection.”

**Impact**  
This initially created uncertainty about whether the attack was detected at the network layer.

**Root Cause**  
Default Suricata rule sets may classify SQL injection attempts as generic web policy violations rather than explicit SQLi signatures, depending on payload and context.

**Resolution**
- Reviewed ET WEB_SERVER alert details
- Confirmed attacker (10.0.0.30) and victim (10.0.0.60) IPs
- Correlated alert timestamp with SQL injection execution

**Validation**
- Suricata alert fired immediately after SQL injection
- Alert details confirmed HTTP-based exploit activity

**Lessons Learned**
> Detection does not always equal perfect classification. Analysts must interpret context, not rely solely on alert labels.

---

### ***🧩 Issue 4 – Suricata Alerts Not Visible in Splunk***
**Description**  
Suricata alerts were visible in Security Onion but could not be found in Splunk searches.

**Impact**  
End-to-end SIEM correlation could not be demonstrated for SIM-004.

**Root Cause**  
No Splunk Universal Forwarder was installed on Security Onion. As a result, Suricata alerts remained within the Elastic stack and were never forwarded to Splunk.

**Resolution**
- Verified absence of Splunk Forwarder processes and services
- Confirmed no Splunk input configuration existed
- Documented integration gap instead of forcing deployment

**Validation**
- Suricata alerts confirmed in Security Onion
- Log pipeline boundary clearly identified and documented

**Lessons Learned**
> Detection platforms often operate independently from SIEMs unless explicitly integrated. Identifying and documenting ingestion gaps is a critical SOC skill.

---

## 🔍 Overall Takeaway
SIM-004 demonstrated that successful detection of SQL injection depends as much on network architecture and log pipeline design as it does on exploit execution. While the SQL injection attack was successfully executed and detected at the network layer by Suricata, the absence of SIEM integration highlighted a common real-world scenario where detections exist but are siloed within individual platforms. This simulation reinforced the importance of validating application security state, ensuring inline traffic visibility, and understanding integration boundaries between detection and aggregation tools.

---

## 📌 Status
Resolved with documented limitation
- SQL injection execution: ✅ Confirmed
- Network-based detection: ✅ Confirmed (Suricata / Security Onion)
- SIEM ingestion (Splunk): ⚠️ Not implemented by design
- Detection gap: ✅ Identified, validated, and documented

SIM-004 is considered complete with detection validated at the IDS layer and integration limitations clearly recorded for future enhancement.
