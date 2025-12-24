# Architecture Overview

This section documents the **network topology, log flow, and design decisions**
used in the JCE Cyber Lab.

The architecture is intentionally designed to mirror **real-world SOC
environments**, emphasizing:
- Behavioral detection over static assumptions
- Clear separation of infrastructure vs endpoints
- Reproducible detection engineering workflows

## 📐 Architecture Documents

- 🖧 **[Network Topology](network-topology.md)**  
  High-level network layout, system roles, and IP addressing strategy

- 🔄 **[Network & Log Flow](network-log-flow.md)**  
  Detailed traffic paths, log ingestion flows, and detection visibility

## 🧠 Design Philosophy

- Infrastructure systems use **static IPs** for stability
- Endpoints and attack hosts use **DHCP** for realism
- Detections rely on **telemetry and behavior**, not fixed addressing
- Network visibility is passive and non-intrusive

This mirrors modern SOC and detection engineering best practices.
