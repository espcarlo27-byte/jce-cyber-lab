# Microsoft Sentinel KQL Detection Queries

## Executive Summary

This document contains Kusto Query Language (KQL) queries developed for security-event validation, threat detection, investigation, and hunting within the JCE Enterprise-Modeled Security Operations Environment.

The queries focus on Windows authentication activity, process execution, PowerShell usage, account changes, endpoint telemetry, and Microsoft Sentinel data-ingestion validation.

> **Implementation Note:**  
> Windows security events may appear in the `SecurityEvent` table, while Sysmon and other Windows event channels may appear in the `WindowsEvent` table. Queries must be adjusted according to the active connector, Data Collection Rule, and available table schema.

---

# Query Objectives

- Confirm security telemetry ingestion.
- Identify failed and successful authentication activity.
- Detect repeated failed-logon attempts.
- Review suspicious process execution.
- Identify PowerShell activity.
- Monitor user and group-management events.
- Review Sysmon process-creation telemetry.
- Support alert validation and incident investigation.
- Align detection activity with MITRE ATT&CK.

---

# 1. Verify Available Tables

Use this query to identify tables receiving data during the selected time range.

```kql
search *
| where TimeGenerated >= ago(24h)
| summarize EventCount = count() by $table
| order by EventCount desc
```

## Purpose

This query provides an initial inventory of populated Log Analytics tables and helps determine whether security telemetry is being ingested into tables such as:

- `SecurityEvent`
- `WindowsEvent`
- `SigninLogs`
- `AzureActivity`
- `Heartbeat`

---

# 2. Verify Windows Security Event Ingestion

```kql
SecurityEvent
| where TimeGenerated >= ago(24h)
| summarize EventCount = count() by EventID
| order by EventCount desc
```

## Purpose

Confirms whether Windows security events are arriving in the `SecurityEvent` table and identifies the most frequently collected Event IDs.

---

# 3. Review Recent Windows Security Events

```kql
SecurityEvent
| where TimeGenerated >= ago(1h)
| project
    TimeGenerated,
    Computer,
    EventID,
    Activity,
    Account,
    SubjectAccount,
    TargetAccount
| order by TimeGenerated desc
```

## Purpose

Provides a chronological view of recent Windows security events for initial validation and investigation.

---

# 4. Failed Logons — Event ID 4625

```kql
SecurityEvent
| where TimeGenerated >= ago(24h)
| where EventID == 4625
| project
    TimeGenerated,
    Computer,
    TargetAccount,
    Account,
    IpAddress,
    WorkstationName,
    LogonType,
    FailureReason,
    Activity
| order by TimeGenerated desc
```

## Purpose

Identifies failed Windows logon attempts and displays available account, host, IP address, logon type, and failure-reason information.

## MITRE ATT&CK

- **Tactic:** Credential Access
- **Technique:** T1110 — Brute Force

---

# 5. Repeated Failed Logons by Account

```kql
SecurityEvent
| where TimeGenerated >= ago(1h)
| where EventID == 4625
| summarize
    FailedAttempts = count(),
    SourceIPs = make_set(IpAddress, 10),
    Computers = make_set(Computer, 10),
    FirstSeen = min(TimeGenerated),
    LastSeen = max(TimeGenerated)
    by TargetAccount
| where FailedAttempts >= 5
| order by FailedAttempts desc
```

## Purpose

Identifies accounts experiencing multiple failed logons within one hour. The threshold is intentionally adjustable and should be tuned against normal environment behavior.

---

# 6. Repeated Failed Logons by Source IP

```kql
SecurityEvent
| where TimeGenerated >= ago(1h)
| where EventID == 4625
| where isnotempty(IpAddress)
| summarize
    FailedAttempts = count(),
    TargetAccounts = make_set(TargetAccount, 20),
    Computers = make_set(Computer, 20),
    FirstSeen = min(TimeGenerated),
    LastSeen = max(TimeGenerated)
    by IpAddress
| where FailedAttempts >= 10
| order by FailedAttempts desc
```

## Purpose

Detects source IP addresses generating repeated failed authentication attempts against one or more accounts.

---

# 7. Successful Logons — Event ID 4624

```kql
SecurityEvent
| where TimeGenerated >= ago(24h)
| where EventID == 4624
| project
    TimeGenerated,
    Computer,
    TargetAccount,
    Account,
    IpAddress,
    WorkstationName,
    LogonType,
    Activity
| order by TimeGenerated desc
```

## Purpose

Reviews successful Windows authentication events and provides context for validating user, host, source, and logon-type activity.

---

# 8. Failed Logon Followed by Successful Logon

```kql
let FailedLogons =
    SecurityEvent
    | where TimeGenerated >= ago(24h)
    | where EventID == 4625
    | project
        FailedTime = TimeGenerated,
        Computer,
        TargetAccount,
        FailedSourceIP = IpAddress;
let SuccessfulLogons =
    SecurityEvent
    | where TimeGenerated >= ago(24h)
    | where EventID == 4624
    | project
        SuccessTime = TimeGenerated,
        Computer,
        TargetAccount,
        SuccessSourceIP = IpAddress,
        LogonType;
FailedLogons
| join kind=inner SuccessfulLogons on Computer, TargetAccount
| where SuccessTime between (FailedTime .. FailedTime + 15m)
| project
    FailedTime,
    SuccessTime,
    TargetAccount,
    Computer,
    FailedSourceIP,
    SuccessSourceIP,
    LogonType
| order by SuccessTime desc
```

## Purpose

Correlates failed authentication with a subsequent successful logon for the same account and computer within 15 minutes.

## Investigation Considerations

- Was the successful logon expected?
- Did the source IP change?
- Is the account authorized to access the system?
- Did suspicious endpoint or network activity follow the logon?
- Is the behavior normal for the user and host?

---

# 9. Process Creation — Event ID 4688

```kql
SecurityEvent
| where TimeGenerated >= ago(24h)
| where EventID == 4688
| project
    TimeGenerated,
    Computer,
    Account,
    NewProcessName,
    ParentProcessName,
    CommandLine,
    ProcessId,
    NewProcessId
| order by TimeGenerated desc
```

## Purpose

Reviews Windows process-creation events collected through Security Event ID 4688.

> Command-line information depends on the Windows audit-policy configuration and available event fields.

---

# 10. PowerShell Process Execution

```kql
SecurityEvent
| where TimeGenerated >= ago(24h)
| where EventID == 4688
| where NewProcessName has_any (
    "powershell.exe",
    "pwsh.exe",
    "powershell_ise.exe"
)
| project
    TimeGenerated,
    Computer,
    Account,
    NewProcessName,
    ParentProcessName,
    CommandLine
| order by TimeGenerated desc
```

## Purpose

Identifies PowerShell-related process creation for investigation and baseline comparison.

## MITRE ATT&CK

- **Tactic:** Execution
- **Technique:** T1059.001 — PowerShell

---

# 11. Potentially Suspicious PowerShell Commands

```kql
SecurityEvent
| where TimeGenerated >= ago(24h)
| where EventID == 4688
| where NewProcessName has_any (
    "powershell.exe",
    "pwsh.exe"
)
| where CommandLine has_any (
    "-enc",
    "-encodedcommand",
    "frombase64string",
    "downloadstring",
    "invoke-webrequest",
    "invoke-expression",
    "iex ",
    "bypass",
    "hidden",
    "nop"
)
| project
    TimeGenerated,
    Computer,
    Account,
    NewProcessName,
    ParentProcessName,
    CommandLine
| order by TimeGenerated desc
```

## Purpose

Flags PowerShell command lines containing terms frequently associated with encoded, downloaded, obfuscated, or policy-bypassing execution.

## Tuning Notes

A keyword match alone does not prove malicious activity. Validate:

- User and host
- Parent process
- Full command line
- File and URL indicators
- Network connections
- Authentication activity
- Normal administrative behavior

---

# 12. New User Account Created — Event ID 4720

```kql
SecurityEvent
| where TimeGenerated >= ago(7d)
| where EventID == 4720
| project
    TimeGenerated,
    Computer,
    SubjectAccount,
    TargetAccount,
    Activity
| order by TimeGenerated desc
```

## Purpose

Identifies newly created Windows user accounts.

## MITRE ATT&CK

- **Tactic:** Persistence
- **Technique:** T1136.001 — Local Account

---

# 13. User Added to a Privileged Local Group — Event ID 4732

```kql
SecurityEvent
| where TimeGenerated >= ago(7d)
| where EventID == 4732
| project
    TimeGenerated,
    Computer,
    SubjectAccount,
    MemberName,
    TargetAccount,
    Activity
| order by TimeGenerated desc
```

## Purpose

Reviews additions to local security groups. Investigators should determine whether the affected group is privileged and whether the change was authorized.

---

# 14. User Added to a Privileged Domain Group — Event ID 4728

```kql
SecurityEvent
| where TimeGenerated >= ago(7d)
| where EventID == 4728
| project
    TimeGenerated,
    Computer,
    SubjectAccount,
    MemberName,
    TargetAccount,
    Activity
| order by TimeGenerated desc
```

## Purpose

Identifies additions to domain global groups, including potentially privileged groups.

## Investigation Considerations

- Who initiated the change?
- Which account was added?
- Which group received the new member?
- Was there an approved change request?
- Did the account perform sensitive activity afterward?

---

# 15. Account Lockouts — Event ID 4740

```kql
SecurityEvent
| where TimeGenerated >= ago(24h)
| where EventID == 4740
| project
    TimeGenerated,
    Computer,
    TargetAccount,
    SubjectAccount,
    Activity
| order by TimeGenerated desc
```

## Purpose

Reviews Windows account lockouts that may result from mistyped passwords, stale credentials, automated services, password spraying, or brute-force activity.

---

# 16. Security Logs Cleared — Event ID 1102

```kql
SecurityEvent
| where TimeGenerated >= ago(30d)
| where EventID == 1102
| project
    TimeGenerated,
    Computer,
    Account,
    SubjectAccount,
    Activity
| order by TimeGenerated desc
```

## Purpose

Detects security audit-log clearing, which should be investigated because it may represent administrative maintenance or an attempt to remove evidence.

## MITRE ATT&CK

- **Tactic:** Defense Evasion
- **Technique:** T1070.001 — Clear Windows Event Logs

---

# 17. Verify WindowsEvent Ingestion

```kql
WindowsEvent
| where TimeGenerated >= ago(24h)
| summarize EventCount = count() by Channel, EventID
| order by EventCount desc
```

## Purpose

Determines which Windows event channels and Event IDs are being collected into the `WindowsEvent` table.

---

# 18. Review Recent Sysmon Events

```kql
WindowsEvent
| where TimeGenerated >= ago(24h)
| where Provider has "Sysmon"
| project
    TimeGenerated,
    Computer,
    Channel,
    EventID,
    Provider,
    EventData
| order by TimeGenerated desc
```

## Purpose

Confirms whether Sysmon telemetry is present and exposes the parsed `EventData` object for schema validation.

---

# 19. Sysmon Process Creation — Event ID 1

```kql
WindowsEvent
| where TimeGenerated >= ago(24h)
| where Provider has "Sysmon"
| where EventID == 1
| extend
    Image = tostring(EventData.Image),
    ParentImage = tostring(EventData.ParentImage),
    CommandLine = tostring(EventData.CommandLine),
    User = tostring(EventData.User),
    ProcessId = tostring(EventData.ProcessId),
    ParentProcessId = tostring(EventData.ParentProcessId)
| project
    TimeGenerated,
    Computer,
    User,
    Image,
    ParentImage,
    CommandLine,
    ProcessId,
    ParentProcessId
| order by TimeGenerated desc
```

## Purpose

Reviews Sysmon process-creation events and extracts common process fields from `EventData`.

> Field names must be confirmed against the actual workspace schema before using this query in an analytics rule.

---

# 20. Suspicious Sysmon Process Execution

```kql
WindowsEvent
| where TimeGenerated >= ago(24h)
| where Provider has "Sysmon"
| where EventID == 1
| extend
    Image = tostring(EventData.Image),
    ParentImage = tostring(EventData.ParentImage),
    CommandLine = tostring(EventData.CommandLine),
    User = tostring(EventData.User)
| where
    Image has_any (
        "powershell.exe",
        "pwsh.exe",
        "cmd.exe",
        "wscript.exe",
        "cscript.exe",
        "mshta.exe",
        "rundll32.exe",
        "regsvr32.exe"
    )
    or CommandLine has_any (
        "-encodedcommand",
        "frombase64string",
        "downloadstring",
        "invoke-expression"
    )
| project
    TimeGenerated,
    Computer,
    User,
    Image,
    ParentImage,
    CommandLine
| order by TimeGenerated desc
```

## Purpose

Identifies selected scripting and Windows utility processes for investigation. These utilities are legitimate, so results require context and behavioral validation.

---

# 21. Heartbeat Validation

```kql
Heartbeat
| where TimeGenerated >= ago(24h)
| summarize
    LastHeartbeat = max(TimeGenerated)
    by Computer, OSType, Version
| order by LastHeartbeat desc
```

## Purpose

Checks whether monitored hosts are communicating with Azure Monitor and identifies the most recent heartbeat received from each system.

---

# 22. Daily Security Event Volume

```kql
SecurityEvent
| where TimeGenerated >= ago(7d)
| summarize EventCount = count() by bin(TimeGenerated, 1d)
| order by TimeGenerated asc
```

## Purpose

Tracks daily Windows security-event volume and helps identify collection gaps or unexpected ingestion changes.

---

# 23. Event Volume by Computer

```kql
SecurityEvent
| where TimeGenerated >= ago(24h)
| summarize EventCount = count() by Computer
| order by EventCount desc
```

## Purpose

Compares security-event volume across monitored computers.

---

# Investigation Methodology

Query results are reviewed using the following structured workflow:

1. **Validate** the event and confirm that the activity occurred.
2. **Gather context** about the user, host, source, destination, and timestamp.
3. **Investigate** relevant endpoint, identity, network, email, and cloud telemetry.
4. **Correlate** related events across multiple data sources.
5. **Decide** whether the activity is benign, suspicious, or malicious.
6. **Respond** according to the appropriate incident-response procedure.

---

# Query Validation Status

| Query Category | Status |
|---|---|
| Table discovery | Ready for validation |
| SecurityEvent ingestion | Pending telemetry confirmation |
| Failed logons | Ready for validation |
| Successful logons | Ready for validation |
| Process creation | Pending Event ID 4688 ingestion |
| PowerShell activity | Pending process-event ingestion |
| Account-management events | Pending applicable event generation |
| WindowsEvent ingestion | Pending telemetry confirmation |
| Sysmon process creation | Pending Sysmon ingestion confirmation |
| Heartbeat validation | Ready for validation |

---

# Important Limitations

- Not every query has been converted into a production analytics rule.
- Query results require contextual validation before being classified as malicious.
- Event fields may differ depending on the connector and table schema.
- Collection depends on Azure Monitor Agent, Data Collection Rules, audit policy, and endpoint configuration.
- Detection thresholds require tuning against normal environment behavior.
- Sysmon collection must be separately configured and validated.
- Queries marked as pending should not be represented as operationally validated until supporting telemetry is confirmed.

---

# MITRE ATT&CK Coverage

| Detection Area | MITRE ATT&CK Technique |
|---|---|
| Repeated failed logons | T1110 — Brute Force |
| PowerShell activity | T1059.001 — PowerShell |
| New account creation | T1136.001 — Local Account |
| Privileged-group modification | T1098 — Account Manipulation |
| Security-log clearing | T1070.001 — Clear Windows Event Logs |
| Suspicious process execution | T1059 — Command and Scripting Interpreter |

---

# Lessons Learned

- Data must be validated before detection logic can be trusted.
- Table and field availability depend on the selected connector and collection method.
- Authentication detections require user, host, source, and behavioral context.
- Legitimate administrative tools can also be used maliciously.
- Threshold-based queries require baseline analysis and tuning.
- Correlation across identity, endpoint, network, and cloud telemetry produces stronger conclusions than reviewing a single event in isolation.
- Detection documentation should clearly distinguish completed validation from planned testing.

---

# Next Steps

- Complete Windows Security Events ingestion validation.
- Confirm Azure Monitor Agent and Data Collection Rule operation.
- Validate the `SecurityEvent` and `WindowsEvent` schemas.
- Confirm Sysmon telemetry ingestion.
- Generate controlled failed- and successful-logon events.
- Test process-creation and PowerShell queries.
- Convert validated queries into Microsoft Sentinel analytics rules.
- Add screenshots and investigation evidence.
- Record tuning decisions and false-positive observations.

---

**Document Status**

🟡 Queries documented and ready for controlled validation.

Operational status will be updated as telemetry onboarding and detection testing are completed.
