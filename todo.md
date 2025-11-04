# Git SSH Migration

## Objective
Switch from HTTPS authentication to SSH authentication for the repository

## Current State
- Remote URL: `https://malikqattoum@github.com/malikqattoum/Jordaniain-University-Portal.git`
- Recent push: Successfully pushed commit bc9d87a..1ef0d5c to main branch
- Authentication: HTTPS with username embedded

## Target State
- Remote URL: `git@github.com:malikqattoum/Jordaniain-University-Portal.git`
- Authentication: SSH key-based authentication
- Benefits: No credential prompts, more secure, no credential cache issues

## Steps to Complete
- [ ] Update Git remote URL from HTTPS to SSH format
- [ ] Verify SSH key is set up for malikqattoum account
- [ ] Test SSH connection to GitHub
- [ ] Test push operation with SSH
- [ ] Verify successful push and repository access

## SSH URL Format
```
git@github.com:malikqattoum/Jordaniain-University-Portal.git
```

## Benefits of SSH
- No credential prompts required
- More secure authentication
- No credential cache management needed
- Works across all Git operations seamlessly
