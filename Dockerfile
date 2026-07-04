# syntax=docker/dockerfile:1
###############################################################################
# Production image — Badger monorepo (Credly digital-badge apps)
#
# Serves the 6 Credly-badge apps under one Apache:
#   /Badger /DUEBadger /Gradger /WolfPack /Buddy (HR) /SEFA (HR 2)
# plus a root index.html linking to each. They are the same Credly framework
# code (issue a badge to the Shibboleth-authenticated user).
#
# NOT included: cbpe/ (ORSEE 3.x) — legacy PHP-5 + MySQL (mysql_* API); it must
# run in its own container/image and is excluded via .dockerignore.
#
# Tech stack : PHP (mostly static HTML/SVG + a few PHP endpoints that call the
#              Credly REST API), no database. Apache so the per-app .htaccess
#              (Shibboleth <IfModule mod_shib> guards) are honored natively.
#
# Authentication:
#   * Shibboleth/SSO is enforced at the ingress / reverse proxy (Ansible-managed).
#     The .htaccess `<IfModule mod_shib>` blocks are inert without mod_shib (this
#     image), so the proxy is the gate; giveYourselfCredit.php reads the
#     $_SERVER Shibboleth attributes (mail/nickname/sn) the proxy supplies.
#   * Credly API credentials + the badging service account are NOT baked in.
#     They are read from runtime environment variables (see
#     .env.production.example) — CREDLY_API_KEY/SECRET/ACCESS_TOKEN/APP_ID and
#     CREDLY_ACCOUNT_EMAIL/PASSWORD. No secrets in the image.
#
# Runs non-root (www-data) on unprivileged port 8080.
###############################################################################
FROM php:8.3-apache

# --- Apache modules the apps' .htaccess use ---
RUN set -eux; \
    a2enmod rewrite headers

# --- Legacy code uses PHP short-open tags (<? ... ?>) ---
RUN set -eux; \
    printf 'short_open_tag = On\n' > /usr/local/etc/php/conf.d/zzz-app.ini

# --- Run as a non-root user on an unprivileged port (8080) ---
RUN set -eux; \
    sed -ri 's/^Listen 80$/Listen 8080/' /etc/apache2/ports.conf; \
    sed -ri 's/:80>/:8080>/' /etc/apache2/sites-available/000-default.conf

# --- Security hardening (suppress server tokens/signature, TRACE, ETag) ---
RUN set -eux; \
    { \
      echo 'ServerTokens Prod'; \
      echo 'ServerSignature Off'; \
      echo 'TraceEnable Off'; \
      echo 'FileETag None'; \
    } > /etc/apache2/conf-available/zzz-hardening.conf; \
    a2enconf zzz-hardening

# --- Docroot policy: parse .htaccess (AllowOverride All), no dir listing,
#     pass the Credly runtime env vars through to PHP, log to stdout/stderr ---
RUN set -eux; \
    { \
      echo '<Directory /var/www/html>'; \
      echo '    Options -Indexes +FollowSymLinks'; \
      echo '    AllowOverride All'; \
      echo '    Require all granted'; \
      echo '</Directory>'; \
      echo 'PassEnv CREDLY_API_DOMAIN CREDLY_API_KEY CREDLY_API_SECRET CREDLY_ACCESS_TOKEN CREDLY_APP_ID CREDLY_ACCOUNT_EMAIL CREDLY_ACCOUNT_PASSWORD'; \
      echo 'ErrorLog /dev/stderr'; \
      echo 'CustomLog /dev/stdout combined'; \
    } > /etc/apache2/conf-available/zzz-docroot.conf; \
    a2enconf zzz-docroot

# --- Application code. .dockerignore excludes .ddev/, .git/, .env*, Dockerfile,
#     the ORSEE app (cbpe/), the *.tar/*.tar.gz archives, the secret-bearing
#     backups (credly_bak.php, oldBadger/), docs and OS junk. ---
COPY --chown=www-data:www-data . /var/www/html/

# --- Permissions: read-only app tree owned by www-data; make the runtime dirs
#     Apache needs writable by the non-root user so it can start ---
RUN set -eux; \
    find /var/www/html -type d -exec chmod 0755 {} +; \
    find /var/www/html -type f -exec chmod 0644 {} +; \
    chown -R www-data:www-data /var/run/apache2 /var/log/apache2 /var/lock; \
    chmod -R g=u /var/run/apache2 /var/log/apache2 /var/lock

USER www-data
EXPOSE 8080

# php:apache base CMD = apache2-foreground
