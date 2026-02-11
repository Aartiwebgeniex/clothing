# WordPress Project Deployment Setup

This repository contains a WordPress project with automated deployment to a live server.

## Setup Instructions

### 1. GitHub Secrets Configuration

To enable automatic deployment, you need to configure the following secrets in your GitHub repository:

1. Go to your GitHub repository settings
2. Click on "Secrets and variables" > "Actions"
3. Add the following secrets:

- `SSH_HOST`: Your live server's hostname or IP address
- `SSH_USERNAME`: Your SSH username for the live server
- `SSH_PRIVATE_KEY`: Your SSH private key (must have access to the live server)

### 2. Update Deployment Path

Edit the `.github/workflows/deploy.yml` file and update the path:

```yaml
script: |
  cd /path/to/your/live/wordpress
  git pull origin master
```

Replace `/path/to/your/live/wordpress` with the actual path to your WordPress installation on the live server.

### 3. Live Server Setup

On your live server, you need to:

1. **Initialize Git repository** (if not already done):
   ```bash
   cd /path/to/your/live/wordpress
   git init
   git remote add origin https://github.com/Aartiwebgeniex/clothing.git
   ```

2. **Set up SSH access** for the deployment user

3. **Ensure the deployment user has write permissions** to the WordPress directory

### 4. Deployment Process

Once configured, deployments will happen automatically:

1. Push changes to the `master` branch
2. GitHub Actions will trigger the deployment workflow
3. The workflow will SSH into your live server
4. Pull the latest changes from GitHub
5. Your live site will be updated

### 5. Manual Deployment

You can also deploy manually by running:
```bash
cd /path/to/your/live/wordpress
git pull origin master
```

## Notes

- The deployment only happens on pushes to the `master` branch
- The workflow uses SSH for secure deployment
- Make sure your live server has Git installed
- Consider setting up proper backup procedures before deployment