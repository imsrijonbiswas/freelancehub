# FreelanceHub – Local Setup Guide

This repository contains the **FreelanceHub** project.  
Follow the instructions below carefully to set up the project correctly on your local machine.

---

## Prerequisites

Before starting, make sure you have installed:

- **XAMPP**
- **Git**
- A GitHub account with **owner access** to the organization repository

---

## Required Folder Structure (Very Important)

Apache **Document Root** must be:

C:\xampp\htdocs\FreelanceHub


⚠️ The folder name **must be exactly `FreelanceHub`**  
⚠️ The location **must be inside `htdocs`**

If this structure is not followed, the project may not work correctly.

---

## Step 1: Start Apache Server

1. Open **XAMPP Control Panel**
2. Click **Start** next to **Apache**
3. Make sure Apache status shows **Running**

---

## Step 2: Verify Apache Document Root 

By default, Apache uses:

C:/xampp/htdocs


Confirm this by checking the file:

C:\xampp\apache\conf\httpd.conf


Look for the following lines:

```apache
DocumentRoot "C:/xampp/htdocs"
<Directory "C:/xampp/htdocs">

No change is required if it matches the above.

## Step 3: Clone the Repository from the Organization
    Open Git Bash

    Navigate to the htdocs directory:

cd /c/xampp/htdocs

    Clone the repository:

git clone https://github.com/webLab-project/FreelanceHub.git

Since you are an owner of the organization, you already have permission to pull the repository.

## Step 4: Access the Project in Browser

After cloning and starting Apache, open your browser and go to:

http://localhost/FreelanceHub

You should now see the FreelanceHub project running.
Notes

    Do not rename the FreelanceHub folder

    Always start Apache before opening the project

    Make sure port 80 is free

    Keep the project inside htdocs