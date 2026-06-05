# Fantapronostico

## Production Deployment

The production stack is **FrankenPHP** (Caddy-based PHP server) behind **Cloudflare** (orange cloud, Full Strict TLS). Deployments are triggered automatically by pushing to `main` via GitHub Actions over SSH.

---

### Architecture

```
Browser → Cloudflare edge (public TLS) → Hetzner server (Origin CA TLS) → FrankenPHP → Laravel
```

---

### One-time setup

#### 1. Cloudflare Origin Certificate

1. In Cloudflare dashboard go to **SSL/TLS → Origin Server → Create Certificate**
2. Choose RSA or ECDSA, set validity up to 15 years
3. Download the two files and place them on the server:
   ```
   /path/to/project/secrets/origin.pem
   /path/to/project/secrets/origin.key
   ```
4. In Cloudflare set **SSL/TLS mode to Full (Strict)**

> These are mounted into the container at `/run/secrets/origin_cert` and `/run/secrets/origin_key` — absolute paths inside the container filesystem, unrelated to the project directory.

---

#### 2. SSH deploy key

Generate a dedicated key on your local machine (no passphrase):

```bash
ssh-keygen -t ed25519 -C "github-actions-deploy" -f ~/.ssh/fantapronostico_deploy
```

Add the public key to the Hetzner server:

```bash
ssh-copy-id -i ~/.ssh/fantapronostico_deploy.pub your_user@your_server_ip
```

---

#### 3. GitHub Secrets

In the repo go to **Settings → Secrets and variables → Actions** and add:

| Secret | Value |
| --- | --- |
| `DEPLOY_HOST` | Hetzner server IP or hostname |
| `DEPLOY_USER` | SSH user on the server |
| `DEPLOY_SSH_KEY` | Full contents of `~/.ssh/fantapronostico_deploy` (private key) |
| `DEPLOY_PATH` | Absolute path to the project on the server (e.g. `/path/to/repo/fantapronostico`) |

---

#### 4. Server first-time setup

**GitHub deploy key**

The server needs read access to the repo to clone and pull. Generate a key on the server:

```bash
ssh-keygen -t ed25519 -C "hetzner-deploy" -f ~/.ssh/github_deploy
cat ~/.ssh/github_deploy.pub
```

Add the public key in GitHub: **repo → Settings → Deploy keys → Add deploy key** (read-only is enough).

Then add to `~/.ssh/config` on the server:

```
Host github.com
    IdentityFile ~/.ssh/github_deploy
```

**Clone and configure**

```bash
git clone git@github.com:your-org/fantapronostico.git /path/to/repo/fantapronostico
cd /path/to/repo/fantapronostico

# Place the production env file
cp .env.example .env
# Edit .env with production values, including SERVER_NAME=yourdomain.com

# Place the Cloudflare Origin certs
mkdir -p secrets
# copy origin.pem and origin.key into secrets/

# First boot
docker compose -f docker-compose.production.yaml up -d --build
docker compose -f docker-compose.production.yaml exec app php artisan key:generate --force
docker compose -f docker-compose.production.yaml exec app php artisan migrate --force
docker compose -f docker-compose.production.yaml exec app php artisan optimize
```

---

### Local machine SSH setup

Steps to configure a new computer for server access and Dozzle tunneling.

#### Personal server key

Generate a key for direct server access (separate from the GitHub Actions deploy key):

```bash
ssh-keygen -t ed25519 -C "fantapronostico-personal" -f ~/.ssh/fantapronostico_server
ssh-copy-id -i ~/.ssh/fantapronostico_server.pub your_user@your_server_ip
```

#### Add server to known_hosts

Scan and trust the server fingerprint so SSH doesn't prompt on first connect:

```bash
ssh-keyscan -H your_server_ip >> ~/.ssh/known_hosts
```

#### SSH config

Add this block to `~/.ssh/config` (create the file if it doesn't exist):

```
Host fantapronostico
    HostName your_server_ip
    User your_user
    IdentityFile ~/.ssh/fantapronostico_server
    LocalForward 8888 localhost:8888
    LocalForward 5433 localhost:5432
```

`LocalForward <local_port> localhost:<remote_port>` tells SSH to forward that local port through the tunnel to the given port on the server. Both Dozzle and PostgreSQL bind only to `127.0.0.1` on the server, so they are unreachable from the internet — the tunnel is the only way in.

While connected you can reach:
- Dozzle at `http://localhost:8888`
- PostgreSQL at `localhost:5433` (use your DB credentials from `.env`)

Connect with:

```bash
ssh fantapronostico
```

---

### Automatic deployments

Every push to `main` triggers the GitHub Actions deploy workflow which:

1. SSHs into the Hetzner server
2. Pulls latest code (`git pull origin main`)
3. Rebuilds the Docker image
4. Restarts containers with zero-orphan cleanup
5. Runs `php artisan migrate --force`
6. Runs `php artisan optimize`

Logs are available via **Dozzle** at `http://your_server_ip:8888` (protected by `DOZZLE_USERNAME` / `DOZZLE_PASSWORD` from `.env`).
