# Control Mapping – NIST CSF (Enterprise Security Operations Environment (JCE))

> Purpose: Map lab security controls and validations to NIST Cybersecurity Framework (CSF) functions and categories.

## Identify (ID)
| NIST CSF Category | Control Implementation | Evidence |
|------------------|------------------------|----------|
| ID.AM (Asset Management) | Asset inventory maintained for lab systems | `asset-inventory.md` |

## Protect (PR)
| NIST CSF Category | Control Implementation | Evidence |
|------------------|------------------------|----------|
| PR.AC (Access Control) | AD-based authentication + password policy; least privilege | AD policy screenshot; group membership export |
| PR.PT (Protective Tech) | pfSense firewall, segmentation, NAT | pfSense firewall rules screenshot |
| PR.IP (Policies/Procedures) | Formal policies maintained | `/policies/` folder |

## Detect (DE)
| NIST CSF Category | Control Implementation | Evidence |
|------------------|------------------------|----------|
| DE.CM (Continuous Monitoring) | Security Onion (Zeek/Suricata) + Splunk dashboards | Splunk dashboard + SO logs |
| DE.AE (Anomalies & Events) | Correlation searches + alerting for suspicious activity | Splunk alert screenshot + query |

## Respond (RS)
| NIST CSF Category | Control Implementation | Evidence |
|------------------|------------------------|----------|
| RS.RP (Response Planning) | IR policy + playbook structure in simulations | IR policy + SIM docs |
| RS.AN (Analysis) | SPL + PCAP/log investigation workflows | SIM logs + queries |

## Recover (RC)
| NIST CSF Category | Control Implementation | Evidence |
|------------------|------------------------|----------|
| RC.RP (Recovery Planning) | Backup approach and lab rebuild notes documented | Restore steps + VM snapshots notes |

