# Simulation 2 – DNS Tunneling (T1071.004)
## ⚠️ Issues & Resolutions

This document captures **real operational issues encountered during SIM-002**
and the structured methodology used to identify, resolve, and validate each one.
All issues documented below were encountered during execution and have since
been **fully resolved**.

---

## 🧾 Evidence & Naming Convention Notes

This simulation follows the standardized evidence naming convention:

- Evidence IDs: `E-SIM002-###`
- Screenshot files: `sim002-evidence-###-<short-description>.png`
- PCAP files (optional): `sim002-evidence-###-<short-description>.pcap`

Key evidence referenced throughout this I&R:
- `E-SIM002-001` – Zeek baseline DNS visibility (`dns.log`)
- `E-SIM002-002` – Hunt baseline DNS visibility (`event.dataset:"zeek.dns"`)
- `E-SIM002-003` – Zeek suspicious DNS activity (`dns.log`, high-entropy/length)
- `E-SIM002-004` – Hunt suspicious DNS pivots / anomaly validation
- `E-SIM002-006` → `E-SIM002-011` – Optional Wireshark supporting evidence
- `E-SIM002-012` → `E-SIM002-013` – Optional PCAP retention artifacts

---

### 🧩 Issue 1: DNS Telemetry Generated but Not Initially Visible in Hunt

**Description:**  
During early execution of the DNS tunneling simulation, DNS traffic was
successfully generated and confirmed at the Zeek sensor level. However, DNS
events did not initially appear in the Security Onion Hunt interface when queried.

**Impact:**  
- DNS tunneling activity appeared invisible in the UI  
- Initial validation relied on raw Zeek log inspection  
- Investigation progress was delayed  
- Detection appeared incomplete despite confirmed telemetry  

**Root Cause:**  
The issue was caused by **incorrect query methodology** in Security Onion 2.x:
- Zeek logs were ingested into ECS-compliant data streams  
- Free-text searches (e.g., `zeek.dns`) did not return results  
- ECS-aware KQL queries were required to surface telemetry  

**Resolution:**  
1. Confirmed Zeek DNS logs were being written at the sensor.  
2. Verified Elastic and SOC services were healthy.  
3. Identified the correct ECS dataset field for Zeek DNS logs.  
4. Queried DNS telemetry using:
   ```so
   event.dataset:"zeek.dns"
   ```
5. Successfully surfaced DNS events in Hunt.

**Validation:**  
DNS telemetry became fully visible and searchable in Security Onion Hunt using
ECS-aware KQL queries.  

**Evidence Reference:**  

- `E-SIM002-001`
   - `sim002-evidence-001-zeek-dns-baseline-log.png`
- `E-SIM002-002`
   - `sim002-evidence-002-hunt-zeek-dns-baseline.png`
     
**Lessons Learned:**  
> Visibility issues may stem from query methodology, not telemetry failure.

---

### 🧩 Issue 2: DNS Tunneling Traffic Blended Into Normal DNS Activity

**Description:**  
Initial DNS tunneling-style queries appeared syntactically valid and resembled
legitimate DNS traffic, making them difficult to distinguish using default views
or simple inspection.

**Impact:**  
- No immediate visual distinction between normal and suspicious DNS  
- Required deeper analysis to identify anomalies  
- Highlighted limitations of signature-based detection  

**Root Cause:**  
DNS tunneling techniques intentionally:
- Use valid DNS syntax  
- Avoid malformed packets  
- Blend into normal DNS workflows  
- Rely on subtle behavioral indicators  

Default views do not flag these patterns without contextual analysis.

**Resolution:**  
1. Established a baseline of normal DNS behavior.  
2. Analyzed DNS queries for:
- Elevated query length  
- Randomized subdomain structure  
- Repetition patterns  
3. Compared baseline traffic against simulated tunneling behavior.  

**Validation:**  
Suspicious DNS queries were clearly distinguishable when evaluated
behaviorally rather than syntactically.  

**Evidence Reference:**

- `E-SIM002-003`
   - `sim002-evidence-003-zeek-dns-suspicious-log.png`
- `E-SIM002-004`
   - `sim002-evidence-004-hunt-zeek-dns-suspicious.png`
     
**Lessons Learned:**  
> DNS tunneling detection requires behavioral analysis, not signature matching.

---

### 🧩 Issue 3: Detection Logic Depended on Understanding ECS Field Mapping

**Description:**  
Although DNS telemetry was indexed, meaningful detection required accurate
understanding of ECS field names and their relationships.

**Impact:**  
- Initial searches were incomplete  
- Field-level analysis was delayed  
- Detection confidence was reduced  

**Root Cause:**  
Security Onion 2.x relies on:
- ECS field normalization  
- Dataset-based indexing  
- Structured field analysis  

Without familiarity with ECS mappings, telemetry may appear incomplete.

**Resolution:**  
1. Identified relevant ECS fields, including:
- `dns.query.name`
- `dns.query.length`
- `dns.subdomain`
- `source.ip`
2. Updated Hunt queries and detection logic to reference ECS fields directly.  
3. Aligned queries with observed Zeek DNS telemetry.

**Validation:**  
Field-level analysis became reliable and repeatable, enabling confident detection.  

**Evidence Reference:**
- `E-SIM002-004` (Hunt pivots + ECS field-based validation)  

**Lessons Learned:**  
> Effective SOC analysis depends on understanding the underlying data model.

---

### 🧩 Issue 4: Detection Thresholds Were Not Initially Defined

**Description:**  
Early analysis lacked defined thresholds for distinguishing suspicious DNS
behavior from normal activity.

**Impact:**  
- DNS tunneling indicators appeared low confidence  
- No clear cutoff for abnormal query length  
- Detection relied on subjective analysis  

**Root Cause:**  
DNS tunneling detection requires **environment-specific thresholds**, which were
not initially established.

**Resolution:**  
1. Reviewed observed baseline DNS query lengths.  
2. Identified anomalous query lengths during tunneling simulation.  
3. Established a practical detection threshold:
- `dns.query.length >= 35`
4. Incorporated threshold into detection queries and alert logic.

**Validation:**  
Suspicious DNS queries consistently exceeded the defined threshold, confirming
the effectiveness of the heuristic.  

**Evidence Reference:**
- `E-SIM002-004` (threshold-supported Hunt results)
    
**Lessons Learned:**  
> Detection thresholds must be informed by real telemetry, not assumptions.

---

### 🧩 Issue 5: Correlation Was Required to Increase Detection Confidence

**Description:**  
Single DNS indicators alone were insufficient to confidently identify tunneling
activity.

**Impact:**  
- Individual events appeared low-risk  
- No single alert-worthy signal  
- Required multi-factor evaluation  

**Root Cause:**  
DNS tunneling behavior manifests through **patterns**, not isolated events:
- Length  
- Frequency  
- Repetition  
- Structure  

Without correlation, signals remain weak.

**Resolution:**  
1. Correlated multiple DNS indicators, including:
- Query length  
- Repetition  
- Frequency  
2. Evaluated DNS behavior over time rather than per event.  
3. Documented correlation logic for alert readiness.

**Validation:**  
When indicators were evaluated together, DNS tunneling behavior was identified
with high confidence.  

**Evidence Reference:**
- `E-SIM002-003` + `E-SIM002-004` (correlated behavioral indicators)
  
**Lessons Learned:**  
> High-confidence detections emerge from correlated weak signals.

---

## 🧠 Overall Takeaway

SIM-002 demonstrated that effective DNS tunneling detection requires:

- Proper sensor placement  
- ECS-aware telemetry analysis  
- Baseline establishment  
- Threshold definition  
- Behavioral correlation  

Each issue reflected **real SOC detection engineering challenges**, all of which
were successfully resolved through structured analysis and validation.

---

## 🛡 GRC Note (Control Impact)

These issues affected the lab’s Detect (DE.CM / DE.AE) capability for DNS-based threats.
Resolution restored consistent, audit-ready telemetry visibility and investigation capability.

- Impacted Control Area: Network Monitoring / Detection Engineering
- Control Status: Restored ✅
- Retest Required: Yes
- Retest Result: Pass ✅ (validated via Hunt + Zeek evidence)

---

## 🏁 Status

- [x] All issues resolved  
- [x] Telemetry validated  
- [x] xDetection logic finalized  
- [x] Evidence captured  
- [x] Simulation fully validated  

**SIM-002 Status:** ***✅ Resolved***
