# 🗺 IAM Topology (Simplified)

This diagram illustrates how identities, role groups, application roles, and resource access groups interact within the Enterprise Security Operations Environment (JCE) Active Directory environment.

```mermaid
flowchart TB

  subgraph AD["Active Directory (Identity Authority)"]
    Users["Human Users\n(Employees | Executives | IT | Contractors)"]
    Svc["Service Accounts\n(svc_*)"]
    RBAC["RBAC Role Groups\n(RBAC_*)"]
    APP["Application Role Groups\n(APP_*)"]
    RES["Resource Access Groups\n(RES_*)"]
  end

  Users -->|Role Assignment| RBAC
  RBAC -->|Access Mapping| RES
  RBAC -->|Admin Role Mapping| APP
  Svc -->|Service Authentication| APP

  SIEM["Splunk SIEM\n(Detection & Monitoring)"]
  AD -->|Auth Logs & Group Changes| SIEM
```

---

## 🔎 Relationship Model

| Layer | Role |
|------|------|
| Users | Represent human identities with Title and Department context |
| RBAC Groups | Define **who the user is** (job function / department) |
| APP Groups | Define administrative roles inside applications |
| RES Groups | Define access to file shares and resources |
| Service Accounts | Represent system-to-system authentication |
| SIEM | Monitors authentication, group changes, and identity activity |

---

## 🧠 Design Insight

This structure enforces separation between:

- **Identity (Users)**
- **Role (RBAC)**
- **Access (RES / APP)**
- **Service Identity (svc_*)**
- **Monitoring (SIEM)**

This separation improves governance, simplifies auditing, and enhances identity-aware detections.

> RBAC groups define *who the user is*; RES groups define *what the user can access*; APP groups define *administrative roles*; service accounts represent *non-human authentication identities*.
