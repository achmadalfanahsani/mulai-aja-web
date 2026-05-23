# Issue: Image Display Not Working in Question Management and Exam Attempt

## Description
Images uploaded for questions are not displaying in the following areas:
1. **Question List View:** `/question-packages/{id}/questions`
2. **Exam Attempt View:** `/exams/attempt/{id}`

## Objective
Enable images to be rendered correctly in both the administrative question management interface and the student-facing exam interface.

## Technical Context
- Images are likely stored in `storage/app/public/questions`.
- The current implementation likely uses relative paths or lacks the proper URL generation (e.g., using `Storage::url()`).

## Plan for Implementation

### 1. Research & Verification
- Check how the `Question` model handles the image path.
- Identify the current Blade views for the question list and exam attempt.
- Verify if `storage:link` has been executed (the `public/storage` symlink should point to `storage/app/public`).

### 2. Implementation Steps
- **Step 1: Check/Create Symlink**
  Ensure the storage link exists:
  ```bash
  php artisan storage:link
  ```

- **Step 2: Update View Controllers/Models**
  - Ensure the `Question` model returns the correct full URL or path for the image.
  - In Blade templates, use the `asset()` helper or `Storage::url()` to generate the correct source URL for `<img>` tags.

- **Step 3: Update Views**
  - Locate the `<img>` tags in the following files:
    - `resources/views/questions/index.blade.php` (or relevant view)
    - `resources/views/exams/attempt.blade.php`
  - Ensure they look like:
    `<img src="{{ asset('storage/' . $question->image_path) }}" alt="Question Image">`

### 3. Verification
- Upload a new image for a question.
- Navigate to the Question Management list and verify the image displays.
- Start an exam attempt and verify the image displays within the test interface.
