# SIM-001 – Phishing Email (T1566.002) – Log Evidence

This document captures the **actual telemetry evidence** generated during
SIM-001 execution and used to validate detection logic.

SIM-001 is an **endpoint-driven phishing detection simulation**.  
Windows Security Event **ID 4688 (Process Creation)** is treated as the
**authoritative detection signal**. Network telemetry, when present, is
supplemental.

---

## Log Sources Used

| Source | Purpose |
|--------|---------|
| Windows Security Log | Process creation visibility (Event ID 4688) |
| Splunk Indexes | Event ingestion, searching, and alerting |
| Security Onion (Optional) | Network HTTP confirmation |

---

## 🔄 Field Normalization Notes

The following field mappings were confirmed as reliable in this lab environment:

### Windows Security (Event ID 4688)

| Field | Description |
|------|-------------|
| `user` | Normalized as actor |
| `New_Process_Name` | Executed process |
| `Process_Command_Line` | Contains execution arguments (URL artifact) |
| `host` | Endpoint hostname |

### Optional Network Telemetry (HTTP)

| Field | Description |
|------|-------------|
| `src_ip` | Endpoint IP (DHCP-assigned) |
| `dest_ip` | Phishing host IP (Kali) |
| `dest_port` | Destination port (8080) |
| `http.method` | HTTP method (GET) |
| `user_agent` | Browser agent string |

Alert metadata fields were preserved to support simulation traceability.

---

### Email Delivery Context (SIM-001 Option A)

In the updated SIM-001 design, the phishing link was delivered through the lab
mail server (**Zimbra**) from an HR user to `it.helpdesk1`. While email delivery
provides realistic attack context, detection validation relies on endpoint
process creation evidence (Event ID 4688).

---

## 🧾 Evidence & Naming Convention Notes

This simulation follows the standardized evidence naming convention:

- Evidence IDs: `E-SIM001-###`
- Screenshot files: `sim001-evidence-###-<short-description>.png`

---

## 1. Baseline Chrome Execution (Pre-Phishing Validation)

This confirms that the endpoint is correctly logging browser activity before
the phishing event.

```text
_time: 2026-01-30 01:10:44
host: WIN11-LAB
user: LAB\it.helpdesk1
EventCode: 4688
New_Process_Name: C:\Program Files\Google\Chrome\Application\chrome.exe
Parent_Process_Name: C:\Windows\explorer.exe
Process_Command_Line: "chrome.exe"
```

📌 **Evidence ID:** `E-SIM001-001`

📸 **Screenshot Reference (Optional):**  
- `sim001-evidence-001-baseline-chrome.png`

**Description:** Baseline Chrome execution confirms Event 4688 visibility.

---

## 2. Phishing URL Execution (Primary Detection Signal)

This event shows Chrome launched with the phishing URL in the command line.
This is the **core SIM-001 detection artifact**.

```text
_time: 2026-01-30 01:13:03
host: WIN11-LAB
user: LAB\it.helpdesk1
EventCode: 4688
New_Process_Name: C:\Program Files\Google\Chrome\Application\chrome.exe
Parent_Process_Name: C:\Windows\explorer.exe
Process_Command_Line: "chrome.exe http://<KALI_IP>:8080"
```

📌 **Evidence ID:** `E-SIM001-002`

📸 **Screenshot Reference:**  
- `sim001-evidence-002-splunk-url-detection.png`

**Description:** Chrome executed with phishing URL — authoritative endpoint signal.

---

## 3. User Workflow Context (Mail → Click Behavior)

This context shows browser activity in the same timeframe as user email
interaction, supporting a **user-driven phishing click** conclusion.

```text
_time: 2026-01-30 01:12:59
host: WIN11-LAB
user: LAB\it.helpdesk1
EventCode: 4688
New_Process_Name: C:\Program Files\Google\Chrome\Application\chrome.exe
Parent_Process_Name: C:\Windows\explorer.exe
Process_Command_Line: "chrome.exe http://<KALI_IP>:8080"
```

📌 **Evidence ID:** `E-SIM001-003`

📸 **Screenshot Reference:**  
- `sim001-evidence-003-splunk-parent-process.png`

**Description:** Browser launch aligned with mailbox interaction timeframe.

---

## 4. Optional Network Telemetry (Security Onion)

If Security Onion is active, HTTP traffic may be observed from the endpoint to
the Kali phishing host.

```text
event_type: http
src_ip: 10.x.x.25
dest_ip: <KALI_IP>
dest_port: 8080
http.method: GET
```

📌 **Evidence ID:** `E-SIM001-007`

📸 **Screenshot Reference (Optional):**  
- `sim001-evidence-006-network-http-confirmation.png`

**Description:** Supplemental network confirmation of HTTP request.

> Network telemetry is not required for SIM-001 detection validation.

---

## 5. Correlated Detection Event (SIEM)

This event shows the phishing execution was detected and tagged by the
correlation query used for alerting.

```text
_time: 2026-01-30 01:13:10
host: WIN11-LAB
user: LAB\it.helpdesk1
New_Process_Name: chrome.exe
Process_Command_Line: http://<KALI_IP>:8080
simulation_id: SIM-001
symbolic_id: LAB-SIM-001-PHISHING-ALERT
```

📌 **Evidence ID:** `E-SIM001-008`

📸 **Screenshot Reference:**  
- `sim001-evidence-004-splunk-correlation.png`
- `sim001-evidence-005-alert-fired.png`

**Description:** Correlation logic successfully identified phishing execution.

> Detection logic successfully correlated endpoint process execution.  
> Network telemetry, when present, provided supplemental confirmation.

---

## Timeline of Key Events

```text
01:12:55 – Phishing email delivered to it.helpdesk1 mailbox
01:13:03 – User opens email and clicks link
01:13:03 – Chrome executed with phishing URL argument [E-SIM001-002]
01:13:10 – SIEM alert triggered [E-SIM001-008]
```

---

## Final Validation Statement

SIM-001 successfully demonstrates:

- Phishing link delivery via email (context)  
- User-driven browser execution  
- URL artifact captured in endpoint telemetry  
- Detection via SIEM correlation  
- Alert generation  

**Endpoint process telemetry remains the authoritative detection source.**
