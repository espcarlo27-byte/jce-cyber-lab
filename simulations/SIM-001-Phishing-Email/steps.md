# SIM-001 – Phishing Email (T1566.002) – Execution Steps

**Detection Focus:** Endpoint Execution via Browser Command Line  
**Primary Telemetry:** Windows Security Event ID 4688  
**MITRE ATT&CK:** T1566.002 – Spearphishing Link

---

## 1. Prerequisites

Ensure the following components are operational before starting SIM-001.

| Component | Required? | Purpose in SIM-001 |
|-----------|-----------|-------------------|
| **Windows 11 Endpoint** | ✅ | Generates authoritative telemetry (Event 4688, process command line) |
| **Splunk Enterprise** | ✅ | Ingests logs, validates detection, and triggers alerts |
| **Splunk Universal Forwarder** | ✅ | Sends Windows logs to Splunk |
| **Kali Linux** | Optional | Hosts phishing landing page used in URL |
| **Mail Server (Zimbra)** | Optional | Delivers phishing email for realistic attack path |
| **Security Onion** | Optional | Provides supplemental network telemetry (not required) |

> Detection for SIM-001 depends on **endpoint process telemetry**, not network logs.

---

# 2. Execution – Option A: Email-Based Phishing (Zimbra)

Use this path when validating phishing detection using **realistic internal email delivery**.

---

## 2.1 Prepare Phishing Landing Page (Kali Linux)

On Kali Linux:

```bash
mkdir -p ~/sim001-phish
cd ~/sim001-phish

cat > index.html << 'EOF'
<html>
  <body>
    <h2>HR Policy Update – Action Required</h2>
    <p>Please review the attached HR policy update.</p>
    <p>This is a benign phishing simulation for LAB-SIM-001.</p>
  </body>
</html>
EOF
```

**Start a lightweight web server:**
```bash
python3 -m http.server 8080
```

**Confirm accessibility:**
```text
http://<kali-ip>:8080/
```

**📸 Evidence:**  
sim001-A-evidence-001-phishing-page-hosted.png
