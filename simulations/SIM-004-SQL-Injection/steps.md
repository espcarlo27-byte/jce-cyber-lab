# SIM-004 – SQL Injection (T1190) – Steps

## 1. Prerequisites

Before running this simulation, confirm the following components are online and healthy:

- **Kali Linux (Attacker – 10.0.0.30)**
   - Network connectivity to DVWA
   - Browser access available
   - Default gateway set to Security Onion
   - Local system time not required to match SIEM (IDS validation only)

- **DVWA Web Server (Ubuntu – 10.0.0.60)**
   - Apache and MySQL services running
   - DVWA database initialized
   - DVWA login page accessible
   - Credentials available (admin / password)

- **Security Onion (IDS – 10.0.0.20)**
   - Fully initialized (so-status healthy)
   - Suricata and Zeek running
   - Monitoring interface: ens192
   - Inline visibility confirmed

- **pfSense**
   - Routing functional
   - No blocking rules between Kali and DVWA

> ⚠️ Splunk is NOT required for SIM-004 execution.
> SIEM ingestion is intentionally not configured for this simulation.

Verify IDS Readiness Before Proceeding:
- On Security Onion, confirm system health:
```bash
sudo so-status
```
***If the system reports ready/healthy, proceed.***
- (Optional traffic sanity check):
```bash
sudo tcpdump -i ens192 -nn
```

---

## 2. Access DVWA and Authenticate

On Kali Linux:
   1. Open a web browser
   2. Navigate to:
   ```cpp
   http://10.0.0.60/DVWA
   ```
   3. Log in using:
   ```pgsql
   Username: admin
   Password: password
   ```

***📸 Take screenshot:*** `sim004-dvwa-login-page.png`

---

## 3. Verify DVWA Security Level (CRITICAL)
> ⚠️ DVWA security level may silently revert and invalidate testing.
1. Click DVWA Security
2. Set Security Level to:
   ```nginx
   Low
   ```
3. Click Submit
4. Log out of DVWA
5. Log back in to ensure persistence

***📸 Take screenshot:*** `sim004-dvwa-security-level-low.png`  
***If security level is not Low, STOP and correct before continuing.***

---

## 4. Navigate to SQL Injection Module
1. From the DVWA left navigation menu, click:
   ```sql
   SQL Injection
   ```
2. Confirm the User ID input field is visible

***📸 Take screenshot:*** `sim004-dvwa-sql-injection-page.png`

---

## 5. Baseline Application Behavior (Non-Malicious Input)
1. Enter the following value:
   ```text
   1
   ```
2. Click Submit
3. Observe that only a single user record is returned

This establishes normal application behavior.

***📸 Take screenshot:*** `sim004-dvwa-baseline-query.png`

---

## 6. Execute SQL Injection Payload
1. In the User ID field, enter:
   ```csharp
   1 OR 1=1#
   ```
2. Click Submit
3. Observe that multiple database records are returned

This confirms successful SQL injection execution.

***📸 Take screenshot:*** ` sim004-dvwa-sqli-success.png`

---

## 7. Validate Inline Traffic Visibility (IDS)

On Security Onion:
1. Confirm Kali → DVWA traffic is visible on the monitoring interface:
   ```spl
   sudo tcpdump -i ens192 -nn
   ```
2. Refresh the DVWA page once from Kali if needed
3. Observe HTTP traffic from 10.0.0.30 to 10.0.0.60

***📸 Optional screenshot:*** `sim004-securityonion-traffic-visible.png`

This confirms IDS visibility and correct network placement.

--- 

## 8. Validate SQL Injection Detection (PRIMARY)

In the Security Onion Web UI:
1. Navigate to:
   ```nginx
   Alerts
   ```
2. Set time range to Last 15 minutes
3. Locate the ET WEB_SERVER alert corresponding to the SQL injection attempt

***📸 Take screenshot:*** `sim004-suricata-alert.png`

---

## 9. Validate Alert Attribution (DETAIL VIEW)
1. Click the ET WEB_SERVER alert
2. Expand alert details
3. Confirm:
   - Source IP = 10.0.0.30 (Kali)
   - Destination IP = 10.0.0.60 (DVWA)
   - HTTP context present

***📸 Take screenshot:*** `sim004-suricata-alert-details.png`

---

## 10. Save Evidence
Add the following files to the screenshots/ directory:
- sim004-dvwa-login-page.png
- sim004-dvwa-security-level-low.png
- sim004-dvwa-baseline-query.png
- sim004-dvwa-sqli-success.png
- sim004-suricata-alert.png
- sim004-suricata-alert-details.png
- sim004-securityonion-traffic-visible.png (optional)

---

## 11. Mark Simulation Completion
Update the SIM-004 checklist in README.md:
- ✅ SQL injection executed successfully
- ✅ Baseline behavior observed
- ✅ Inline IDS visibility confirmed
- ✅ Suricata alert generated
- ✅ Attacker attribution validated
- ⚠️ SIEM ingestion intentionally excluded
- ✅ Screenshots captured
- ✅ Detection matrix updated
