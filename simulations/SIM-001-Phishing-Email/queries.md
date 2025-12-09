# SIM-001 – Phishing Email (T1566.002) – SPL Queries

This file documents all Splunk searches used to detect, validate, and correlate phishing link activity for SIM-001 using Google Chrome on Windows 11.

The purpose of these queries is to:
- Detect browser execution
- Identify the presence of a suspicious URL
- Check for follow-on PowerShell activity
- Correlate all relevant activity
- Support alerting and dashboarding

---

## 1. Chrome Browser Process Execution (Windows Security – Process Validation)

**Purpose:**  
This query confirms that Google Chrome executed on the Windows 11 endpoint using Windows Security Event 4688.  
This is the first indication that a user interacted with something that launched a browser.

```spl
(index=winevent_security OR index=winevent_system)
EventCode=4688
New_Process_Name="*\\chrome.exe"
| table _time, host, user, New_Process_Name, Process_Command_Line
| sort - _time
```

What This Confirms:
- Chrome was launched
- Which user launched it
- What command line was used

---

### 2. Chrome With Suspicious URL in Command Line (Direct Phishing Click)

**Purpose:**  
This query confirms that Chrome executed with a URL passed directly in the command line, which strongly indicates that a user clicked a phishing link.
```spl
(index=winevent_security OR index=winevent_system)
EventCode=4688
New_Process_Name="*\\chrome.exe"
Process_Command_Line="*http*"
| table _time, host, user, New_Process_Name, Process_Command_Line
| sort - _time
```

**What This Confirms:**
- A URL was directly used to launch Chrome
- The activity is user-driven
- This is the primary phishing click indicator

---

## 3. PowerShell Fallback Detection (Post-Click Payload Check)

**Purpose:**  
This query checks whether PowerShell executed after the phishing click.
It is used to identify potential payload downloads, command execution, or follow-on compromise.
```spl
index=winevent_powershell
| table _time, host, user, Process_Command_Line, ScriptBlockText
| sort - _time
```

**What This Detects:**
- Encoded PowerShell commands
- Invoke-WebRequest
- IEX / DownloadString
- Script-based payload execution

---

## 4. Broad Phishing Keyword Hunt (Cross-Index)

**Purpose:**  
This query performs a wide keyword-based search across all Windows log data to identify any phishing-related artifacts that may not be captured by strict process detection.
```spl
(index=winevent_security OR index=winevent_system)
("phish" OR "policy" OR "update" OR "http://" OR "https://")
| table _time, host, user, New_Process_Name, Process_Command_Line
| sort - _time
```

**What This Helps With:**
- Catching edge cases
- Verifying alternate download methods
- Identifying suspicious URL activity outside Chrome

---

## 5. ✅ PRIMARY CORRELATION QUERY (ALERT-READY – VALIDATED)

**Purpose:**  
This is the main detection query for SIM-001.
It is the query used to:
- Validate the phishing click
- Generate a dashboard signal
- Trigger the Splunk alert
```spl
(index=winevent_security OR index=winevent_system)
EventCode=4688
New_Process_Name="*\\chrome.exe"
Process_Command_Line="*http*"
| eval simulation_id="SIM-001"
| eval symbolic_id="LAB-SIM-001-PHISHING-ALERT"
| table _time, host, user, New_Process_Name, Process_Command_Line, simulation_id, symbolic_id
| sort - _time
```

**What This Confirms:**
- A phishing link was clicked
- Chrome executed with a URL
- The event is tagged with:
- simulation_id = SIM-001
- symbolic_id = LAB-SIM-001-PHISHING-ALERT

***✅ This query is directly reused in the live alert configuration.***

---

## 6. Last 15 Minutes Validation (Post-Execution Check)

**Purpose:**  
Used immediately after completing the simulation to confirm that activity occurred in real time.
```spl
(index=winevent_security OR index=winevent_system)
EventCode=4688
New_Process_Name="*\\chrome.exe"
Process_Command_Line="*http*"
earliest=-15m
| table _time, host, user, New_Process_Name, Process_Command_Line
| sort - _time
```

**Expected Outcome:**
- If results appear → SIM-001 executed successfully
- If no results → something in the lab pipeline requires troubleshooting

**✅ Interpretation Guide**
- Results in Section 2 → Confirmed phishing link click
- Results in Section 3 → Payload execution detected
- Results in Section 5 → Alert should fire
- Results in Section 6 → Real-time validation successful

---

***✅ This file serves as the core detection engineering artifact for SIM-001 and reflects the finalized, validated lab configuration.***


