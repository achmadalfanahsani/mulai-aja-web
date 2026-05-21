# MulaiAja — Project Instructions

MulaiAja is a Computer Based Test (CBT) platform built with Laravel 13 and the Codebase Bootstrap 5 template. It provides a robust system for managing question packages and conducting online examinations.

## Project Overview

- **Core Purpose:** To manage exam packages (soal) and allow students to take tests online.
- **Target Audience:** Teachers/Administrators (management) and Students (exam takers).
- **Primary Stack:**
  - **Framework:** Laravel 13 (PHP 8.3+)
  - **Frontend:** Codebase Bootstrap 5 with Vite for asset management.
  - **Database:** MySQL (relational schema for questions, options, attempts, and responses).
  - **Auth:** Custom mock authentication system for rapid development and testing.

## Key Architecture & Modules

### 1. Question Management (`app/Models/QuestionPackage.php`, `app/Models/Question.php`)
- **Question Packages:** Containers for exams. Includes metadata like duration, passing score, and settings for shuffling.
- **Questions:** Nested within packages. Supports text, images, and explanations.
- **Options:** Multiple-choice options linked to questions.

### 2. Examination Engine (`app/Http/Controllers/ExamController.php`)
- **Attempts:** Tracks a student's progress through an exam package.
- **Responses:** Individual answers saved per question during an attempt.
- **Auto-save:** Responses are saved asynchronously via the `exams.save-response` route.
- **Results:** Scoring logic that calculates totals and identifies correct/incorrect answers.

### 3. UI/UX (`resources/views/layouts/app.blade.php`)
- Uses the **Codebase** admin template.
- Layouts are modularized into `partials/head`, `partials/header`, `partials/sidebar`, and `partials/footer`.
- Components are styled using Bootstrap 5 and customized SCSS in `resources/scss/`.

## Building and Running

### Prerequisites
- PHP 8.3 or higher
- Composer
- Node.js & NPM
- MySQL

### Setup Commands
1. **Install Dependencies:**
   ```bash
   composer install
   npm install
   ```
2. **Environment Configuration:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
3. **Database Setup:**
   ```bash
   # Ensure database is created first
   php artisan migrate --seed
   ```
4. **Compile Assets:**
   ```bash
   npm run dev   # For development
   npm run build # For production
   ```
5. **Run Server:**
   ```bash
   php artisan serve
   ```

## Development Conventions

### Coding Style
- **Naming:** Controller methods use standard English (e.g., `index`, `store`, `update`, `destroy`).
- **Validation:** Always perform validation within Controller methods using `$request->validate()`.
- **Formatting:** Adhere to PSR-12 for PHP and standard Laravel conventions.

### View Patterns
- **Template Engine:** Blade.
- **Comments:** Use `{{-- ... --}}` for Blade comments.
- **Extension:** All main pages must `@extends('layouts.app')`.
- **Dynamic Content:** Use `@section('content')` and `@push('scripts')` for page-specific logic.

### Routing
- Web routes are defined in `routes/web.php`.
- Question management routes are grouped under the `auth` middleware.
- Resource controllers are used for CRUD operations (e.g., `Route::resource('question-packages', ...)`).

## Directory Structure Highlights
- `app/Http/Controllers`: Business logic for exams and management.
- `app/Models`: Database entities and relationships.
- `database/migrations`: Evolution of the database schema.
- `resources/views`: UI templates and partials.
- `public/assets`: Static assets from the Codebase template.
