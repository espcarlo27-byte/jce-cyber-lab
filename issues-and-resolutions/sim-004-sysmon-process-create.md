# SIM-004 – Sysmon Process Create – Issues & Resolutions

## Issue 1 – Baseline Process Noise

**Issue:**  
High-volume benign process creation events made initial detection tuning noisy.

**Resolution:**  
Focused detection logic on parent–child relationships, execution paths, and command-line context rather than process name alone.

**Overall Takeaway:**  
Baseline process telemetry is critical before building higher-fidelity detections.

**Status:**  
Closed – Baseline execution validated at endpoint. Alerting intentionally deferred.

