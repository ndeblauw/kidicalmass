# Page registry (fixture)

Small, stable stand-in for `docs/wiki/design/30-skeleton/00-page-registry.md`.
Lets `BuildStatus`/`WikiParser` be tested on known content that never changes
when the real registry is edited.

| ID | Page | Slug | Type | UX | Conf | Wire | Assets | UI | Back | OK | Top gaps |
|------|----------|------------|--------|----|------|------|--------|----|------|----|------------------------|
| P-01 | **Home** | `/`        | static | 🟢 | 3    | 🟢   | ⚪      | 🟠 | 🔴   | 🟢 | Hero copy live [content] |
| P-02 | Kalender | `/events`  | model  | 🟠 | 2    | 🟠   | ⚪      | 🔴 | 🔴   | 🔴 | Data wiring [strategy]   |
