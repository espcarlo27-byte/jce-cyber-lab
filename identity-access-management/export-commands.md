# 📤 IAM Evidence Exports (Reproducible)

## Executive Summary

This document defines reproducible export commands used to generate
**audit-friendly identity evidence** from Active Directory.

These exports support:

- IAM documentation accuracy (users, groups, memberships)
- Repeatable evidence generation (GRC-ready traceability)
- Change validation after IAM updates (RBAC, titles, departments)

---

## 🧠 Evidence Philosophy

The Enterprise Security Operations Environment (JCE) follows an enterprise-style approach:

- **Exports are authoritative** (pulled directly from AD)
- Documentation is updated only after evidence is refreshed
- RBAC and identity governance changes are validated via exports

---

## 📁 Output Location

```powershell
New-Item -ItemType Directory -Path "C:\AD_Exports" -Force
```

---

## 👤 Export: Users (with Title/Department + Group Membership)

```powershell
Get-ADUser -Filter * -Properties DisplayName, Title, Department, Enabled, MemberOf |
Select SamAccountName, DisplayName, Title, Department, Enabled,
@{Name="Groups";Expression={$_.MemberOf -join "; "}} |
Export-Csv "C:\AD_Exports\ad_users_FINAL.csv" -NoTypeInformation
```

---

## 👥 Export: Groups (scope + category + description)

```powershell
Get-ADGroup -Filter * -Properties Description, GroupScope, GroupCategory |
Select Name, Description, GroupScope, GroupCategory |
Export-Csv "C:\AD_Exports\ad_groups_FINAL.csv" -NoTypeInformation
```

---

## 🔗 Export: Group Membership Map (Group → Members)

```powershell
Get-ADGroup -Filter * | ForEach-Object {
 $group = $_.Name
 Get-ADGroupMember $_ | Select @{Name="Group";Expression={$group}}, Name, SamAccountName, ObjectClass
} | Export-Csv "C:\AD_Exports\group_membership_FINAL.csv" -NoTypeInformation
```

---

## 🔐 Optional Export: Privileged Group Membership (Tier 0 visibility)

```powershell
$PrivGroups = @(
 "Domain Admins",
 "Enterprise Admins",
 "Schema Admins",
 "Administrators",
 "Account Operators",
 "Server Operators",
 "Backup Operators",
 "Print Operators"
)

foreach ($g in $PrivGroups) {
  $grp = Get-ADGroup -Identity $g -ErrorAction SilentlyContinue
  if ($null -eq $grp) { continue }

  Get-ADGroupMember -Identity $g -Recursive -ErrorAction SilentlyContinue |
    Select-Object @{Name="Group";Expression={$g}}, Name, SamAccountName, ObjectClass
} | Export-Csv "C:\AD_Exports\privileged_group_membership.csv" -NoTypeInformation
```

---

## ✅ Validation Quick Checks

**Confirm Titles/Departments are populated for human accounts:**

```powershell
Get-ADUser -Filter * -Properties Title,Department |
Select SamAccountName, Title, Department |
Sort SamAccountName
```

**Confirm RBAC groups exist:**

```powershell
Get-ADGroup -Filter "Name -like 'RBAC_*' -or Name -like 'APP_*' -or Name -like 'RES_*'" |
Select Name |
Sort Name
```

> These exports provide repeatable IAM evidence to support SOC operations, GRC traceability, and detection engineering validation.
