---
trigger: always_on
---

# Context Efficiency

## General
Minimize unnecessary context, file reads, tool calls, and token usage.

Do not scan the entire repository unless absolutely necessary.

Reuse information already obtained during the current task instead of
re-reading the same files or symbols.

## Serena

Use Serena as the preferred tool for understanding and navigating
the existing codebase.

Before reading an entire file:

1. Search for the relevant symbol using Serena.
2. Inspect the symbol definition.
3. Find references only when necessary.
4. Read only the relevant portions of code.

Prefer:
- symbol search
- reference search
- targeted code retrieval
- project memory

Avoid:
- reading entire large files unnecessarily
- recursively scanning directories
- repeatedly reading unchanged files
- loading unrelated modules into context

Use Serena when investigating:
- existing implementations
- functions
- classes
- services
- components
- API handlers
- dependencies between modules
- references and usages

## Context7

Use Context7 only when current or version-specific external
documentation is needed.

Examples:
- Next.js APIs
- React APIs
- Prisma APIs
- library configuration
- package upgrades
- deprecated APIs
- version-specific behavior

Do not use Context7 for simple code changes that can be understood
from the existing project.

Do not retrieve broad documentation when a targeted documentation
query is sufficient.

## Repository Exploration

Before exploring code:

1. Identify the likely module or package.
2. Search relevant symbols with Serena.
3. Inspect only the necessary implementation.
4. Expand the search only if required.

Ignore generated/dependency directories unless specifically needed:

- node_modules
- .next
- dist
- build
- coverage
- .git

## Editing

Before changing code:

1. Identify the relevant symbols.
2. Understand their references when necessary.
3. Read the minimum surrounding code required.
4. Make the smallest correct change.

Avoid unrelated refactoring.

## Validation

Use the smallest appropriate validation first.

Prefer:

1. targeted typecheck
2. targeted tests
3. lint
4. full build only when necessary

Do not repeatedly run expensive validation commands when the
relevant code has not changed.

## Simple Tasks

For simple/local changes, do not invoke Serena or Context7
unnecessarily.

Examples:
- changing text
- changing a label
- simple CSS adjustment
- obvious configuration value
- known one-line change

Perform the smallest appropriate operation.

## Goal

Optimize for:

- minimum necessary context
- minimum unnecessary token usage
- minimum redundant tool calls
- accurate code understanding
- targeted changes
- appropriate validation