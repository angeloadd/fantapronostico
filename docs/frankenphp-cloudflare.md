# FrankenPHP + Cloudflare Origin CA

## Overview

Stack: Laravel app → FrankenPHP (Caddy-based) → Cloudflare orange cloud (proxied).

Cloudflare Origin CA is free, valid up to 15 years, and trusted by Cloudflare's edge. Browsers never see it directly — Cloudflare terminates public TLS at the edge and re-encrypts to the origin using this cert. Requires **SSL/TLS mode: Full (Strict)** in Cloudflare dashboard.

---

## Cloudflare Dashboard Steps

1. Go to **SSL/TLS → Origin Server → Create Certificate**
2. Choose RSA or ECDSA, set validity (15 years max)
3. Download:
   - `origin.pem` — certificate
   - `origin.key` — private key
4. Store them as Docker secrets or environment-injected files (never bake into image)

---

## Dockerfile

```dockerfile
FROM dunglas/frankenphp

# Copy app
COPY . /app

# Copy Caddy config
COPY Caddyfile /etc/caddy/Caddyfile

# Cert files are mounted at runtime, not baked in
# e.g. /run/secrets/origin.pem and /run/secrets/origin.key
```

---

## Caddyfile

```caddyfile
{
    admin off
}

yourdomain.com {
    tls /run/secrets/origin.pem /run/secrets/origin.key

    root * /app/public
    encode zstd br gzip

    php_server
}
```

> Replace `/run/secrets/origin.pem` with wherever you mount the cert (Docker secrets, bind mount, etc).

---

## docker-compose snippet

```yaml
services:
  app:
    build: .
    secrets:
      - origin_cert
      - origin_key
    environment:
      SERVER_NAME: "yourdomain.com"
    ports:
      - "443:443"
      - "80:80"

secrets:
  origin_cert:
    file: ./secrets/origin.pem
  origin_key:
    file: ./secrets/origin.key
```

---

## Notes

- DNS-01 / ACME not needed — Origin CA bypasses it entirely
- HTTP-01 challenge won't work behind orange cloud anyway
- FrankenPHP stock image does **not** include the Cloudflare DNS Caddy module — avoid that path unless necessary
- If you ever turn off orange cloud, browsers will get an untrusted cert warning (Origin CA is not publicly trusted)