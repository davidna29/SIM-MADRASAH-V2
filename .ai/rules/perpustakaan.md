---
paths:
  - 'app/Http/Controllers/Perpustakaan/**'
---

# Perpustakaan

## Perpustakaan module conventions
Routes use prefix `/perpustakaan` with name `perpustakaan.*`. Middleware: `role:super_admin|pustakawan|kepala_madrasah`. Kategori & Anggota routes MUST be defined BEFORE `{book}` wildcard routes. `available_qty` decrements on loan, increments on return. `return_date` + status `terlambat` set when return_date > due_date. Member names are snapshot from student/employee at creation time.
