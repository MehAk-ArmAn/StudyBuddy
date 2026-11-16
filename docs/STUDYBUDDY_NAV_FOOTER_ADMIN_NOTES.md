# StudyBuddy Navbar / Footer Admin Notes

This patch upgrades the hover effects, footer structure, and admin polish while keeping content admin-controlled.

## Admin-controlled sources

- `site_settings.site_name`
- `site_settings.logo_text`
- `site_settings.logo_image`
- `site_settings.site_tagline`
- `site_settings.footer_text` or `site_settings.brand_promise`
- `site_settings.footer_pill_one`
- `site_settings.footer_pill_two`
- `site_settings.footer_pill_three`
- `site_settings.contact_email` or `site_settings.support_email`
- `site_settings.creator_name`
- `site_settings.creator_url`
- `site_settings.instagram_url`, `youtube_url`, `tiktok_url`, `x_url`, `twitter_url`, `linkedin_url`
- `navigation_items` for the navbar
- `footer_items` / `footerGroups` for footer columns

Fallback values only appear when the admin tables are empty so the page never breaks.
