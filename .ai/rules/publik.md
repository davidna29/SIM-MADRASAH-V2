---
paths:
  - 'app/Http/Controllers/Publik/**'
---

# Publik

## Filter public content and protect personal data
Every public query must enforce the publication state and any media approval; select only fields needed for the page. Never expose NIK, NIP, private contact details, student records, or operational inventory data. Staff appear only with `show_on_website=true`; gallery/video media requires recorded editor approval. Keep demo media out of public pages.
