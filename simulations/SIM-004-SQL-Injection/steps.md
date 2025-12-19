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
