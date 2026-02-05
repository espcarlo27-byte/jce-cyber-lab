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
- (Optional) Check for follow-on PowerShell activity
- Correlate relevant activity into a single detection
- Support alerting and analyst investigation
- Provide **email workflow context** (Zimbra delivery path) without changing detection logic

---

## Email Context (Supplemental – Not Required for Detection)

SIM-001 includes an email delivery path using the lab **Mail Server (Zimbra)**.
Email delivery provides realistic context (sender → recipient → click), but the
**detection signal remains endpoint process execution**.

These queries help analysts reconstruct the chain:

Email delivery → user interaction → browser execution → URL artifact captured

> Note: These are **context enrichment** queries. SIM-001 validation does **not**
> depend on mail server telemetry.

---

## 1. Baseline Chrome Process Execution (4688)

**Purpose:**  
Confirms that Google Chrome executed on the Windows 11 endpoint using
Windows Security Event ID **4688**. This establishes baseline browser execution
prior to URL analysis.

```spl
(index=winevent_security OR index=winevent_system)
EventCode=4688
New_Process_Name="*\\chrome.exe"
| table _time, host, user, Parent_Process_Name, New_Process_Name, Process_Command_Line
| sort - _time
```

**What This Confirms:**
- Chrome was launched
- Which user launched it
- Parent process context (user-driven vs automated)
- Command line visibility is present

**Related Log Evidence:**  
`E-SIM001-001` (Baseline Chrome Execution – 4688)

---

### 2. Chrome With Suspicious URL in Command Line (Primary Phishing Click Indicator)

**Purpose:**  
Confirms that Chrome executed with a URL passed directly in the command line,
which strongly indicates a user-driven phishing link click. In the Zimbra path,
this aligns with the user clicking a link from the mailbox.
```spl
(index=winevent_security OR index=winevent_system)
EventCode=4688
New_Process_Name="*\\chrome.exe"
Process_Command_Line="*http*"
| table _time, host, user, Parent_Process_Name, New_Process_Name, Process_Command_Line
| sort - _time
```

**What This Confirms:**
- A URL was directly used to launch Chrome
- The activity is user-driven
- This is the primary detection signal for SIM-001

**Related Log Evidence:**  
`E-SIM001-002` (Chrome executed with URL in command line – authoritative signal)

---

## 3. Mailbox/User Workflow Context (Chrome + Email Client Proximity)

**Purpose:**  
Provides investigation context by showing browser execution alongside common
mailbox interaction processes (if present), such as Outlook or other mail clients.
> This does not require mail telemetry; it uses endpoint process evidence only.
```spl
(index=winevent_security OR index=winevent_system)
EventCode=4688
(New_Process_Name="*\\chrome.exe" OR New_Process_Name="*\\outlook.exe")
| table _time, host, user, Parent_Process_Name, New_Process_Name, Process_Command_Line
| sort - _time
```

**What This Helps With:**
- Identifying user workflow around the click
- Showing if a mail client launched near the click timeframe
- Strengthening investigation narratives (email → click → execution)

**Related Log Evidence:**  
`E-SIM001-003`

---

## 4. Parent Process Context (User-Driven Launch Validation)

**Purpose:**  
Validates that chrome.exe was launched from typical user context
(e.g., explorer.exe or a mail client), supporting a user-driven phishing interaction
conclusion.
```spl
(index=winevent_security OR index=winevent_system)
EventCode=4688
New_Process_Name="*\\chrome.exe"
| table _time, host, user, Parent_Process_Name, New_Process_Name, Process_Command_Line
| sort - _time
```

**What This Helps With:**
- Confirming the parent process aligns with user-driven behavior
- Differentiating user interaction vs automated spawning

**Related Log Evidence:**  
`E-SIM001-004`

---

## 5. User-Focused Timeline (Investigation View)

**Purpose:**  
Shows a quick timeline of process creation activity for the target user during
SIM-001 execution (especially useful for Option A: Zimbra delivery path).
```spl
(index=winevent_security OR index=winevent_system)
EventCode=4688
user="it.helpdesk1"
| table _time, host, user, Parent_Process_Name, New_Process_Name, Process_Command_Line
| sort - _time
```

**What This Helps With:**
- Quickly viewing activity immediately before/after the click
- Confirming the correct user account is associated with the event

**Related Log Evidence:**  
`E-SIM001-005`

---

## 6. (Optional) PowerShell Follow-On Activity (Post-Click Payload Check)

**Purpose:**  
Checks whether PowerShell was executed after the phishing click. This query is
supplemental and used to identify potential payload execution or secondary compromise.
```spl
index=winevent_powershell
| table _time, host, user, Process_Command_Line, ScriptBlockText
| sort - _time
```

**What This May Detect:**
- Encoded PowerShell commands
- `Invoke-WebRequest` activity
- `IEX` / `DownloadString` usage
- Script-based payload execution

> Note: PowerShell execution is not required for SIM-001 validation.
> This query supports deeper investigation if additional activity occurs.

**Related Log Evidence:**  
`E-SIM001-006`

---

## 7. Broad Phishing Keyword Hunt (Exploratory / Analyst Context)

**Purpose:**  
Performs a wide keyword-based search across Windows logs to identify phishing-related
artifacts that may not be captured by strict process-based detection.

```spl
(index=winevent_security OR index=winevent_system)
("phish" OR "policy" OR "update" OR "http://" OR "https://")
| table _time, host, user, Parent_Process_Name, New_Process_Name, Process_Command_Line
| sort - _time
```

**What This Helps With:**

- Catching edge cases  
- Providing analyst context during investigation  
- Identifying alternate delivery methods (email vs non-email)

**Related Log Evidence:**  
`E-SIM001-007`

---

## 8. ✅ Primary Correlation Query (Alert-Ready – Validated)

**Purpose:**  
This is the authoritative detection/correlation query for SIM-001.  
It is used to:

- Validate the phishing link click  
- Generate an investigation signal  
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
- Chrome executed with a URL in the command line  
- The event is tagged for SIM tracking and alerting  

**Related Log Evidence:**  
`E-SIM001-008`

---

## 9. Last 15 Minutes Validation (Post-Execution Sanity Check)

**Purpose:**  
Used immediately after simulation execution to confirm real-time activity and verify  
ingestion and indexing health.

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

- Results appear → SIM-001 executed successfully  
- No results → investigate ingestion, logging, or execution path  

**Related Log Evidence:**  
`E-SIM001-009`

---

## Interpretation Guide

- **Query 2 results present:** Confirmed phishing link click signal  
- **Query 6 results present:** Potential follow-on payload behavior  
- **Query 8 results present:** Correlation/alert logic is functioning  
- **Query 9 results present:** Real-time validation successful  

---

## 10. Zimbra Mail Authentication Activity (Identity Signal)

**Purpose:**  
Validates that the mailbox involved in SIM-001 is associated with a known enterprise
identity and that authentication events from the mail system are observable.

```spl
index=zimbra_logs
("auth" OR "login" OR "authentication")
user="it.helpdesk1"
| table _time, host, user, message
| sort - _time
```

**What This Confirms:**

- The mailbox is identity-backed  
- Mail authentication telemetry exists  
- Email activity is tied to enterprise IAM  

**Detection Role:**  
Identity-layer evidence supporting phishing investigation.

**Related Log Evidence:**  
`E-SIM001-010` (Zimbra authentication event for user mailbox – identity validation)

---

## 11. Active Directory Authentication Events (Identity Context)

**Purpose:**  
Confirms that the user involved in the phishing simulation is an Active Directory identity with observable authentication activity.

```spl
index=winevent_security
(EventCode=4624 OR EventCode=4625)
Account_Name="it.helpdesk1"
| table _time, host, Account_Name, EventCode, Logon_Type, Authentication_Package_Name
| sort - _time
```

**What This Confirms:**

- The user is a valid AD account  
- Successful and failed logons are visible  
- Identity monitoring is active in the lab  

**Detection Role:**  
Provides identity telemetry independent of endpoint process logs.

**Related Log Evidence:**  
`E-SIM001-011` (AD logon event for simulation user – IAM telemetry proof)

---

## 12. Identity → Endpoint Correlation (Enterprise SOC View)

**Purpose:**  
Correlates identity authentication activity with endpoint phishing execution to demonstrate cross-layer detection capability.

```spl
(
    index=winevent_security EventCode=4624 Account_Name="it.helpdesk1"
) OR (
    (index=winevent_security OR index=winevent_system)
    EventCode=4688
    New_Process_Name="*\\chrome.exe"
    Process_Command_Line="*http*"
)
| eval event_type=case(EventCode==4624,"AD Logon", EventCode==4688,"Process Execution")
| table _time, host, Account_Name, event_type, New_Process_Name, Process_Command_Line
| sort - _time
```

**What This Proves:**

- The same identity authenticated to AD  
- That identity executed the phishing link  
- The attack chain spans IAM → Endpoint  

**Detection Role:**  
Demonstrates SOC-level identity correlation.

**Related Log Evidence:**  
`E-SIM001-012` (Correlated identity authentication and phishing execution timeline)

---

## ✅ Final Note

> This file represents the finalized detection engineering logic for SIM-001.  
> Endpoint telemetry is authoritative; additional data sources (mail workflow context, network telemetry)  
> are supplemental.
