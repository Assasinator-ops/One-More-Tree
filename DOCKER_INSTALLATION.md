# Docker Installation Instructions

## Install Docker on Linux

Since your system uses a custom Ubuntu codename (zena), we'll use the official Docker installation script which handles all dependencies automatically.

### 1. Clean Up Any Broken Docker Repositories
```bash
sudo rm /etc/apt/sources.list.d/docker.list
```

### 2. Download and Run Official Docker Installation Script
```bash
cd "/home/nati/Documents/One More Tree WP"
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh
```

### 3. Add Your User to Docker Group (Required for Non-Sudo Usage)
```bash
sudo usermod -aG docker $USER
```
*Note: You'll need to log out and log back in for this to take effect.*

### 4. Verify Installation
```bash
docker --version
docker compose version
```

## Start WordPress Environment

Once Docker is installed, start the WordPress environment:

```bash
cd "/home/nati/Documents/One More Tree WP"
docker compose up -d
```

## Access WordPress

1. Wait 30-60 seconds for containers to initialize
2. Open browser: http://localhost:8888
3. Complete WordPress installation wizard
4. Access admin: http://localhost:8888/wp-admin

## Troubleshooting

### Permission Denied
If you get "permission denied" errors, either:
- Use `sudo` with docker commands
- Log out and log back in after adding your user to docker group

### Port Already in Use
If port 8888 is already in use, edit `docker-compose.yml` and change the port mapping:
```yaml
ports:
  - "8000:80"  # Change 8888 to another port
```

### Docker Won't Start
```bash
sudo systemctl start docker
sudo systemctl enable docker
```
