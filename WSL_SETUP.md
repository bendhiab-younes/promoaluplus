# WSL Setup for ALU Workshop Laravel

## Move Project to WSL

### Option 1: Clone Fresh (Recommended)

```bash
# In WSL terminal - go to home directory
cd ~

# Clone your repo (replace with your actual repo URL)
git clone https://github.com/your-username/alu-workshop-laravel.git

# Navigate into the project
cd alu-workshop-laravel

# Install dependencies
composer install

# Copy and configure environment
cp .env.example .env
php artisan key:generate

# Install frontend dependencies
npm install
```

### Option 2: Copy Existing Project

```bash
# In WSL terminal
cp -r /mnt/c/clone/Alu-workshop/alu-workshop-laravel ~/

cd ~/alu-workshop-laravel
composer install
npm install
```

---

## Keep GitHub Connected

### Option A: GitHub CLI (Recommended)

```bash
# Install gh if needed
sudo apt install gh

# Authenticate
gh auth login
# Follow prompts:
#   - GitHub.com
#   - HTTPS
#   - Yes, authenticate with GitHub credentials
#   - Login with browser
```

Push with:
```bash
git push origin main
```

### Option B: SSH

```bash
# Generate SSH key
ssh-keygen -t ed25519 -C "your-email@example.com"

# Copy public key to clipboard (WSL)
cat ~/.ssh/id_ed25519.pub
# Or: clip.exe < ~/.ssh/id_ed25519.pub  (if xclip installed)

# Add to GitHub:
# Settings → SSH and GPG keys → New SSH key → paste the key
```

Push with:
```bash
git remote set-url origin git@github.com:your-username/alu-workshop-laravel.git
git push -u origin main
```

---

## Configure Git (First Time)

```bash
git config --global user.name "Your Name"
git config --global user.email "your-email@example.com"
```

---

## Run the Project in WSL

```bash
cd ~/alu-workshop-laravel

# Start dev server
php artisan serve

# In another terminal - run Vite
npm run dev

# Or run both together
composer run dev
```

Access at: `http://localhost:8000`

---

## Quick Reference

| Task | Command |
|------|---------|
| Enter WSL | `wsl` |
| Check current directory | `pwd` |
| List files | `ls` |
| Navigate to project | `cd ~/alu-workshop-laravel` |
| Run migrations | `php artisan migrate` |
| Clear cache | `php artisan optimize:clear` |
| Push to GitHub | `git push` |