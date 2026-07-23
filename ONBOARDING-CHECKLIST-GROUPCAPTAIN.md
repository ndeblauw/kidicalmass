# Group Captain Onboarding Checklist

> For chapter leaders (kapiteins/trekkers). Work through these tasks in order. Each chapter can do this independently.

---

## Step 1: Get Your Account

- [ ] Register on the site or ask a superadmin to create your account
- [ ] Ask a superadmin to assign you as **captain** of your chapter
- [ ] Log in and verify you land on your chapter page
- [ ] Navigate to `/admin` and verify the Blue Admin panel loads
- [ ] You should see only your chapter's content (not other chapters)

---

## Step 2: Chapter Page Setup

- [ ] Go to **Chapters** in admin > click on your chapter
- [ ] Verify the chapter name, zip code, and start date are correct
- [ ] Upload a **main image** (hero photo, ~1200x630px recommended)
- [ ] Add **gallery images** (multiple photos showing your chapter in action)
- [ ] Save and verify on the public site at `/nl/chapters/{your-chapter}`

---

## Step 3: Add Activities (Rides & Events)

For each upcoming ride or event:

- [ ] Go to **Activities** > Create New
- [ ] Fill in **title** (Dutch first)
- [ ] Set **activity type**: `kidicalmass` for rides, `meeting` for meetings, `workshop` for workshops
- [ ] Set **begin date and time**
- [ ] Add **location** (start point, e.g., "Parc de la Senne, Schaarbeek")
- [ ] Add **postal code** (4 digits, e.g., "1030")
- [ ] Optionally add **distance** (km) and **duration** (minutes)
- [ ] Optionally add **Komoot URL** for route details
- [ ] Optionally upload a **GPX file** for map rendering
- [ ] Optionally add a **commute link** (public transport info)
- [ ] Check the box to assign to **your chapter**
- [ ] Set **author** and **organizer** (from users in your chapter)
- [ ] Upload a **main image**
- [ ] Click **published** toggle when ready
- [ ] Verify it appears on `/nl/events` and your chapter page

**Repeat for each activity.**

---

## Step 4: Add News Articles

For each news article:

- [ ] Go to **News Articles** > Create New
- [ ] Fill in **title** (Dutch first)
- [ ] Write **content** using the rich text editor
- [ ] Check the box to assign to **your chapter**
- [ ] Set yourself as **author**
- [ ] Upload a **main image** (appears on the article card)
- [ ] Optionally add **gallery images**
- [ ] Set **published_at** date
- [ ] Click **published** toggle when ready
- [ ] Verify it appears on `/nl/about/news`

**Repeat for each article.**

---

## Step 5: Add Press Articles

For each press mention:

- [ ] Go to **Press Articles** > Create New
- [ ] Fill in **title** (Dutch)
- [ ] Add the **outlet** name (e.g., "BRUZZ", "De Morgen", "Het Laatste Nieuws")
- [ ] Add the **URL** to the original article
- [ ] Set **published_at** date
- [ ] Link to relevant **activities**, **articles**, or your **chapter** (checkboxes)
- [ ] Optionally upload a **PDF scan** of the article (max 15MB)
- [ ] Verify it appears on `/nl/about/press`

**Repeat for each press article.**

---

## Step 6: Add Partners

For each partner supporting your chapter:

- [ ] Go to **Partners** > Create New
- [ ] Add **name** and **URL**
- [ ] Write **description** (Dutch first)
- [ ] Set **category**: `institutioneel`, `bondgenoot`, or `operationeel`
- [ ] Check the box to assign to **your chapter**
- [ ] Upload their **logo** (square format works best)
- [ ] Toggle **show_logo** and **visible** as needed
- [ ] Verify it appears on `/nl/about/partners`

**Repeat for each partner.**

---

## Step 7: Upload Photo Galleries

- [ ] Go to your chapter's **backstage** at `/backstage/{chapter-shortname}`
- [ ] Navigate to **fotos** (photos)
- [ ] For each past ride that needs photos:
  - [ ] Click into the ride's photo upload page
  - [ ] Upload a **main photo** (the ride's cover image, max 15MB)
  - [ ] Upload **gallery photos** (select multiple, max 15MB each)
  - [ ] Wait a moment for images to process
  - [ ] Verify photos appear in the gallery

**Repeat for each ride that needs photos.**

---

## Step 8: Add Members to Your Chapter

### Option A: Add existing users
- [ ] Go to **Chapters** > click on your chapter
- [ ] Use the **Group Member Manager** search to find users
- [ ] Use the radio buttons to assign roles:
  - **Interested** -- new members, no specific role yet
  - **Pink Vest** -- confirmed route marshals
  - **Captain** -- chapter leaders (use for 1-2 people only)

### Option B: Convert contact form submissions
- [ ] Go to **Contact Submissions** in admin
- [ ] Find the volunteer signup submission
- [ ] Click **Convert to User**
- [ ] Then use Group Member Manager to assign the correct role

### Public visibility
- [ ] Check that members you want on the public roster have **is_public = yes**
- [ ] Set **is_public = no** for members who want to stay private

---

## Step 9: Invite Pink Vests to the Backstage

- [ ] Make sure pink vests have user accounts (see Step 8)
- [ ] Send them the backstage link: `/backstage/{chapter-shortname}`
- [ ] Explain what they can do:
  - View upcoming rides on the agenda
  - Upload photos after rides
  - See the team roster
  - Access materials and resources

---

## Step 10: Verify Everything

- [ ] Visit your chapter page on the public site (`/nl/chapters/{chapter}`)
- [ ] Check that the main image and gallery display correctly
- [ ] Check that activities appear on the events calendar
- [ ] Check that news articles appear on the news page
- [ ] Check that press articles appear on the press page
- [ ] Check that partners appear on the partners page
- [ ] Check that the team roster shows members correctly
- [ ] Check that photo galleries display correctly

---

## Need Help?

- **Can't access admin?** Ask a superadmin to verify your role assignment
- **Content not appearing?** Check the `is_published` toggle and chapter assignment
- **Images not loading?** Wait for processing, or check file size (max 15MB)
- **Something broken?** Contact a superadmin for troubleshooting
