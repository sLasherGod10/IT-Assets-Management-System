# IT Asset Management System

A comprehensive IT Asset Management System built with PHP, MySQL, and Bootstrap.

## Features

- User Authentication (Admin/Employee roles)
- Dashboard with asset overview and analytics
- Asset Management (CRUD operations)
- Asset Allocation and Tracking
- Reports Generation
- Responsive Design

## Requirements

- XAMPP (Apache, MySQL, PHP)
- Web Browser
- Internet connection (for CDN resources)

## Installation

1. Install XAMPP
2. Clone this repository to your htdocs folder
3. Import the database schema using phpMyAdmin
4. Configure database connection in `config/database.php`
5. Access the application through your web browser: `http://localhost/myapp`

## Default Login

- Admin: admin@itams.com / admin123
- Employee: employee@itams.com / employee123

Sure! Based on your project details and enhancements you've made recently, here's an **updated and more complete `README.md`** file for your **IT Asset Management System** project:

---

# 💻 IT Asset Management System

A dynamic, responsive, and feature-rich **IT Asset Management System** built using **PHP**, **MySQL**, **HTML/CSS/JS**, and **Bootstrap**. This system helps organizations manage, allocate, and track IT assets efficiently with insightful dashboards and reports.

---

## 🌟 Features

- 🔐 **User Authentication System**
  - Role-based login (Admin / Employee)
- 📊 **Dashboard Overview**
  - Motivational slideshow with carousel
  - Animated Chart.js graphs for asset insights
  - Stats on total, available, in-use, and maintenance assets
- 🧾 **Asset Management**
  - Add, view, edit, and delete assets
  - Asset categories and status tags
- 🔄 **Asset Allocation & Return**
  - Allocate assets to users
  - Return/remove assets from users
  - Recent allocations table
- 📈 **Reports Page**
  - Colorful, animated pie and bar charts
  - Asset/user allocation trends
  - Exportable reports (future enhancement idea)
- 📱 **Responsive UI**
  - Clean design with Bootstrap 5
  - Purple gradient theme and dark-themed tables
  - `Poppins` font and smooth animations

---

## 🛠️ Tech Stack

- **Frontend**: HTML, CSS, JavaScript, Bootstrap 5, Chart.js
- **Backend**: PHP (vanilla)
- **Database**: MySQL (phpMyAdmin via XAMPP)
- **Server**: Apache (XAMPP)

---

## ⚙️ Installation

1. 🔽 Install [XAMPP](https://www.apachefriends.org/)
2. 📁 Clone or download this repository into your `htdocs` folder:
   ```
   git clone https://github.com/yourusername/it-asset-management-system.git
   ```
3. 🛢️ Import the SQL file (`assets_db.sql`) into **phpMyAdmin**.
4. ⚙️ Configure the database connection in:
   ```
   /config/database.php
   ```
   Update the `DB_HOST`, `DB_USER`, `DB_PASS`, and `DB_NAME` as per your setup.
5. 🌐 Start Apache & MySQL via XAMPP, then visit:
   ```
   http://localhost/it-asset-management-system
   ```

---

## 🔐 Default Login Credentials

> You can use these for testing:

- **Admin**

  - Email: `admin@itams.com`
  - Password: `admin123`

- **Employee**
  - Email: `employee@itams.com`
  - Password: `employee123`

---

## 📂 Folder Structure

```
/it-asset-management-system
│
├── /assets/             # CSS, JS, Images
├── /config/             # Database connection
├── /includes/           # Header, footer, nav
├── /pages/              # Allocation, reports, users, dashboard
├── /auth/               # Login / signup logic
├── /sql/                # Database schema
└── index.php            # Landing page
```

---

## 💡 Future Enhancements

- Email notifications on allocation
- Asset audit history/logs
- Export reports as PDF/Excel
- Role permission management
- Asset depreciation tracking

---

## 🧠 Inspiration

> _"Organizing IT resources shouldn’t be a headache — this system makes tracking and management a breeze."_  
> Motivational quotes dynamically displayed on the dashboard add purpose and vision to the platform.

---



---

Let me know if you'd like me to tailor this for GitHub or add badges/shields for build, license, etc.
