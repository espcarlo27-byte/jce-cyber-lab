# SIM-004 – SQL Injection (T1190)

## 🎯 Goal

This simulation demonstrates how attackers exploit insecure web applications using **SQL Injection** to bypass authentication and gain unauthorized access. The objective is to validate that **network and web-layer telemetry** (IDS) can detect SQL Injection activity against a vulnerable application.

From a defender’s perspective, this simulation focuses on **visibility and detection**, not exploitation depth.

> ⚠️ Note: SIEM correlation was evaluated and documented, but full IDS-to-SIEM ingestion was not implemented for this simulation.

---

## 🧩 MITRE ATT&CK Mapping

| Category  | Mapping                                       |
| --------- | --------------------------------------------- |
| Technique | **T1190 – Exploit Public-Facing Application** |
| Tactic    | Initial Access (TA0001)                       |
| Related   | Credential Access, Collection (contextual)    |

SQL Injection is a common real-world initial access vector against poorly secured or legacy web applications.

---

## 🧠 What Is SQL Injection (Layered Explanation)

### Simple Explanation

A web application accepts user input and sends it to a database. If that input is not properly validated, an attacker can inject SQL logic instead of normal data. The database executes the injected logic, potentially bypassing authentication or exposing data.

### Defender Explanation

The application dynamically constructs SQL queries using unsanitized user input. Logical operators and SQL comment markers alter query execution. Defenders typically observe **malicious payloads within HTTP requests**, rather than the SQL query itself, making network-layer visibility critical.

---

## ⚠️ Why SQL Injection Still Matters

* SQL Injection remains one of the most common web application attacks
* Legacy and internal applications often lack proper input validation
* Successful SQLi can result in:

  * Authentication bypass
  * Credential theft
  * Data exfiltration
  * Database destruction
  * Data disclosure
  * Credential harvesting
  * Backend database compromise

SOC teams regularly investigate SQLi alerts originating from public-facing and internal applications.

---

## 🏗 Lab Components Used

| Role           | System            | Purpose                          |
| -------------- | ----------------- | -------------------------------- |
| Attacker       | Kali Linux        | Launch SQL Injection payloads    |
| Vulnerable App | Ubuntu + DVWA     | Intentionally vulnerable web app |
| Database       | MySQL (local)     | SQL backend for DVWA             |
| Firewall       | pfSense           | Traffic routing and visibility   |
| IDS            | Security Onion    | Inline network-based detection   |
| SIEM           | Splunk Enterprise | Not integrated for this simulation    |

> DVWA and MySQL are hosted on the same Ubuntu VM to reduce application complexity.

---

## 🌐 Network Placement
- DVWA resides on the internal lab network
- Kali and DVWA share the same LAN segment
- Security Onion is placed inline by forcing Kali’s default gateway
- All SQL injection traffic traverses Security Onion’s monitoring interface (ens192)
- pfSense remains the upstream gateway

This design ensured deterministic IDS visibility without relying on port mirroring.

---

## 🔁 Attack Flow
1. Attacker accesses DVWA SQL Injection page from Kali
2. Malicious SQL payload is submitted via application input
3. DVWA constructs an unsafe SQL query
4. Backend database evaluates injected logic
5. Application returns multiple records (authentication bypass behavior)
6. HTTP traffic traverses inline Security Onion
7. Suricata generates a web policy alert
8. Detection is validated at the IDS layer

This flow mirrors how SOC analysts reconstruct web-based intrusion attempts.

---

## 🧪 SQL Injection Scenario Used

### Authentication Bypass (Primary Scenario)

* DVWA security level: **Low**
* Payload example:
```sql
1 OR 1=1#
```

This payload exploits numeric input handling and SQL comment behavior, resulting in multiple database records being returned.

> Earlier textbook payloads (' OR '1'='1) did not execute due to application context and security level state.

This method was selected because it:
- Worked reliably in the actual lab
- Altered application behavior visibly
- Generated observable network artifacts

---

## 📊 Observed Telemetry

### Web Application Behavior
- HTTP requests containing SQL logic operators
- Application returned multiple records after malformed input
- No application-layer error messages displayed

### IDS (Suricata)
- ET WEB_SERVER alert triggered
- Alert classified as generic web policy violation
- Source and destination IPs confirmed in alert details

### SIEM (Splunk)
- ❌ No Suricata events ingested
- Splunk Universal Forwarder not installed on Security Onion
- SIEM integration gap documented intentionally

---

## ❌ Expected Limitations

| Missing Artifact        | Reason                           |
| ----------------------- | -------------------------------- |
| Windows Event Logs      | Linux-based application          |
| Sysmon                  | No endpoint malware or Windows host              |
| Database audit logs     | Out of scope for this simulation |
| Application source code | Black-box detection model        |
| SIEM Correlation        | IDS-to-Siem Integration not configured by design |

These limitations are intentional and reflect real-world SOC visibility constraints.

---

## 🚨 Detection Focus

This simulation emphasizes:

* Identification of SQL-related logic in HTTP payloads
* Understanding generic web alerts as potential exploit indicators
* Validating IDS visibility through correct network placement
* Recognizing detection vs. ingestion boundaries

---

## ⚠️ Issues & Resolutions

Multiple real-world operational and architectural issues were encountered during SIM-004, including:
- DVWA security level reverting unexpectedly
- Initial lack of inline traffic visibility
- Ambiguity in alert classification
- Absence of IDS-to-SIEM forwarding
All issues were investigated, documented, and resolved or formally recorded.

👉 Full technical breakdown:  
[SIM-004 – Issues & Resolutions](../../issues-and-resolutions/sim-004-sql-injection.md)

---

## 🔍 Overall Takeaway

SIM-004 demonstrated that SQL injection detection depends heavily on network architecture, application context, and telemetry interpretation rather than exploit complexity. Even without SIEM integration, validated IDS detection provides meaningful security insight and mirrors real SOC investigative workflows.

---

## 🏁 Status

Simulation Status: ✅ Validated (IDS Layer)  

SQL injection execution and detection were successfully validated at the network intrusion detection layer. SIEM ingestion was intentionally excluded and documented as a known limitation.
