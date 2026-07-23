# Kidical Mass Belgium - Website Onboarding Guide

> This guide walks superadmins, group captains, and pink vests through populating the new Kidical Mass Belgium website. The approach maximizes parallel work: each chapter can onboard independently while superadmins handle national content.

---

## Table of Contents

1. [Before You Begin: Superadmin Self-Test](#1-before-you-begin-superadmin-self-test)
2. [Understanding the User Roles](#2-understanding-the-user-roles)
3. [Phase 0: Superadmin Foundation](#3-phase-0-superadmin-foundation)
4. [Phase 1: Content Review](#4-phase-1-content-review)
5. [Phase 2: Pilot Group Validation](#5-phase-2-pilot-group-validation)
6. [Phase 3: Full Chapter Rollout](#6-phase-3-full-chapter-rollout)
7. [Phase 4: People Onboarding](#7-phase-4-people-onboarding)
8. [Phase 5: Pink Vest Activation](#8-phase-5-pink-vest-activation)
9. [Timeline & Gantt Chart](#9-timeline--gantt-chart)
10. [Troubleshooting](#10-troubleshooting)
11. [Quick Reference](#11-quick-reference)

---

## 1. Before You Begin: Superadmin Self-Test

**This is the most important chapter in this guide.** Before sending instructions to any group captain, superadmins should personally test the captain and pink vest experience themselves. This ensures the instructions are clear, the workflow makes sense, and you can answer questions confidently.

### 1.1 Pick 1-2 Pilot Chapters

Choose chapters that are:
- **Digitally skilled** -- captains who are comfortable with web forms
- **Active** -- chapters that already have some content or momentum
- **Small enough** -- to iterate quickly without overwhelming the system

Good pilot candidates: **Schaarbeek** (has demo data, most mature), **Elsene** or **Gent**.

### 1.2 Test the Captain Experience

1. **Create a test captain account** for yourself (or use the demo account `captain@kidi.be`)
2. Log in and verify you land on the chapter page
3. Navigate to `/admin` -- you should see the Blue Admin panel
4. Walk through **every task** a captain needs to do:
    - [ ] Edit your chapter page (name, main image, gallery)
    - [ ] Create an activity (ride) with all fields
    - [ ] Create a news article
    - [ ] Create a press article
    - [ ] Add a partner
    - [ ] Upload photos via the backstage hub
    - [ ] Use the Group Member Manager to add members and assign roles
    - [ ] Verify the chapter page looks correct on the public site

### 1.3 Test the Pink Vest Experience

1. **Create a test pink vest account** (or use the demo account `pinkvest@kidi.be`)
2. Log in and verify you land on the chapter page
3. Walk through **every task** a pink vest needs to do:
    - [ ] Navigate the backstage hub (overview, agenda, photos, team, materials)
    - [ ] Upload photos for an activity
    - [ ] Verify the photo gallery displays correctly

### 1.4 Fix What's Broken

If anything is confusing, missing, or broken:
- Fix it before sending instructions to real captains
- Update this guide with clarifications
- Note any "coming soon" features that need to be communicated

### 1.5 Then Roll Out to 2-5 Strong Groups

Once you've validated the flow:
1. Send the **Captain Onboarding Checklist** (see `ONBOARDING-CHECKLIST-GROUPCAPTAIN.md`) to 2-5 strong or digitally skilled groups
2. **Wait for their feedback** -- do they understand the instructions? Is anything unclear?
3. **Iterate on the instructions** based on their questions
4. Only when no core feedback comes from the pilot groups, proceed to Phase 3

### 1.6 Then Roll Out to All Groups

After pilot validation:
1. Send the checklist to all remaining groups
2. Offer a short intro call or walkthrough for less digital-savvy captains
3. Be available for questions during the first 2 weeks

---

## 2. Understanding the User Roles

The website has three active user roles. Each role builds on the one below it.

### Superadmin
- **Who:** 1-2 national coordinators
- **Access:** Full admin panel (`/admin`), all chapters, all content
- **Can do:** Everything. Create chapters, manage users, set roles, impersonate users for troubleshooting
- **Should focus on:** National content, user management, quality control

### Captain (Kapitein / Trekker)
- **Who:** 1-2 people per chapter (the chapter leaders)
- **Access:** Admin panel scoped to their chapter only, backstage hub
- **Can do:** Create/edit activities, articles, press articles; manage chapter members and their roles; upload images; manage partners
- **Should focus on:** Chapter content, member management, activity planning

### Pink Vest (Roze Hesje)
- **Who:** Volunteers who ride along as route marshals
- **Access:** Backstage hub for their chapter
- **Can do:** Upload photos for activities, view team roster, see agenda
- **Should focus on:** Photo uploads, staying informed

### How Roles Work

Roles are stored **per group** in the `group_user` pivot table. A user can be a captain in one group and a pink vest in another. The role hierarchy is:

```
superadmin (global boolean)
  └── captain (per-group, inherits pinkvest rights)
       └── pinkvest (per-group)
            └── null / interested (member with no role)
```

The `is_public` flag on the pivot controls whether a member appears on the public chapter roster.

---

## 3. Phase 0: Superadmin Foundation

**Timeline:** Week 1-2
**Who:** Superadmins only
**Goal:** Ensure the structural foundation is solid before chapters start onboarding

### 3.1 Verify the Chapter Structure

The seeder creates 26 chapters across 3 regions. Verify they match reality:

1. Log in as superadmin (`/admin`)
2. Navigate to **Chapters** in the sidebar
3. Review the hierarchy: Belgium > [Brussels Capital Region, Wallonia, Flanders] > local chapters
4. Check that chapter names, zip codes, and start dates are correct
5. If a chapter needs to be added or removed, do it now

### 3.2 Set Up National Content

These items live at the national level and are shared across all chapters:

| Content | Where in admin | Notes |
|---|---|---|
| **Team members** | Teamleden | National coordination team shown on "Hoe we werken" page. Add name, role, bio (Dutch first, French later), photo, sort order |
| **Year stats** | Jaarcijfers | Add rows for each year with participant and volunteer counts. These power the "Steun ons" page |
| **Quotes** | Citaten | Pull quotes for mission/vision pages. Each quote has a `slot` identifier (e.g., `mission`, `vision-1`, `vision-2`) |
| **National partners** | Partners | Partners without a chapter assignment (leave group empty) appear at the national level |

### 3.3 Import the Press Archive

A `PressArchiveSeeder` imports ~20 historic press articles from the old Wix site (2020-2025). If this hasn't been run yet:

```bash
php artisan db:seed --class=PressArchiveSeeder
```

After seeding, review them in **Press Articles** in the admin panel. Check that titles, outlets, dates, and URLs are correct.

### 3.4 Review Existing News Articles

The seeder creates ~9 demo news articles. Review them in **News Articles**:
- Verify titles and content in Dutch
- Check that articles are assigned to the correct chapters
- Mark articles as published or draft as needed
- Add main images where missing

### 3.5 Create Superadmin Accounts for Colleagues

If other national coordinators need superadmin access:

1. Go to **Users** in admin
2. Create the user account
3. In the database, set `superadmin = true` on their user record

Or use tinker:

```bash
php artisan tinker --execute 'App\Models\User::where("email", "colleague@example.com")->update(["superadmin" => true]);'
```

---

## 4. Phase 1: Content Review

**Timeline:** Week 2-3 (overlaps with Phase 0 completion)
**Who:** Superadmins
**Goal:** Review and polish all national-level content before chapters start building on it

### 4.1 Public Site Content Review

Walk through every page of the public site and verify:

- [ ] **Homepage** (`/nl/`) -- Hero text, stats, call-to-action links work
- [ ] **About > Mission** (`/nl/about/mission`) -- Text and quote are correct
- [ ] **About > Vision** (`/nl/about/vision`) -- Text and quotes are correct
- [ ] **About > Organisation** (`/nl/about/organisation`) -- Team members display correctly
- [ ] **About > News** (`/nl/about/news`) -- Articles list correctly
- [ ] **About > Press** (`/nl/about/press`) -- Press articles display with correct links
- [ ] **About > Partners** (`/nl/about/partners`) -- National partners show with logos
- [ ] **Chapters index** (`/nl/chapters`) -- All 26 chapters listed under correct regions
- [ ] **Events calendar** (`/nl/events`) -- Rides display with correct dates/locations
- [ ] **Steun ons** (`/nl/steun-ons`) -- Year stats show correctly
- [ ] **Contact** (`/nl/contact`) -- Form works, submissions land in admin
- [ ] **Volunteer page** (`/nl/help-out`) -- Links and text are correct

### 4.2 Fix Issues Found

For each issue found during review:
- **Text errors:** Edit directly in the admin panel (Activities, Articles, Quotes, etc.)
- **Missing images:** Upload via FilePond in the admin form
- **Structural issues:** Contact the development team

---

## 5. Phase 2: Pilot Group Validation

**Timeline:** Week 3-4
**Who:** 2-5 selected pilot group captains (superadmins support closely)
**Goal:** Validate the onboarding instructions with a small group before full rollout

### 5.1 Selecting Pilot Groups

Choose 2-5 groups that are:
- **Digitally skilled** -- captains comfortable with web admin interfaces
- **Active** -- chapters with existing content or momentum
- **Responsive** -- people who will give feedback quickly

### 5.2 Sending the Instructions

1. Send the pilot captains the **Captain Onboarding Checklist** (`ONBOARDING-CHECKLIST-GROUPCAPTAIN.md`)
2. Include a clear deadline for feedback (e.g., "Try these steps and let us know what's confusing by Friday")
3. Offer a quick 15-minute video call if they get stuck

### 5.3 Collecting Feedback

Ask pilot captains specifically:
- Were the instructions clear? What was confusing?
- Did the admin panel work as expected? Any errors?
- Were there any steps that felt unnecessary or were missing?
- How long did each task take?

### 5.4 Iterating

Based on pilot feedback:
- Fix any bugs or UX issues
- Clarify confusing instructions
- Add missing steps
- Remove unnecessary steps

**Do not proceed to full rollout until pilot feedback is incorporated and no core issues remain.**

---

## 6. Phase 3: Full Chapter Rollout

**Timeline:** Week 5-8
**Who:** All remaining group captains (superadmins support)
**Goal:** Each chapter populates its own content independently

After pilot validation, send the **Captain Onboarding Checklist** to all remaining groups. Each captain works through these tasks independently. Chapters can work in parallel -- there are no dependencies between them.

### 6.1 Captain Account Setup

Each captain needs:

1. **A user account** -- They register via the site or the superadmin creates it
2. **Captain role assignment** -- Superadmin assigns them to their chapter with the `captain` role via the admin panel (Users > edit > assign to group) or via tinker:

```bash
php artisan tinker --execute '
$user = App\Models\User::where("email", "captain@example.com")->first();
$group = App\Models\Group::where("shortname", "schaarbeek")->first();
$user->groups()->attach($group->id, ["role" => "captain"]);
'
```

3. **Login and verify access** -- After login, they should land on their chapter page. They should see the admin panel when navigating to `/admin`.

### 6.2 Chapter Page Setup

Each captain should complete these tasks for their chapter:

#### Step 1: Basic Chapter Info
- Go to **Chapters** in admin > edit your chapter
- Verify the chapter name, zip code, and start date
- Upload a **main image** (hero photo for the chapter page)
- Add **gallery images** (multiple photos showing the chapter in action)

#### Step 2: Activities (Rides and Events)
- Go to **Activities** in admin > Create New
- For each upcoming ride:
    - Fill in **title** (Dutch, French later)
    - Set **activity type**: `kidicalmass` for rides, `meeting` for meetings, `workshop` for workshops
    - Set **begin date and time**
    - Add **location** (start point)
    - Add **postal code** (for proximity calculations)
    - Optionally add **distance** (km) and **duration** (minutes)
    - Optionally add **Komoot URL** for route details
    - Optionally upload a **GPX file** for map rendering
    - Optionally add a **commute link** (public transport info)
    - Assign to your **chapter** (checkbox)
    - Set **author** and **organizer** (from users in your chapter)
    - Upload a **main image**
    - Mark as **published** when ready

#### Step 3: News Articles
- Go to **News Articles** in admin > Create New
- For each article:
    - Fill in **title** (Dutch first)
    - Write **content** using the rich text editor
    - Assign to your **chapter**
    - Set yourself as **author**
    - Upload a **main image** (appears on the article card)
    - Optionally add **gallery images**
    - Set **published_at** date
    - Mark as **published**

#### Step 4: Press Articles
- Go to **Press Articles** in admin > Create New
- For each press mention:
    - Fill in **title** (Dutch)
    - Add the **outlet** name (e.g., "BRUZZ", "De Morgen")
    - Add the **URL** to the article
    - Set **published_at** date
    - Link to relevant **activities**, **articles**, or your **chapter**
    - Optionally upload a **PDF scan** of the article

#### Step 5: Partners
- Go to **Partners** in admin > Create New
- For each partner supporting your chapter:
    - Add **name** and **URL**
    - Write **description** (Dutch first)
    - Set **category**: `institutioneel`, `bondgenoot`, or `operationeel`
    - Assign to your **chapter**
    - Upload their **logo**
    - Toggle **show_logo** and **visible** as needed

#### Step 6: Photo Galleries
- Go to your chapter's **backstage** (`/backstage/{chapter-shortname}`)
- Navigate to **fotos** (photos)
- For each past ride that needs photos:
    - Click into the ride's photo upload page
    - Upload a **main photo** (the ride's cover image)
    - Upload **gallery photos** (multiple at once, max 15MB each)

### 6.3 Content Guidelines

When creating content, follow these guidelines:

- **Language:** Write in Dutch first. French translations can come later (all fields have `_nl` and `_fr` variants)
- **Images:** Use high-quality photos. Minimum width ~800px. The system generates responsive variants automatically
- **Publishing:** Use the `is_published` toggle. Drafts are visible in admin but not on the public site
- **Chapter assignment:** Always assign activities and articles to your chapter so they appear on your chapter page
- **Author:** Always set yourself (or another user) as the author

---

## 7. Phase 4: People Onboarding

**Timeline:** Week 4-8 (starts after captains have their accounts)
**Who:** Captains manage their members; superadmins support
**Goal:** Get all volunteers into the system with correct roles

### 7.1 Adding Members to a Chapter

Captains can manage their chapter's members through two methods:

#### Method A: Via the Admin Panel (Recommended)
1. Go to **Chapters** in admin > click on your chapter
2. Use the **Group Member Manager** (Livewire component)
3. Search for existing users by name or email
4. Use the radio buttons to set roles:
    - **Interested** -- Just a member, no specific role
    - **Pink Vest** -- Route marshal volunteer
    - **Captain** -- Chapter leader (use sparingly)

#### Method B: Via Contact Form Conversion
1. When someone signs up via the volunteer form on your chapter page
2. Their submission lands in **Contact Submissions** in admin
3. Click **Convert to User** on the submission
4. This creates a user account and attaches them to your chapter with role `null` (interested)
5. Then use the Group Member Manager to assign the correct role

### 7.2 Role Assignment Strategy

For each person joining your chapter, decide their role:

| Role | When to assign | What they can do |
|---|---|---|
| **Interested** (`null`) | Default for new sign-ups | See backstage hub, limited access |
| **Pink Vest** (`pinkvest`) | Confirmed route marshals | Upload photos, see backstage hub fully |
| **Captain** (`captain`) | Chapter leaders/coordinators | Full admin access for the chapter |

**Tip:** Start everyone as `interested`. Promote to `pinkvest` once they've ridden at least once. Only promote to `captain` for the 1-2 core organizers.

### 7.3 Public Visibility

The `is_public` flag controls whether a member appears on the public chapter roster at `/nl/chapters/{chapter}`.

- **Default:** New members are public (`is_public = true`)
- **Opt-out:** Members can request to be hidden (set `is_public = false` via the admin panel)
- **Where to change it:** Users > edit > toggle the `is_public` checkbox in the group membership

### 7.4 Inviting People

There's no automated invite system yet. Current approach:

1. Share the **volunteer signup link**: `/nl/chapters/{chapter}/help-out` (or the chapter-specific volunteer page)
2. Or manually create accounts and assign roles via the admin panel
3. Send a personal email explaining how to log in and what the backstage hub offers

---

## 8. Phase 5: Pink Vest Activation

**Timeline:** Week 6-10
**Who:** Pink vests (captains guide them)
**Goal:** Volunteers learn to use the backstage hub and contribute photos

### 8.1 Pink Vest Onboarding Flow

Once pink vests have accounts:

1. **Send them the backstage link:** `/backstage/{chapter-shortname}`
2. **Walk them through the hub pages:**
    - **Overzicht** (overview) -- See upcoming rides, recent activity
    - **Aan de slag** (getting started) -- Learn what a pink vest does
    - **Agenda** -- See upcoming and past rides
    - **Fotos** -- Browse ride galleries
    - **Groep** -- See the team roster
    - **Materiaal** -- Access resources (charter, ride guide, playlist)

### 8.2 Photo Upload Workflow

After each ride:

1. Pink vests go to **Backstage > Fotos**
2. Select the ride they just completed
3. Upload their photos (main photo + gallery)
4. Captains can review and remove photos if needed

### 8.3 Ongoing Engagement

- Keep the **agenda** up to date with upcoming rides
- After each ride, ensure photos are uploaded within a few days
- New pink vests can sign up via the chapter's volunteer page
- Captains assign roles via the Group Member Manager

---

## 9. Timeline & Gantt Chart

### High-Level Timeline

```
         Week 1   Week 2   Week 3   Week 4   Week 5   Week 6   Week 7   Week 8   Week 9   Week 10
         ──────── ──────── ──────── ──────── ──────── ──────── ──────── ──────── ──────── ────────
PHASE 0  ████████████████████
Superadmin
Foundation

PHASE 1           ████████████████████
Content
Review

PHASE 2                     ████████████████████
Pilot
Groups

PHASE 3                               ████████████████████████████████████████
Full Chapter
Rollout

PHASE 4                     ████████████████████████████████████████████████████
People
Onboarding

PHASE 5                                         ████████████████████████████████████████████
Pink Vest
Activation
```

### What Happens When (Summary)

| Phase | Week 1 | Week 2 | Week 3 | Week 4 | Week 5 | Week 6 | Week 7 | Week 8 | Week 9 | Week 10 |
|---|---|---|---|---|---|---|---|---|---|---|
| **0: Foundation** | Setup | Setup | | | | | | | | |
| **1: Review** | | Review | Review | | | | | | | |
| **2: Pilot** | | | Pilot | Pilot | | | | | | |
| **3: Rollout** | | | | | Rollout | Rollout | Rollout | Rollout | | |
| **4: People** | | | | People | People | People | People | People | | |
| **5: Pink Vests** | | | | | | Activate | Activate | Activate | Activate | Activate |

### Parallel Work Streams

```
Superadmins:   [Foundation]──[Review]──[Support]──────────────────────────────────[QC]
                                  └──[Pilot validation]──[Iterate]──[Rollout support]──

Pilot groups:               [Test]──[Feedback]──
                                                         └──[Fixes]

All chapters:                          [Captain accounts]──[Chapter content]──[Published]
                                                              [Members]──[Roles]──

Pink vests:                                            [Accounts]──[Backstage]──[Photos]──
```

### Dependency Chain

```
Phase 0 (Foundation)
  │
  ├──> Phase 1 (Content Review) ──> Phase 2 (Pilot) ──> Phase 3 (Full Rollout)
  │                                                              │
  └──> Phase 4 (People Onboarding) ─────────────────────────────> Phase 5 (Pink Vests)
```

**Key insight:** Phase 4 (People) can start as soon as captains have accounts -- it does NOT depend on content being complete. Phase 5 (Pink Vests) requires both Phase 3 (content exists) and Phase 4 (members exist).

---

## 10. Troubleshooting

### Common Issues

| Problem | Cause | Solution |
|---|---|---|
| Captain can't see `/admin` | Role not assigned or not logged in | Verify `group_user` pivot has `role = 'captain'` for their user + group |
| Captain sees 404 on admin pages | `LocalGroupScope` filtering | Check they're assigned to the right group. Use impersonation to verify |
| Pink vest can't upload photos | Not assigned as pinkvest | Use Group Member Manager to set their role to `pinkvest` |
| Activity doesn't appear on chapter page | Not assigned to group | Edit the activity and check the chapter checkbox |
| Photos look blurry | Responsive image conversions | Wait for queue processing. Check `php artisan queue:work` is running |
| Contact form submission not showing | Honeypot triggered | Check the honeypot field was left empty (bot protection) |
| Can't upload images > 15MB | File size limit | Resize images before upload. Max is 15MB per file |
| User registered but can't access anything | No group assigned | Go to Users > edit > assign to group |

### Using Impersonation

Superadmins can impersonate any user to see what they see:

1. Go to **Users** in admin
2. Click the impersonate icon next to a user
3. You're now logged in as that user
4. To stop, click "Stop Impersonation" in the top bar

### Getting Help

- **Technical issues:** Contact the development team
- **Content questions:** Refer to this guide or the checklists
- **Access issues:** Use impersonation to diagnose, then fix via tinker or admin panel

---

## 11. Quick Reference

### Admin Panel URLs

| Page | URL |
|---|---|
| Admin dashboard | `/admin` |
| Activities CRUD | `/admin/activities` |
| News articles CRUD | `/admin/articles` |
| Press articles CRUD | `/admin/pressarticles` |
| Contact submissions | `/admin/contactforms` |
| Users management | `/admin/users` |
| Chapters management | `/admin/groups` |
| Year stats | `/admin/yearstats` |
| Partners | `/admin/partners` |
| Team members | `/admin/teammembers` |
| Quotes | `/admin/quotes` |

### Backstage Hub URLs

| Page | URL pattern |
|---|---|
| Backstage home | `/backstage/{chapter-shortname}` |
| Getting started | `/backstage/{chapter-shortname}/welkom` |
| Agenda | `/nl/chapters/{chapter}/roze-hesjes/agenda` |
| Photos | `/nl/chapters/{chapter}/roze-hesjes/fotos` |
| Team roster | `/nl/chapters/{chapter}/roze-hesjes/groep` |
| Materials | `/nl/chapters/{chapter}/roze-hesjes/materiaal` |

### Tinker Quick Commands

```bash
# Create a captain
php artisan tinker --execute '
$user = App\Models\User::where("email", "captain@example.com")->first();
$group = App\Models\Group::where("shortname", "schaarbeek")->first();
$user->groups()->attach($group->id, ["role" => "captain"]);
'

# Create a pink vest
php artisan tinker --execute '
$user = App\Models\User::where("email", "volunteer@example.com")->first();
$group = App\Models\Group::where("shortname", "schaarbeek")->first();
$user->groups()->attach($group->id, ["role" => "pinkvest"]);
'

# Make someone a superadmin
php artisan tinker --execute 'App\Models\User::where("email", "admin@example.com")->update(["superadmin" => true]);'

# Hide a member from public roster
php artisan tinker --execute '
$user = App\Models\User::where("email", "private@example.com")->first();
$group = App\Models\Group::where("shortname", "schaarbeek")->first();
$user->groups()->updateExistingPivot($group->id, ["is_public" => false]);
'

# List all members of a chapter with roles
php artisan tinker --execute '
$group = App\Models\Group::where("shortname", "schaarbeek")->first();
$group->users->each(fn($u) => print_r([$u->name, $u->email, $u->pivot->role]));
'
```
