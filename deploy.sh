#!/bin/bash
# deploy.sh — Run this on a fresh Ubuntu 22.04 server
# Usage: bash deploy.sh YOUR_SERVER_IP
# Example: bash deploy.sh 129.154.60.22

set -e

SERVER_IP="${1:?Usage: bash deploy.sh YOUR_SERVER_IP}"
NIP_DOMAIN="${SERVER_IP}.nip.io"

echo "========================================="
echo " NexoPOS SaaS — Deploy to ${NIP_DOMAIN}"
echo "========================================="

# ── 1. Install Docker ──────────────────────────────────────────────────────
if ! command -v docker &>/dev/null; then
    echo "→ Installing Docker..."
    curl -fsSL https://get.docker.com | sh
    sudo usermod -aG docker "$USER"
    echo "→ Docker installed."
else
    echo "→ Docker already installed."
fi

# ── 2. Install Docker Compose plugin ──────────────────────────────────────
if ! docker compose version &>/dev/null; then
    echo "→ Installing Docker Compose plugin..."
    sudo apt-get install -y docker-compose-plugin
fi

# ── 3. Clone / pull repo ───────────────────────────────────────────────────
REPO_DIR="/opt/nexopos-saas"
if [ ! -d "$REPO_DIR" ]; then
    echo "→ Cloning repository..."
    sudo git clone https://github.com/hhassanhafeez/nexopos-saas.git "$REPO_DIR"
    sudo chown -R "$USER":"$USER" "$REPO_DIR"
else
    echo "→ Pulling latest changes..."
    git -C "$REPO_DIR" pull origin saas-multitenancy
fi
cd "$REPO_DIR"

# ── 4. Switch to production branch ────────────────────────────────────────
git checkout saas-multitenancy

# ── 5. Create .env if missing ─────────────────────────────────────────────
if [ ! -f .env ]; then
    echo "→ Creating .env from .env.production template..."
    cp .env.production .env

    # Generate APP_KEY
    APP_KEY=$(docker run --rm php:8.2-cli php -r "echo 'base64:' . base64_encode(random_bytes(32));")
    sed -i "s|APP_KEY=.*|APP_KEY=${APP_KEY}|" .env

    # Substitute IP in all domain references
    sed -i "s|YOUR_SERVER_IP|${SERVER_IP}|g" .env

    # Generate random passwords
    DB_PASS=$(openssl rand -base64 16 | tr -dc 'a-zA-Z0-9' | head -c 20)
    DB_ROOT_PASS=$(openssl rand -base64 16 | tr -dc 'a-zA-Z0-9' | head -c 20)
    REDIS_PASS=$(openssl rand -base64 16 | tr -dc 'a-zA-Z0-9' | head -c 20)
    REVERB_ID=$(openssl rand -hex 8)
    REVERB_KEY=$(openssl rand -hex 16)
    REVERB_SECRET=$(openssl rand -hex 16)

    sed -i "s|DB_PASSWORD=.*|DB_PASSWORD=${DB_PASS}|" .env
    sed -i "s|DB_ROOT_PASSWORD=.*|DB_ROOT_PASSWORD=${DB_ROOT_PASS}|" .env
    sed -i "s|REDIS_PASSWORD=.*|REDIS_PASSWORD=${REDIS_PASS}|" .env
    sed -i "s|REVERB_APP_ID=.*|REVERB_APP_ID=${REVERB_ID}|" .env
    sed -i "s|REVERB_APP_KEY=.*|REVERB_APP_KEY=${REVERB_KEY}|" .env
    sed -i "s|REVERB_APP_SECRET=.*|REVERB_APP_SECRET=${REVERB_SECRET}|" .env

    echo ""
    echo "⚠️  IMPORTANT: Set your SUPER_ADMIN_PASSWORD in .env before continuing"
    echo "   nano .env  →  find SUPER_ADMIN_PASSWORD and set a strong value"
    echo ""
    read -rp "Press Enter after setting SUPER_ADMIN_PASSWORD..."
fi

# ── 6. Build and start containers ─────────────────────────────────────────
echo "→ Building Docker images (this takes a few minutes)..."
docker compose -f docker-compose.prod.yml build

echo "→ Starting containers..."
docker compose -f docker-compose.prod.yml up -d

# ── 7. Wait for MySQL to be ready ─────────────────────────────────────────
echo "→ Waiting for MySQL..."
sleep 15

# ── 8. Run central migrations ─────────────────────────────────────────────
echo "→ Running central database migrations..."
docker compose -f docker-compose.prod.yml exec app php artisan migrate --force

# ── 9. Open firewall ports (Oracle Cloud uses iptables) ───────────────────
echo "→ Opening firewall ports..."
sudo iptables -I INPUT -p tcp --dport 80 -j ACCEPT
sudo iptables -I INPUT -p tcp --dport 8080 -j ACCEPT
sudo apt-get install -y iptables-persistent
sudo netfilter-persistent save

# ── 10. Done ──────────────────────────────────────────────────────────────
echo ""
echo "========================================="
echo " ✅ Deployment complete!"
echo "========================================="
echo ""
echo " Super Admin:  http://${NIP_DOMAIN}/super-admin"
echo " Demo tenant:  http://demo.${NIP_DOMAIN}"
echo ""
echo " To add a tenant, go to super-admin and create one."
echo " New tenant URL will be: http://{subdomain}.${NIP_DOMAIN}"
echo ""
