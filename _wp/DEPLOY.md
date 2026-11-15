# Deploying the WordPress theme and plugin

Pushing to `main` deploys automatically when anything under `_wp/renewradar/` (theme) or `_wp/renewradar-core/` (plugin) changes. The workflow `.github/workflows/deploy-wp.yml` checks PHP syntax, copies both folders to Hostinger over SSH with rsync (only changed files are sent; files removed here are removed on the server), then clears the LiteSpeed page cache. Other pushes (static files, `_src`) do not deploy anything.

The plugin's `rr_migrate` runs on `init`, so a new plugin version takes effect on the next page load without reactivating it. Customizer settings are untouched.

Watch runs at GitHub, repo `renewradar-site`, Actions, "Deploy WordPress". A failed run leaves the live site as it was before that file copy.

## One-time setup

1. hPanel, Websites, renewradar.pixeldevcode.com, Advanced, SSH Access: enable SSH. Note the IP, port and username.
2. On this PC, create a key used only for this deploy:
   `ssh-keygen -t ed25519 -N "" -C "renewradar-site deploy" -f ~/.ssh/renewradar_deploy`
3. hPanel, SSH Access, SSH keys, Add SSH key: paste the contents of `~/.ssh/renewradar_deploy.pub`.
4. Store the private key as a repo secret (from the `renewradar-site` folder):
   `gh secret set HOSTINGER_SSH_KEY < ~/.ssh/renewradar_deploy`
5. Set the repo variables:
   - `gh variable set HOSTINGER_HOST --body "<IP>"`
   - `gh variable set HOSTINGER_PORT --body "<port>"` (Hostinger uses 65002)
   - `gh variable set HOSTINGER_USER --body "<username>"`
   - `gh variable set HOSTINGER_WP_PATH --body "<path>"` (the WordPress folder relative to the SSH home, usually `domains/renewradar.pixeldevcode.com/public_html`; it contains `wp-config.php`)
   - `ssh-keyscan -p <port> <IP> > kh.txt && gh variable set HOSTINGER_KNOWN_HOSTS < kh.txt && rm kh.txt`
6. Actions, Deploy WordPress, Run workflow, keep "dry run" ticked. Check that the log lists the expected files, then run it again with dry run unticked.

## Manual fallback

Build the zips with `/c/Windows/System32/tar.exe -a -cf` into `_wp/dist/` and upload them in wp-admin (Themes or Plugins, Add New, Upload, "Replace installed with uploaded").
