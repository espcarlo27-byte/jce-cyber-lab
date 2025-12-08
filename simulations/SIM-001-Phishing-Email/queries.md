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
