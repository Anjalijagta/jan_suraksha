
# 🛡️ Jan Suraksha - Online Crime Reporting Portal

![Jan Suraksha Banner](/jan_suraksha/banner.png)


![Language](https://img.shields.io/badge/Language-PHP-blue?style=for-the-badge&logo=php&logoColor=white)
![Database](https://img.shields.io/badge/Database-MySQL-lightgrey?style=for-the-badge&logo=mysql&logoColor=white)
![Deployment](https://img.shields.io/badge/Deployment-Automated%20CI%2FCD-brightgreen?style=for-the-badge)
![Open Source](https://badges.frapsoft.com/os/v1/open-source.svg?v=103)
![License](https://img.shields.io/badge/License-MIT-green.svg)

## 📖 About The Project

**Jan Suraksha** is a digital initiative aimed at bridging the gap between citizens and law enforcement. It is an online crime reporting portal designed to make the process of reporting incidents safer, faster, and more transparent.

In many scenarios, citizens hesitate to report crimes due to fear or procedural complexities. Jan Suraksha aims to solve this by providing a user-friendly platform where complaints can be lodged securely.

### 🌟 Why this project?
* **Safety:** Encourages citizens to report crimes without fear.
* **Transparency:** Real-time tracking of complaint status.
* **Social Impact:** Directly contributes to a safer society using technology.

---

## 🔗 Live Demo
Check out the live project here:  
👉 **https://jan-suraksha.infinityfreeapp.com/**
---

## 🚀 Tech Stack

This project is built using the following technologies:

* **Frontend:** HTML5, CSS3, JavaScript, Bootstrap (Responsive Design)
* **Backend:** PHP (Core)
* **Database:** MySQL
* **Server Environment:** XAMPP / WAMP (Apache Server)
* **DevOps:** GitHub Actions (Automated Deployment)

---

## 📂 Project Architecture

Understanding the codebase is easy! Here is how our files are organized:

```text
Jan-Suraksha/
├── assets/              # Images, CSS, JS files
├── includes/            # Reusable PHP snippets (headers, footers)
├── admin/               # Admin dashboard files
├── config.php           # Database connection (Safe for Local XAMPP use)
├── schema.sql           # Database structure import file
├── index.php            # Landing page
├── login.php            # User authentication
└── README.md            # You are reading this!
````

-----

## ✨ Features

  * **User Registration/Login:** Secure authentication for citizens.
  * **File Complaint:** Easy-to-use form to report incidents with categories (Cybercrime, Theft, Harassment, etc.).
  * **Admin Panel:** Interface for authorities to view, manage, and update complaint statuses.
  * **Status Tracking:** Users can track the progress of their reported complaints.
  * **Responsive Design:** Works smoothly on mobile and desktop devices.

-----

## 📸 Screenshots

| Home Page | Complaint Form |
| :---: | :---: |
| <img width="1913" height="925" alt="Screenshot 2025-12-07 195411" src="https://github.com/user-attachments/assets/d5163e3c-ef33-43dc-bd2c-d5ace7925f67" />
 | <img width="1897" height="928" alt="Screenshot 2025-12-07 195453" src="https://github.com/user-attachments/assets/036f94c0-80fc-4a91-9d76-47a496f6b25c" /> |

-----

## 🛠️ Getting Started (Installation)

Follow these steps to set up the project locally on your machine.

### Prerequisites

You need a local server environment to run PHP and MySQL.

  * [XAMPP](https://www.apachefriends.org/index.html) (Recommended for Windows/Linux/Mac)
  * OR [WAMP](http://www.wampserver.com/en/) (For Windows)
  * A Code Editor (VS Code recommended)

### Step-by-Step Installation

1.  **Install XAMPP:** Download and install XAMPP. Start the **Apache** and **MySQL** modules from the XAMPP Control Panel.

2.  **Fork & Clone the Repository:**

      * Navigate to your XAMPP installation folder (usually `C:\xampp\htdocs`).
      * Open your terminal/git bash in the `htdocs` folder.
      * Clone the repo:

    <!-- end list -->

    ```bash
    git clone [https://github.com/your-username/jan_suraksha.git]
    ```

3.  **Database Configuration:**

      * Open your browser and go to `http://localhost/phpmyadmin`.
      * Create a new database named **`jan_suraksha`** (Make sure this name matches your code).
      * Click on the **Import** tab.
      * Choose the **`schema.sql`** file provided in this repository and click **Go**.

4.  **Connect Database:**

      * Go to the project folder -\> Open the `config.php` file.
      * Ensure the credentials match your local setup (Default XAMPP user is `root` with no password).

    <!-- end list -->

    ```php
    $db_host = 'localhost';
    $db_user = 'root';
    $db_pass = ''; 
    $db_name = 'jan_suraksha';
    ```

5.  **Run the Project:**

      * Open your browser.
      * Go to: `http://localhost/jan_suraksha/`

-----

## 🤝 Contribution Guidelines 

We welcome contributions from everyone!

### How to Contribute?

1.  **Find an Issue:** Look for issues tagged with  `good first issue`, `bug`, or `enhancement`.
2.  **Assign Yourself:** Comment on the issue asking to be assigned. **Do not start working until assigned.**
3.  **Fork the Repo:** Click the "Fork" button on the top right.
4.  **Create a Branch:**
    ```bash
    git checkout -b feature-name
    ```
5.  **Make Changes & Commit:**
      * Keep code clean and commented.
    <!-- end list -->
    ```bash
    git commit -m "Fixed bug in login page"
    ```
6.  **Push Changes:**
    ```bash
    git push origin feature-name
    ```
7.  **Create a Pull Request (PR):** Go to the original repository and click "Compare & pull request".

> **⚠️ IMPORTANT:** Do **NOT** change the `config.php` file in your PR unless necessary for local testing. Our CI/CD pipeline handles the live server connection automatically.

-----

## 🛡️ Project Admin

  * **Anjali Jagtap** - *Project Lead* - https://github.com/Anjalijagta

-----

## 📄 License

This project is licensed under the [MIT License](https://www.google.com/search?q=LICENSE).

-----


## Thanks for visiting Jan Suraksha\! 🇮🇳
## Making society safer, one line of code at a time.


```
```

