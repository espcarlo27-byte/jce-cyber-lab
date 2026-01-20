# SIM-001 – Phishing Email (T1566.002) – SPL Queries

This file documents all Splunk searches used to **detect, validate, and correlate**
phishing link activity for **SIM-001** using Google Chrome on a Windows 11 endpoint.

SIM-001 is designed as an **endpoint-driven detection**.
Windows Security Event **4688** is treated as the **authoritative signal**.
Network telemetry, when available, is considered **supplemental only**.

---

## Detection Objectives

The purpose of these queries is to:

- Detect browser execution on the endpoint
- Identify the presence of a URL in the browser command line
- Check for optional follow-on PowerShell activity
- Correlate relevant activity into a single detection
- Support alerting and analyst investigation

---

## 1. Chrome Browser Process Execution  
*(Windows Security – Baseline Validation)*

**Purpose:**  
Confirms that Google Chrome executed on the Windows 11 endpoint using
Windows Security Event ID **4688**.

This establishes baseline browser execution prior to URL analysis.

```spl
(index=winevent_security OR index=winevent_system)
EventCode=4688
New_Process_Name="*\\chrome.exe"
| table _time, host, user, New_Process_Name, Process_Command_Line
| sort - _time
```

**What This Confirms:**
- Chrome was launched
- Which user launched it
- What command line was used

**Related Log Evidence:**
- `E-SIM001-002` (Baseline Chrome Execution – 4688)
---

### 2. Chrome With Suspicious URL in Command Line (Primary Phishing Click Indicator)

**Purpose:**  
Confirms that Chrome executed with a URL passed directly in the
command line, which strongly indicates a user-driven phishing link click.
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
- This is the primary detection signal for SIM-001

**Related Log Evidence:**
- `E-SIM001-003` (Chrome executed with URL in command line – authoritative signal)

---

## 3. Optional - PowerShell Fallback Detection (Post-Click Payload Check)

**Purpose:**  
Checks whether PowerShell was executed after the phishing click.
This query is supplemental and used to identify potential
payload execution or secondary compromise.
```spl
index=winevent_powershell
| table _time, host, user, Process_Command_Line, ScriptBlockText
| sort - _time
```

**What This May Detect:**
- Encoded PowerShell commands
- Invoke-WebRequest activity
- IEX / DownloadString usage
- Script-based payload execution

> Note:
> PowerShell execution is not required for SIM-001 validation.
> This query supports deeper investigation if additional activity occurs.

---

## 4. Broad Phishing Keyword Hunt (Exploratory / Analyst Context)

**Purpose:**  
Performs a wide keyword-based search across Windows logs to identify
phishing-related artifacts that may not be captured by strict
process-based detection.
```spl
(index=winevent_security OR index=winevent_system)
("phish" OR "policy" OR "update" OR "http://" OR "https://")
| table _time, host, user, New_Process_Name, Process_Command_Line
| sort - _time
```

**What This Helps With:**
- Catching edge cases
- Providing analyst context during investigation
- Identifying alternate delivery methods

---

## 5. ✅ PRIMARY CORRELATION QUERY (ALERT-READY – VALIDATED)

**Evidence ID:** `E-SIM001-004`

**Purpose:**  
This is the authoritative detection query for SIM-001.  
It is the query used to:
- Validate the phishing link click
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

## 6. Last 15 Minutes Validation (Post-Execution Sanity Check)

**Purpose:**  
Used immediately after simulation execution to confirm real-time activity
and verify ingestion and indexing health.
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
- Results in Section 3 → Follow-on payload activity (if any)
- Results in Section 5 → Alert should fire
- Results in Section 6 → Real-time validation successful

---

***✅ This file represents the finalized detection engineering logic for SIM-001.
Endpoint telemetry is authoritative; additional data sources are supplemental.***


