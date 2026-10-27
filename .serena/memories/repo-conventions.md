# TryPost conventions

Read AGENTS.md and .ai/rules/index.md before edits, then all rules matching affected paths. Framework versions come from composer.lock and package-lock.json. Laravel 13 uses Pest 5; run explicit relevant test paths, never infer coverage from a build. Social publishing tests fake remote APIs while exercising real application/database flows. Frontend uses Inertia Vue 3, Wayfinder routes, shared date/search helpers, and Tabler icons. Preserve fork Docker deployment wiring when syncing upstream. Runtime secrets and generated caches do not belong in commits.
