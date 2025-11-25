---
inclusion: always
---

# Implementation Rules for Laravel Modular System

## Core Principles

### 1. Always Run Diagnostics Checks
- Run `getDiagnostics` after creating or modifying PHP files
- Verify syntax errors, type errors, and other issues
- Fix all diagnostics before moving to next task

### 2. Keep It Simple - No Over-Engineering
- Think like a human, be straightforward
- Focus on deliverables, not perfect architecture
- Avoid unnecessary abstractions
- Use simple, readable code over clever solutions
- Industry standard patterns only when they add clear value

### 3. No Documentation Until Asked
- Do NOT create documentation files automatically
- Do NOT write README updates unless explicitly requested
- Do NOT create CHANGELOG entries unless asked
- Focus on working code first
- Documentation is a separate task

### 4. Complete All Functionalities
- Never leave implementations half-done
- If something cannot be completed, inform the user FIRST
- Get confirmation before skipping any feature
- All features must be fully functional, not just scaffolded

### 5. No Redundant Code
- Follow DRY (Don't Repeat Yourself)
- Use existing Laravel features instead of reinventing
- Follow PSR standards
- Keep methods focused and single-purpose
- Reuse existing package code where possible

## Implementation Checklist

For each feature:
- [ ] Implement the simplest solution that works
- [ ] Run diagnostics to verify no errors
- [ ] Test the feature works as expected
- [ ] Ensure it integrates with existing code
- [ ] No documentation unless requested

## What NOT to Do

❌ Create markdown documentation files  
❌ Write elaborate comments explaining obvious code  
❌ Add unnecessary design patterns  
❌ Create abstract classes when simple classes work  
❌ Add features not explicitly requested  
❌ Leave TODOs or incomplete implementations  

## What TO Do

✅ Write clean, simple, working code  
✅ Use Laravel conventions  
✅ Run diagnostics after changes  
✅ Complete each feature fully  
✅ Ask before skipping anything  
✅ Focus on the task at hand  

## Priority Order

1. Make it work (functionality)
2. Make it correct (diagnostics pass)
3. Make it simple (refactor if needed)
4. Document it (only when asked)
