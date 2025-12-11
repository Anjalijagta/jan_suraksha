

# 🤝 Contributing to Jan Suraksha

First off, thank you for considering contributing to **Jan Suraksha**! We are excited to have you on board.


---

## 🚀 Getting Started

To work on this project, you need a local server environment (PHP & MySQL).

### 1. Prerequisites
* **XAMPP** (Recommended), WAMP, or MAMP.
* **Git** installed on your machine.
* A code editor (VS Code recommended).

### 2. Fork & Clone
1.  Fork this repository to your GitHub account.
2.  Clone it to your `htdocs` folder (e.g., `C:\xampp\htdocs\`):
    ```bash
    git clone [https://github.com/your-username/Jan-Suraksha.git](https://github.com/your-username/Jan-Suraksha.git)
    cd Jan-Suraksha
    ```

### 3. Setup Database
1.  Start **Apache** and **MySQL** in XAMPP.
2.  Go to `http://localhost/phpmyadmin`.
3.  Create a database named **`jan_suraksha`**.
4.  Import the `schema.sql` file located in the root directory.

### 4. Configure Connection
The `config.php` file is pre-set for XAMPP (`root` user, no password).
* **If you use default XAMPP settings:** You don't need to change anything.
* **If you have a password:** Change it locally to test, but **DO NOT commit this change**.

---

## ⚠️ The Golden Rule: `config.php`

**Please Read Carefully:**
Our project uses an automated CI/CD pipeline to deploy to the live server.
* **DO NOT** upload/commit your local database credentials (password/username) in `config.php`.
* If you changed `config.php` to make it work on your laptop, please **discard those specific changes** before pushing your code.
* **Why?** If you push your local config, it might overwrite the live server's connection file and crash the website.

---

## 🛠️ How to Contribute

### Step 1: Find an Issue
* Go to the **Issues** tab.
* Look for labels like `wocs`, `level 1`, `level 2`, or `level 3`.
* Comment on the issue: *"I would like to work on this issue. Please assign it to me."*
* **Wait for approval** from a Project Admin before you start coding.

### Step 2: Create a Branch
Always create a new branch for your work. Do not work on the `main` branch directly.
```bash
git checkout -b feature/your-feature-name
# Example: git checkout -b feature/dark-mode-login
````

### Step 3: Code & Commit

  * Make your changes.
  * Test your changes locally to ensure they work.
  * Commit your work with a clear message:

<!-- end list -->

```bash
git add .
git commit -m "Added validation to the login form"
```

### Step 4: Push & Pull Request

1.  Push your branch to your forked repository:
    ```bash
    git push origin feature/your-feature-name
    ```
2.  Go to the original **Jan Suraksha** repository.
3.  Click **"Compare & pull request"**.
4.  Fill in the template (describe what you did).
5.  Submit the PR\!



## 📞 Need Help?

If you are stuck, feel free to ask questions in the:

  * GitHub **Discussions** tab.
  * Comments section of the Issue you are working on.

Happy Coding\! 💻🛡️

```
