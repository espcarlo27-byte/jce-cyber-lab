# Access Control Policy

## 1. Purpose
Define requirements for access control to ensure only authorized users can access systems and data within the JCE Cyber Lab.

## 2. Scope
Applies to:
- Windows endpoints and servers
- Linux servers
- SIEM/logging platforms
- Firewall and network devices

## 3. Policy Requirements
- All users must have unique accounts (no shared logins).
- Privileged accounts must be limited and used only for administrative tasks.
- Password policy must enforce:
  - Minimum length: 12+
  - Account lockout after repeated failed attempts
- Remote access must be monitored and logged.

## 4. Roles & Responsibilities
- Owner (JCE): implements access controls and reviews accounts periodically.

## 5. Evidence / Control Validation
- AD password policy screenshot
- AD group membership export
- Authentication logs

## 6. Review Frequency
Quarterly.

