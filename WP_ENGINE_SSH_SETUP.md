# WP Engine SSH Setup Guide

This guide explains how to get the SSH credentials needed for deployment from your WP Engine server.

## Getting SSH Details from WP Engine

### 1. SSH Host (Server Address)

WP Engine provides SSH access through their platform. The SSH host format is:
```
[instance-name].ssh.wpengine.net
```

Where `[instance-name]` is your specific WP Engine environment name.

**To find your instance name:**
1. Log into your WP Engine User Portal
2. Navigate to your site
3. Click on the specific environment (Production, Staging, etc.)
4. The instance name is displayed in the environment details

### 2. SSH Username

Your SSH username follows this format:
```
[instance-name]
```

Same as your instance name.

### 3. SSH Private Key

WP Engine uses SSH key authentication. You need to:

#### Option A: Use Existing SSH Key
If you already have an SSH key pair:
1. The public key should be added to your WP Engine account
2. Use your existing private key

#### Option B: Generate New SSH Key
1. Generate a new SSH key pair:
   ```bash
   ssh-keygen -t rsa -b 4096 -C "your-email@example.com"
   ```
2. Add the public key to WP Engine:
   - Go to WP Engine User Portal
   - Navigate to Account → SSH Keys
   - Add your public key (contents of `~/.ssh/id_rsa.pub`)

### 4. Adding SSH Key to WP Engine

1. **Log into WP Engine User Portal**
2. **Navigate to Account Settings**
   - Click on your profile/avatar in the top right
   - Select "Account" or "Account Settings"
3. **Find SSH Keys Section**
   - Look for "SSH Keys" or "Security" section
4. **Add Your Public Key**
   - Copy the contents of your public key file (`~/.ssh/id_rsa.pub`)
   - Paste it into the SSH key field
   - Save/Submit

### 5. Testing SSH Connection

Test your SSH connection:
```bash
ssh [instance-name]@[instance-name].ssh.wpengine.net
```

### 6. GitHub Secrets Configuration

Once you have these details, configure them in GitHub:

1. Go to your GitHub repository
2. Settings → Secrets and variables → Actions
3. Add these secrets:
   - `SSH_HOST`: `[instance-name].ssh.wpengine.net`
   - `SSH_USERNAME`: `[instance-name]`
   - `SSH_PRIVATE_KEY`: [Your private key content]

### 7. WordPress Path on WP Engine

The WordPress path on WP Engine is typically:
```
/htdocs/
```

So your deployment script should use:
```yaml
script: |
  cd /htdocs
  git pull origin master
```

### 8. Important Notes

- WP Engine environments are isolated, so ensure you're using the correct environment's SSH details
- SSH access might need to be enabled in your WP Engine plan
- Contact WP Engine support if you don't see SSH options in your account
- Always use secure SSH keys and never commit private keys to your repository

### 9. WP Engine Documentation

For the most up-to-date information, refer to WP Engine's official documentation:
- [WP Engine SSH Documentation](https://wpengine.com/support/ssh/)
- [WP Engine User Portal Guide](https://wpengine.com/support/user-portal/)

### 10. Troubleshooting

If you encounter issues:
1. Verify your SSH key is properly added to WP Engine
2. Check that SSH access is enabled for your account
3. Ensure you're using the correct instance name
4. Test SSH connection manually before setting up GitHub Actions
5. Contact WP Engine support for account-specific issues