# Final visual system and release closure — 2026-09-04

## Classification

READY FOR DEPLOYMENT APPROVAL. Deployment has NOT been performed.
This classification covers the scoped local visual-system changes, not a fresh production audit or a claim of universal WCAG compliance.

## Changes and preserved state

- Four independent Selected Works panels; no pooled technical category. Dynamic counts remain 9 / 1 / 4 / 3.
- Desktop expansion ratio 4 : 0.85 : 0.85 : 0.85. Vertical collapsed labels switch orientation instantly; no rotation animation.
- One 6-second row scheduler advances one loaded image at a time (900ms crossfade). Expanded/focused, offscreen, hidden-tab, touch/mobile and reduced-motion states stop scheduling. No independent per-panel intervals.
- Graphic Design artwork and UI/UX interface presentation retained. Web & Systems browser-bar-plus-canvas is 16:10. All AI & Data covers are raw 16:10; three columns from 1280px.
- Existing radius/caption/number baselines retained; keyboard panel focus is visible and uses the real link rather than a duplicate tab stop.
- Fixed real narrow-phone EN/ID heading overflow using a below-480px font clamp on Hero/Contact only. Copy, hierarchy, colors and tablet/desktop typography remain unchanged.
- Removed nested main landmarks in Archive/Detail. Added an accessible name to the mobile Archive back-home icon.
- Archive description now includes AI & data and feeds existing OG/Twitter metadata. Homepage already mentions applied AI/ML; its approved copy and structured-data architecture remain unchanged.
- Updated obsolete source/test/Admin explanations. Historical audit decisions are explicitly superseded in the two existing reports.
- No project/category/media/database writes, migrations, seeding, commit, push or remote access. Featured IDs/order 21,22,7,10,14 remain visible unchanged.
- No replacement/generated images. WebGIS detail media and Archive trend-chart cover remain unchanged.

## Verification

- php artisan test: 158 passed, 476 assertions.
- node --test tests/js/project-accordion.test.mjs: 3 passed. Tests execute the actual scheduler in a controlled DOM/timer harness; prove serialized rotation, pause conditions, and protection against unloaded images.
- npm run build: success. Optional fontaine fallback-optimization warning remains; no dependency changes made.
- public/build/.htaccess was missing initially, recreated after the FINAL build with the previously approved immutable-cache policy. It is gitignored/manual-upload.
- git diff --check: passed.
- Actual in-app browser viewport override worked (unlike the previous automation environment). Measured Homepage EN and Archive ID at 320,390,480,768,960,1024,1280,1440,1536px: no horizontal page overflow after the narrow-phone correction. Homepage ID was rechecked at 320/390px after correction; its wider layouts were checked earlier.
- Homepage: column below 1024px, row at 1024px+. Archive AI: 1/2/3 columns at mobile/tablet/1280px+ respectively. All measured Web browser canvases have width/height = 1.6.
- Visually inspected desktop expanded accordion at 1024/1440, mobile ID dark accordion at 390, AI chapter EN light/ID dark, Web chapter light. Seven technical covers loaded with nonzero intrinsic dimensions.
- EN/ID rendered category/ARIA text checked, Hero wording preserved. One main landmark confirmed after fix.
- Captured browser warn/error log list was empty during the checked session. This is not a guarantee for every route.
- Existing modal/Visual Deck/Admin implementations were not rewritten. Full manual assistive-technology/device-matrix QA and a new Lighthouse benchmark were not performed; no performance-score claim.

## Exact deployment manifest

Project root: C:/laragon/www/portfolio/
Destination paths below are relative to the existing production application root. No FTP profile or server settings were inspected/changed.

Upload these source files, preserving their paths:

- C:/laragon/www/portfolio/app/Http/Controllers/Admin/CategoryController.php → app/Http/Controllers/Admin/CategoryController.php
- C:/laragon/www/portfolio/lang/id.json → lang/id.json
- C:/laragon/www/portfolio/resources/css/app.css → resources/css/app.css
- C:/laragon/www/portfolio/resources/js/project-accordion.js → resources/js/project-accordion.js
- C:/laragon/www/portfolio/resources/views/admin/categories/index.blade.php → resources/views/admin/categories/index.blade.php
- C:/laragon/www/portfolio/resources/views/components/nav.blade.php → resources/views/components/nav.blade.php
- C:/laragon/www/portfolio/resources/views/portfolio/index.blade.php → resources/views/portfolio/index.blade.php
- C:/laragon/www/portfolio/resources/views/portfolio/projects.blade.php → resources/views/portfolio/projects.blade.php
- C:/laragon/www/portfolio/resources/views/portfolio/show.blade.php → resources/views/portfolio/show.blade.php

Upload the complete FINAL build listed below. Assets first, then fonts-manifest.json and manifest.json, then changed Blade templates to avoid a manifest/asset mismatch. Retain old hashed assets during rollout; no remote deletion is authorized by this manifest.

| Local path relative to project root = production-relative destination | Bytes | SHA-256 |
|---|---:|---|
| public/build/.htaccess | 591 | E46267E317AA5E511D784206423C29878E9656D6272A770FDD61B7673073A668 |
| public/build/fonts-manifest.json | 5742 | 66EDF17C93351FE01158BE7414A441A762F106417C74351881876B405B8B10CA |
| public/build/manifest.json | 3707 | FAF0E32852AC19E4B16C9701C04B89B47E05A2B0FA5D9682814D0182FEC4162F |
| public/build/assets/admin-BF8TMsBr.css | 118209 | 62973C6A1A47A3990322888DCC46B5EE1A208E3AA3D9E66D8BD40994E24731F8 |
| public/build/assets/admin-D4xZqTcJ.js | 3709 | 2F7329BA2014121590063957EC523D72C6E6EF5E807E78455F6D0769387F3DDA |
| public/build/assets/app-BnfpcFjr.css | 106238 | 5C2D1DD7B3489A5557D0B5E8AC3464D2C88CDB5D549C100E739D048F84581A85 |
| public/build/assets/app-v1FN6qH6.js | 1901 | B48ECAC06EC0389CAC5A5D9900F1B6555E5D79BAA1B09F1EF266549CE21D9A25 |
| public/build/assets/archive-nav-COvrR-oO.js | 651 | E1DAE11A2CCD7BC06DA5C5C41960C4C0C9E830EFE042D3F5BC1CCB77FEE865C5 |
| public/build/assets/experience-toggle-CLYwLntj.js | 417 | 93FA9DC6BE30EFD7C938B9FD6157DE8385A4559C942501B19BAD80A64028D6D2 |
| public/build/assets/fonts-C9MNnjVw.css | 2352 | 3846A9BB60BFAD92960EADCCCAF0B4E129D74B11D48791506C8D1A827A385CC8 |
| public/build/assets/gallery-reorder-2eBREV7X.js | 2262 | 5ED9717118BA185448EE87FE6E4E131F8CCEA49EF464C847B2FCC30E342C8F35 |
| public/build/assets/home-nav-scrollspy-Zzo0j9Zp.js | 712 | 4233B1299DC5F4BF0C31F75F343A218E199353573A88164AC7C254290466DC22 |
| public/build/assets/image-modal-DsBptIV5.js | 77078 | F140841CC70026D8D995579949FD3FC6AF06B96530AE9AE9EC2628D58E5447F4 |
| public/build/assets/instrument-sans-400-normal-D1W7dsQl.woff | 21240 | 797BD3EAA70EDBD4757650AB44DAD75FBFBFDF18BACEDB4B71022C04621D30AD |
| public/build/assets/instrument-sans-400-normal-DRC__1Mx.woff2 | 16860 | 9A91EFAA3599A6EF01B2F49E9EC670E3A2F39BAEEF09C73198F1052CF3BB0850 |
| public/build/assets/instrument-sans-500-normal-Dk9ku72i.woff2 | 17232 | 9D3547DA16D950D12A677C772A3FF9B69846E60E7DCD1B5EFD540993F7286532 |
| public/build/assets/instrument-sans-500-normal-Z6ESRlEs.woff | 21652 | BD6A8DC8B0DC0CCED0C32D3EDA93F0E0E82C58DDEFEA8B53B3307B0C48DA3089 |
| public/build/assets/instrument-sans-600-normal-B7fBEWYG.woff2 | 17408 | EFB770916EDA95A0E31D80CCCDE9651D4E8617010B21220E9F227C4820D675DA |
| public/build/assets/instrument-sans-600-normal-B9e8oLYv.woff | 21676 | 9B4CE1F2B26BE7CB087FD87D6F99A202136292EC820AF75BD38AB9E53895CF28 |
| public/build/assets/locale-sync-GWF35RuG.js | 309 | 7D8D5EFA269E5F11290A3DC943E8693D7B7C036C1C3851521B95477C8F9E4576 |
| public/build/assets/preferences-fab-C4FwR1A5.js | 1384 | E767485EF8AD699B4EC86937255C90F509D528B695103B4BE94D458116F61D8B |
| public/build/assets/project-accordion-zze8HXDA.js | 1488 | 644A9882DE34F951C12DA2FFA74ABA565027C64D3176D75E07532880CB26E4DE |
| public/build/assets/project-assistant-BPrl1l6Y.js | 21627 | 972D7795396DB78B27AC18FFD3AC594CD13B874423757ACFF2E3ADF75D0B2611 |
| public/build/assets/theme-Dv_nRN3I.js | 776 | 525E9D5130AFF50127C1FB649DE0E98B062098CDB52D0D98564CA44E96C876F6 |

Build file count: 24 (including both manifests and .htaccess).
No more rebuilds should be assumed after recording these hashes; any rebuild requires regenerating this list and restoring .htaccess again.

### Media dependencies, not newly generated uploads

public/storage is a hosting mirror/copy, not a reliable symlink. Preserve these existing files at their actual public paths:
- public/storage/projects/speech-emotion/upload-interface.jpg (20,412 bytes)
- public/storage/projects/webgis/trend-chart-raw.webp (27,582 bytes)
- public/storage/projects/vision-ai/recognition-scanner-raw.webp (33,256 bytes)

All three were loaded by the local Archive. No new media upload is required by this pass. Do not overwrite project originals, sync the whole storage directory, or replace the production database. If a future deployment preflight finds a missing mirror file, copy only its corresponding existing storage/app/public/projects/... asset after authorization.

### Do not deploy

Tests, audit/inventory Markdown, deployment/ scratch artifacts, .env, database files, vendor, node_modules, .git, storage/framework, storage/logs, or backups. No cache deletion/migration/server configuration action is included.

## Local-only changed documentation/tests

- PROJECT_MASTER_INVENTORY.md and FINAL_PORTFOLIO_QA.md: current-decision notices (ignored by Git).
- RELEASE_CLOSURE_MANIFEST.md: this report.
- tests/Feature/ArchiveTaxonomyTest.php
- tests/Unit/Services/ProjectCompletenessTest.php
- tests/js/project-accordion.test.mjs

Stop before production deployment. No additional redesign or feature work is proposed.

