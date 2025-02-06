## Project Overview

WordPress Docker-based Setup
This project provides a streamlined way to set up a WordPress environment using Docker. It includes Apache, PHP, and WordPress for local development.

### Installation Guide

1. **Clone the Repository**

```shell
git clone https://github.com/your-username/your-repo-name.git
cd your-repo-name
```

2. **Set Up Docker Containers**

Run the following command to start the WordPress environment:
```shell
docker-compose up -d --build
```
This will build and run all required services, including WordPress, Apache, and PHP.

3. **Install Dependencies**

After starting the containers, install required dependencies:

```shell
npm install
composer install
```

4. **Compile JavaScript and SCSS Files**

If your project includes custom JS and SCSS files, compile them using:

```shell
npm run dev  # For development mode
npm run build  # For production mode
```

This will generate the necessary assets for your project.

5. **Access WordPress Locally**

Once the setup is complete, access WordPress in your browser:

```shell
http://localhost:8000
```

Default credentials are in wp-config.php.

## Syncing with a Template Repository

1. **Add Template Repository as a Remote:**

```shell
git remote add template git@github.com:pixelkey/docker-apache-wordpress.git
```

2. **Fetch the Changes:**

```shell
git fetch --all
```

3. **Merge the Changes:**

```shell
git merge template/[branch to merge] --allow-unrelated-histories
```

## Cherry-Pick Method:

1. **Cherry-Pick the Changes:**

First, identify the specific commit(s) in the template repository that contain the core setup files you want to sync.

```shell
git cherry-pick <commit-hash>
```

2. **Commit the Changes**

```shell
git commit -m "Sync core setup files from template repository"
```

Make sure to replace [URL of the template repo] and <commit-hash> with the actual URL and commit hash that you intend to use.

## Troubleshooting

If you encounter database connection issues, ensure that the "data" folder in the project root has the correct owner and group permissions. Run:

```shell
sudo chown -R root:root data/
```

If the database does not start, restart Docker:

```shell
docker-compose down && docker-compose up -d --build
```

## Running the Project Completely

1. **Start the Containers**

Ensure Docker is running and start the project:

```shell
docker-compose up -d
```

2. **Compile Assets**

Compile JavaScript and SCSS files:

```shell
npm run dev
```

3. **Run WordPress Locally**

Visit:
` http://localhost:8000 `

Log in using WordPress admin credentials.

# Updating the Project

To get the latest version of the project from GitHub:

```shell
cd /path-to-your-local-project/
git pull origin main
docker-compose down
docker-compose up -d --build
```

Then, clear your browser cache and refresh the WordPress admin panel.

# Contributing

Fork the repository.

Create a new feature branch:
` git checkout -b feature-new-functionality `

Make changes and commit:
` git commit -m "Added new feature" `

Push and create a Pull Request.