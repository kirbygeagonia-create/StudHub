# StudHub — Role Hierarchy Correction
> Correction to `STUDHUB_FULL_IMPLEMENTATION_PLAN.md` based on SEAIT's actual authority structure.

---

## The Correction

The previous plan placed **Super Admin** above SAO as the highest authority.
This was wrong for SEAIT.

**At SEAIT:**
- The **School President** is the highest authority — but will NOT use the system
- The **SAO (Safety and Security Office)** is the highest authority *within* the system
- There is no internal role above SAO from the school's perspective

---

## Revised Role Hierarchy

```
SAO                      ← Highest authority. Manages everything. Owns all feedback.
  └── Dean               ← College-level. Manages programs under their college.
        └── Program Head ← Program-level. Manages students/moderators in their program.
              └── Moderator ← Assists Program Head within a program.
                    └── Student ← Regular user.
```

---

## What Happens to `super_admin`?

`super_admin` **stays in the codebase** but becomes a purely **technical/developer role** — invisible
to school staff and never assigned to anyone at SEAIT.

| Who holds it | Purpose | Visible in UI? |
|---|---|---|
| The developer / system owner only | Emergency fixes, seeding, database repair | No — hidden from all school-side panels |

Think of it like a root account on a Linux server. It exists, but no school user ever sees it or needs it.

**In practice:** The SAO account is the highest account any SEAIT staff will ever log into.
The `super_admin` account is only used by the developer if something breaks at the database level.

---

## Revised Enum

```php
enum UserRole: string
{
    case Student      = 'student';
    case Moderator    = 'moderator';
    case ProgramHead  = 'program_head';
    case Dean         = 'dean';
    case Sao          = 'sao';            // ← Highest school-side authority
    case SuperAdmin   = 'super_admin';    // ← Developer/system-owner only; hidden from school UI

    public function label(): string
    {
        return match ($this) {
            self::Student     => 'Student',
            self::Moderator   => 'Program Moderator',
            self::ProgramHead => 'Program Head',
            self::Dean        => 'College Dean',
            self::Sao         => 'Administrator',        // ← shown to school users simply as "Administrator"
            self::SuperAdmin  => 'System Administrator', // ← never shown in school-facing UI
        };
    }

    /**
     * Whether this role should be visible/assignable inside the school's admin panels.
     * super_admin is excluded — only assignable directly in the database or via artisan.
     */
    public function isSchoolRole(): bool
    {
        return $this !== self::SuperAdmin;
    }
}
```

---

## What Changes From the Original Plan

| Item | Before | After |
|------|--------|-------|
| Highest school authority | Super Admin | SAO |
| SAO capabilities | Campus-wide admin below Super Admin | Full top-level authority: can assign/remove any role, see all data, manage all feedback |
| Super Admin | Highest school authority | Developer-only, hidden from school UI |
| SAO can assign Dean role | No (Super Admin did this) | Yes — SAO assigns and removes Dean accounts |
| SAO can assign Program Head role | No | Yes — SAO (or Dean) assigns Program Heads |
| User role management screen | Super Admin panel | SAO panel |
| Who sees all feedback | Super Admin + SAO | SAO only (Super Admin is never active in school context) |

---

## SAO Panel — Updated Capabilities

SAO is now the school's system owner. The SAO panel becomes the full administration hub:

- **User Management** — assign/remove any role (Dean, Program Head, Moderator)
- **Feedback Inbox** — receives all escalated feedback from Deans; can see all feedback chain-wide
- **All Reports** — campus-wide moderation reports and open issues
- **Announcements** — post campus-wide notices shown on student dashboards
- **AUP / Help content management** — edit the Acceptable Use Policy and Help text
- **College & Program overview** — view all colleges, programs, enrollment counts
- **Audit log** — view all admin actions taken by any role
- **Suspension override** — can unsuspend any user suspended by any role below

---

## Dev Account Correction

Remove the `super_admin` dev account from `DevUsersSeeder` (don't seed it for school use).
The developer creates their own `super_admin` account manually via `artisan tinker` if ever needed.

| Email | Role | Note |
|-------|------|------|
| `test@seait.edu.ph` | Student | Dev test |
| `mod@seait.edu.ph` | Moderator | Dev test |
| `admin@seait.edu.ph` | Program Head | Dev test (renamed from old `admin`) |
| `dean.cict@seait.edu.ph` | Dean | Dev test |
| `sao@seait.edu.ph` | **SAO** | **Highest school-side account** |
| *(artisan tinker only)* | Super Admin | Developer only, never seeded |

---

## One-Line Summary

> SAO is the school's top authority. Super Admin is the developer's emergency backdoor.
> No school staff member ever touches a Super Admin account.

---

*Correction issued 2026-05-29. Supersedes Section 2 and Section 13 of STUDHUB_FULL_IMPLEMENTATION_PLAN.md.*
