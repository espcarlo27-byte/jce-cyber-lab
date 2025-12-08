# SIM-001 – Phishing Email (T1566.002) – SPL Queries

This file documents all Splunk searches used to detect, validate, and correlate phishing link activity for SIM-001 using Google Chrome on Windows 11.

The purpose of these queries is to:
- Detect the browser execution
- Identify the presence of a suspicious URL
- Check for follow-on PowerShell activity
- Correlate all relevant activity
- Support alerting and dashboarding

---
---

## 1. Chrome Browser Process Execution (Windows / Process Validation)

**Purpose:**  
This query confirms that Google Chrome executed on the Windows 11 endpoint.  
This is the first indication that the user interacted with something that launched a browser.

```spl
index=winevent_system OR index=winevent_security OR index=lab_index
(Image="*\\chrome.exe" OR NewProcessName="*\\chrome.exe")
| table _time, host, user, Image, ParentImage, CommandLine
| sort - _time
```

What This Confirms:
- Chrome was launched
- Which user launched it
- What process triggered it (Outlook.exe or explorer.exe)
- What command line was used

---
---

## 2. Chrome With Suspicious URL in Command Line (Direct Phishing Click)

**Purpose:**
This query confirms that Chrome executed with a URL passed directly in the command line, which strongly indicates that a user clicked a phishing link.

```spl
index=winevent_system OR index=winevent_security OR index=lab_index
(Image="*\\chrome.exe" OR NewProcessName="*\\chrome.exe")
("http://" OR "https://")
| table _time, host, user, Image, ParentImage, CommandLine
| sort - _time
```

What This Confirms:
- A URL was directly used to launch Chrome
- The activity is user-driven
- This is the primary phishing click indicator
    
---
---

## 3. PowerShell Fallback Detection (Post-Click Payload Check)

**Purpose:**
This query checks whether PowerShell executed after the phishing click.
It is used to identify potential payload downloads, command execution, or follow-on compromise.

```spl
index=winevent_powershell
| table _time, host, user, CommandLine, ScriptBlockText
| sort - _time
```

What This Detects:
- Encoded PowerShell commands
- Invoke-WebRequest
- IEX / DownloadString
- Script-based payload execution

---
---

## 4. Broad Phishing Keyword Hunt (Cross-Index)

Purpose:
This query performs a wide keyword-based search across all lab-related indexes to identify any phishing-related artifacts that may not be captured by strict process detection.

```spl
(index=lab_index OR index=winevent_system OR index=winevent_security)
("phish" OR "policy" OR "update" OR "http://" OR "https://")
| table _time, host, user, Image, ParentImage, CommandLine
| sort - _time
```

What This Helps With:
- Catching edge cases
- Verifying email artifacts
- Identifying alternate download methods

---
---

## 5. ✅ PRIMARY CORRELATION QUERY (ALERT-READY)

Purpose:
This is the main detection query for SIM-001.
It is the query used to:
- Validate the phishing click
- Generate a dashboard signal
- Trigger the Splunk alert

```spl
(index=winevent_system OR index=winevent_security OR index=lab_index)
(Image="*\\chrome.exe" OR NewProcessName="*\\chrome.exe")
("http://" OR "https://")
| eval simulation_id="SIM-001"
| eval symbolic_id="LAB-SIM-001-PHISHING-ALERT"
| table _time, host, user, Image, ParentImage, CommandLine, simulation_id, symbolic_id
| sort - _time
```

What This Confirms:
- A phishing link was clicked
- Chrome executed with a URL
- The event is tagged with:
  - simulation_id = SIM-001
  - symbolic_id = LAB-SIM-001-PHISHING-ALERT

This query is directly reused in the alert configuration.

---
---

## 6. Last 15 Minutes Validation (Post-Execution Check)

Purpose:
Used immediately after completing the simulation to confirm that activity occurred in real time.

```spl
(index=winevent_system OR index=winevent_security OR index=lab_index)
(Image="*\\chrome.exe" OR NewProcessName="*\\chrome.exe")
("http://" OR "https://")
earliest=-15m
| table _time, host, user, Image, ParentImage, CommandLine
| sort - _time
```

Expected Outcome:
- If results appear here → SIM-001 executed successfully
- If no results → something in the lab pipeline is broken

---
---

✅ Interpretation Guide
- Results in Section 2 → Confirmed phishing link click
- Results in Section 3 → Payload execution detected
- Results in Section 5 → Alert should fire
- Results in Section 6 → Real-time validation successful

This file serves as the core detection engineering artifact for SIM-001.
