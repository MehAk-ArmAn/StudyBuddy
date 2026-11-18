# StudyBuddy Pack 11B — School OS Governance and Staff Board

**Scope:** Functional school-only coordination, governed staff roles, and PCL oversight. Not a replacement for existing school applications or invitation workflows.

## Routes and account separation

- `/schools/{organization}/workspace`: existing membership list and verified school invite flow; owner gains staff role editor.
- `/schools/{organization}/board`: original free Kanban-style board with four collapsible lanes and a detail side panel; task operations are persisted server-side.
- `/admin/control-room/schools`: existing PCL approval directory with a link to the read-only administrative organization overview.
- `/admin/control-room/schools/{organization}/overview`: PCL institutional record, approved staff roster, limited invitation statuses and selected audit events. It does **not** show the staff Kanban task descriptions, private student resources, DMs or chats.

## Server permissions

- Only active `studybuddy_organization_members` of an approved school can view its board.
- Owners and school administrators can create, edit, assign and archive staff tasks.
- Teachers can view their own school's staff board. They can change the lane of tasks assigned specifically to them, never title/description/assignee/priority.
- Only school owners can promote or demote verified teachers into the `school_admin` membership role; `owner` role is immutable through this UI. No cross-school role changes.
- PCL staff must carry the `schools.review` capability to access governance overview. This does not confer membership in the school's internal board.
- Tasks are archived instead of hard-deleted. All edits produce scoped security event records without including task body content.
- All data comes from real database tables; no mock task cards or fake school counts.

## Launch

The source installer hashes every file, makes a restoration backup, PHP-lints and compiles Blade, checks routes and runs the full PHPUnit suite **on the Mac**. It never migrates the active database automatically. After passing source install, make a new post-11A database backup before `php artisan migrate --force`, then browser-test with the synthetic dev.school-owner, dev.school-admin and dev.teacher accounts. Changes to preexisting school memberships are explicit and logged.

## Not shipped in Pack 11B

Group chat, direct messaging, student moderation, school calendar booking, full staffing analytics, actual React Bits Pro paid source, production billing, and private message surveillance. Schedule these as separate feature packs with safeguarding/legal review before public launch.
