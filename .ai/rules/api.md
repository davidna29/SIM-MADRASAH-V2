---
paths:
  - 'app/Http/Controllers/Api/**'
---

# Api

## API v1 security and write contract
API v1 is stateless Sanctum token auth (no web session). Login rejects inactive accounts, must_change_password, and parent/student roles; scopes are role-derived, never client-escalated. Read endpoints require website:read and row-level policies; full student identity requires akademik:write. All POST writes require Idempotency-Key; API only creates drafts/archives/private records and never publishes. SPP nominal is computed server-side and external_reference is unique. Log successful writes to activity('api') with request/token/idempotency properties.
