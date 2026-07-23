# Superadmin Onboarding Checklist

> For national coordinators. Complete these tasks before sending instructions to group captains.

---

## Before Starting: Self-Test (Week 1)

- [ ] Pick 1-2 pilot chapters (e.g., Schaarbeek, Elsene)
- [ ] Create a test captain account for yourself
- [ ] Log in as test captain and verify you land on the chapter page
- [ ] Navigate to `/admin` and verify the Blue Admin panel loads
- [ ] Walk through every captain task:
  - [ ] Edit chapter page (name, main image, gallery)
  - [ ] Create an activity (ride) with all fields
  - [ ] Create a news article
  - [ ] Create a press article
  - [ ] Add a partner
  - [ ] Upload photos via the backstage hub
  - [ ] Use the Group Member Manager to add members and assign roles
  - [ ] Verify the chapter page looks correct on the public site
- [ ] Create a test pink vest account
- [ ] Log in as pink vest and verify backstage hub access
- [ ] Walk through pink vest tasks:
  - [ ] Navigate all backstage pages
  - [ ] Upload photos for an activity
- [ ] Fix anything that's confusing or broken before proceeding

---

## Phase 0: Foundation (Week 1-2)

### Chapter Structure
- [ ] Log in as superadmin at `/admin`
- [ ] Go to **Chapters** and review the hierarchy (Belgium > Regions > Local chapters)
- [ ] Verify chapter names, zip codes, and start dates are correct
- [ ] Add/remove chapters if needed

### National Content
- [ ] Add **team members** (Teamleden) -- name, role, bio, photo, sort order
- [ ] Add **year stats** (Jaarcijfers) -- participants and volunteers per year
- [ ] Add **quotes** (Citaten) -- mission/vision page pull quotes with correct `slot` values
- [ ] Add **national partners** (Partners) -- leave group empty for national-level partners

### Press Archive
- [ ] Run `php artisan db:seed --class=PressArchiveSeeder` if not done yet
- [ ] Review imported press articles in **Press Articles**
- [ ] Verify titles, outlets, dates, and URLs are correct

### News Articles
- [ ] Review ~9 demo news articles in **News Articles**
- [ ] Verify titles and content in Dutch
- [ ] Check chapter assignments are correct
- [ ] Mark articles as published or draft
- [ ] Add main images where missing

### Superadmin Accounts
- [ ] Create superadmin accounts for colleagues:
  1. Create user via **Users** > Create New
  2. Set `superadmin = true` via tinker:
     ```bash
     php artisan tinker --execute 'App\Models\User::where("email", "colleague@example.com")->update(["superadmin" => true]);'
     ```

---

## Phase 1: Content Review (Week 2-3)

- [ ] Walk through every page of the public site:
  - [ ] Homepage (`/nl/`)
  - [ ] About > Mission (`/nl/about/mission`)
  - [ ] About > Vision (`/nl/about/vision`)
  - [ ] About > Organisation (`/nl/about/organisation`)
  - [ ] About > News (`/nl/about/news`)
  - [ ] About > Press (`/nl/about/press`)
  - [ ] About > Partners (`/nl/about/partners`)
  - [ ] Chapters index (`/nl/chapters`)
  - [ ] Events calendar (`/nl/events`)
  - [ ] Steun ons (`/nl/steun-ons`)
  - [ ] Contact (`/nl/contact`)
  - [ ] Volunteer page (`/nl/help-out`)
- [ ] Fix any text errors via the admin panel
- [ ] Upload missing images via FilePond
- [ ] Report structural issues to the development team

---

## Phase 2: Pilot Rollout (Week 3-4)

- [ ] Select 2-5 pilot groups (digitally skilled, active, responsive)
- [ ] Send them the **Captain Onboarding Checklist** (`ONBOARDING-CHECKLIST-GROUPCAPTAIN.md`)
- [ ] Set a feedback deadline (e.g., "let us know by Friday")
- [ ] Collect feedback:
  - [ ] Were instructions clear?
  - [ ] Any admin panel errors?
  - [ ] Missing or unnecessary steps?
  - [ ] How long did each task take?
- [ ] Fix issues found during pilot
- [ ] Update the checklist based on feedback
- [ ] Confirm no core issues remain before full rollout

---

## Phase 3-5: Ongoing Support (Week 4-10)

### User Management
- [ ] Create captain accounts for chapters that need help
- [ ] Assign captain roles via tinker or admin panel
- [ ] Convert contact form submissions to users when requested
- [ ] Troubleshoot access issues (use impersonation)

### Quality Control
- [ ] Review content as chapters publish it
- [ ] Check that activities appear on the correct chapter pages
- [ ] Verify photo galleries display correctly
- [ ] Monitor contact form submissions

### Full Rollout
- [ ] Send Captain Onboarding Checklist to all remaining groups
- [ ] Offer intro calls for less digital-savvy captains
- [ ] Be available for questions during the first 2 weeks after rollout
