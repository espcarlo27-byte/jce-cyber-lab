# Detection Queries — SIM-001

## Suspicious Process Execution (whoami.exe)
index=winlog sourcetype=XmlWinEventLog EventCode=1
| eval exe=lower(Image)
| search exe="c:\\windows\\system32\\whoami.exe"
| table _time host User Image ParentImage CommandLine

## Expanded Detection (whoami.exe, certutil.exe, powershell.exe)
index=winlog sourcetype=XmlWinEventLog EventCode=1
| eval exe=lower(Image)
| search exe="c:\\windows\\system32\\whoami.exe" OR exe="c:\\windows\\system32\\certutil.exe" OR exe="c:\\windows\\system32\\windowspowershell\\v1.0\\powershell.exe"
| table _time host User Image ParentImage CommandLine




### `logs.md`


# Symbolic Log Entries — SIM-001

## LAB-SIM-SYSMON-2025-126 — Suspicious Process Execution Detected

**Symptom:**  
Forwarder inactive, Sysmon events not visible.

**Fix Sequence:**  
1. Corrected ACL with `wevtutil sl`  
2. Restarted SplunkForwarder service  
3. Verified Sysmon events in Splunk (`index=winlog`)  
4. Ran SPL query to detect `powershell.exe → whoami.exe` chain  

**Validation:**  
- Forwarder active  
- Sysmon ProcessCreate event captured  
- SPL query matched expected process chain  

---

## LAB-SIM-SYSMON-2025-127 — Splunk Alert Configured

**Symptom:**  
Detection query built but no automated alerting.

**Fix Sequence:**  
1. Pasted SPL into Splunk Web  
2. Saved query as alert  
3. Trigger condition: Number of Results > 0  
4. Severity set to Medium  

**Validation:**  
- Alert fires when suspicious process executed  
- Sysmon event visible in `winlog` index  
- Detection matrix entry created

