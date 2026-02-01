# Personal Finance with Laravel and Filament

A modern, robust Personal Finance application built to demonstrate the power of **Laravel 12** and **Filament 5**. This project serves as the codebase for the "Personal Finance with Laravel and Filament" course.

## 🚀 About the Project

This application is designed to help users track their income, expenses, and budget in a clean, dashboard-style interface. It features a fully responsive admin panel powered by Filament, providing an excellent starting point for learning how to build complex data-driven applications.

### Key Features

*   **📊 Interactive Dashboard**: Visual overview of your financial health with charts and statistics.
*   **💰 Transaction Management**: Easily record income and expenses.
*   **🏦 Multi-Account Support**: Manage multiple bank accounts and track balances automatically.
*   **📂 Categorization**: Organize transactions with custom categories.
*   **📉 Budgeting**: Set and monitor monthly budgets to stay on track.
*   **🔒 Secure & Private**: Built with multi-tenancy in mind—users only see their own data, secured by Global Scopes.
*   **🌍 User Preferences**: Customizable currency and locale settings per user.

## 🛠️ Technology Stack

*   **Framework**: [Laravel 12](https://laravel.com)
*   **Admin Panel**: [Filament 5](https://filamentphp.com)
*   **Database**: SQLite
*   **Testing**: Pest PHP

## 🎓 The Course

This repository contains the source code for the **Personal Finance with Laravel and Filament** course. Each branch represents a different lesson, taking you from a fresh installation to a complete, deployed application.

👉 **[View the Full Course](https://www.maiobarbero.dev/courses/personal-finance-with-laravel/)**

## 🏁 Getting Started

1.  **Clone the repository**
    ```bash
    git clone https://github.com/maiobarbero/laravel-filament-personal-finance.git
    cd laravel-filament-personal-finance
    ```

2.  **Install dependencies**
    ```bash
    composer install
    
3.  **Setup Environment**
    ```bash
    composer setup
    php artisan migrate:fresh --seed --seeder=BoostSeeder
    ```

4.  **Serve the App**
    ```bash
    php artisan serve
    ```

Visit `http://localhost:8000/admin` to log in.

## 📝 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
