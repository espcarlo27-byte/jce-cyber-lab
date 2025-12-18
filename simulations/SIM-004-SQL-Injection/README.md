# SIM-004 – SQL Injection (T1190)

## 🎯 Goal

This simulation demonstrates how attackers exploit insecure web applications using **SQL Injection** to bypass authentication and gain unauthorized access. The objective is to validate that **network and web-layer telemetry** (IDS and SIEM) can detect SQL Injection activity against a vulnerable application.

From a defender’s perspective, this simulation focuses on **visibility and detection**, not exploitation depth.

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

A web application accepts user input (such as a login form) and sends it to a database. If the application does not properly validate that input, an attacker can insert SQL commands instead of normal text. The database then executes the attacker’s logic.

### Defender Explanation

The application dynamically constructs SQL queries using unsanitized user input. Logical operators, SQL keywords, and comment markers alter the intended query behavior, allowing authentication bypass or data disclosure. Defenders typically observe **malicious payloads in HTTP requests**, not the SQL query itself.

---

## ⚠️ Why SQL Injection Still Matters

* SQL Injection remains one of the most common web application attacks
* Legacy and internal applications often lack proper input validation
* Successful SQLi can result in:

  * Authentication bypass
  * Credential theft
  * Data exfiltration
  * Database destruction

SOC teams regularly investigate SQLi alerts originating from public-facing and internal applications.

---

## 🏗 Lab Components Used

| Role           | System            | Purpose                          |
| -------------- | ----------------- | -------------------------------- |
| Attacker       | Kali Linux        | Launch SQL Injection payloads    |
| Vulnerable App | Ubuntu + DVWA     | Intentionally vulnerable web app |
| Database       | MySQL (local)     | SQL backend for DVWA             |
| Firewall       | pfSense           | Traffic routing and visibility   |
| IDS            | Security Onion    | Detect SQLi payloads             |
| SIEM           | Splunk Enterprise | Log ingestion and correlation    |

DVWA and MySQL are hosted on the same Ubuntu VM to minimize complexity.

---

## 🌐 Network Placement

* DVWA resides on the internal lab network
* All traffic passes through pfSense
* Security Onion monitors HTTP traffic
* Splunk ingests IDS and web-related logs

Although the application is internal, the traffic patterns and detections mirror real-world public-facing attacks.

---

## 🔁 Attack Flow

1. Attacker accesses DVWA login page from Kali
2. Malicious SQL payload is submitted via login form
3. Application constructs unsafe SQL query
4. Database evaluates injected logic
5. Authentication is bypassed
6. HTTP and IDS logs are generated
7. Security Onion and Splunk detect and correlate activity

This flow mirrors how SOC analysts reconstruct web-based intrusion attempts.

---

## 🧪 SQL Injection Scenario Used

### Authentication Bypass (Primary Scenario)

* DVWA security level: **Low**
* Payload example:

```sql
' OR '1'='1
```

This payload forces the database condition to always evaluate as true, allowing login without valid credentials.

This method was selected because it:

* Is reliable
* Produces clear network artifacts
* Is easy to explain and validate

---

## 📊 Expected Telemetry

### Web Server Logs

* HTTP POST requests to login endpoint
* Suspicious parameters containing SQL keywords
* Successful responses following malformed input

### IDS (Suricata)

* SQL Injection signature alerts
* Detection of SQL keywords and encoded characters

### SIEM (Splunk)

* Correlation of source IP, payload patterns, and HTTP success
* Repeated attack attempts from same host

---

## ❌ Expected Limitations

| Missing Artifact        | Reason                           |
| ----------------------- | -------------------------------- |
| Windows Event Logs      | Linux-based application          |
| Sysmon                  | No endpoint malware              |
| Database audit logs     | Out of scope for this simulation |
| Application source code | Black-box detection model        |

These limitations are intentional and reflect real-world SOC visibility constraints.

---

## 🚨 Detection Focus

This simulation emphasizes:

* Identification of SQL keywords in HTTP payloads
* Detection of malformed parameters
* Correlation of malicious input with successful responses
* Understanding why alerts matter, not just that they fire

---

## ⚠️ Known Issues & Considerations

Common issues encountered during this simulation may include:

* DVWA database not initialized
* Incorrect DVWA security level configuration
* Apache or MySQL permission issues
* IDS rule noise requiring tuning

These issues are documented in the Issues & Resolutions log for learning and reuse.

---

## ✅ Why This Simulation Matters

This simulation demonstrates the ability to:

* Understand common web attack techniques
* Identify realistic detection points
* Analyze network and application-layer telemetry
* Document limitations and lessons learned
* Validate security tooling in a SOC-style workflow

This aligns with real-world detection engineering and SOC analyst responsibilities.
