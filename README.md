# Laravel Quiz System

A comprehensive quiz management system built with Laravel, allowing lecturers to create and manage quizzes while students can take quizzes and view their results.

## Features

### For Lecturers
- Create and manage quizzes
- Add multiple-choice and text-based questions
- Set time limits for quizzes
- Publish/unpublish quizzes
- View student attempts and results
- Set start and end dates for quiz availability

### For Students
- View available quizzes
- Take quizzes with timer
- See immediate results after submission
- View detailed feedback for each question
- Track progress and past attempts

## Requirements

- PHP >= 7.4
- MySQL >= 5.7
- Composer
- XAMPP/WAMP/LAMP

## Installation

1. Clone the repository:
```bash
git clone https://github.com/oficialbertin/group-3-22RP01881-22RP03951-Quizsystem.git
cd quiz_system_laravel
```

2. Install dependencies:
```bash
composer install
```

3. Create and configure .env file:
```bash
cp .env.example .env
```
Update database configuration in .env file:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=quiz_system_laravel
DB_USERNAME=root
DB_PASSWORD=
```

4. Generate application key:
```bash
php artisan key:generate
```

5. Run migrations:
```bash
php artisan migrate
```

6. Start the development server:
```bash
php artisan serve
```

## Usage

1. Register as either a lecturer or student
2. For Lecturers:
   - Create a new quiz
   - Add questions and options
   - Set quiz parameters (time limit, availability)
   - Publish the quiz
3. For Students:
   - Browse available quizzes
   - Start a quiz attempt
   - Submit answers within time limit
   - View results and feedback

## Database Structure

- Users (id, name, email, role, password)
- Quizzes (id, user_id, title, description, time_limit, total_marks, is_published, start_time, end_time)
- Questions (id, quiz_id, question_text, type, marks)
- Options (id, question_id, option_text, is_correct)
- QuizAttempts (id, user_id, quiz_id, start_time, end_time, score)
- Answers (id, quiz_attempt_id, question_id, option_id, answer_text, is_correct, marks_obtained)

## Contributing

1. Fork the repository
2. Create your feature branch: `git checkout -b feature/my-new-feature`
3. Commit your changes: `git commit -am 'Add some feature'`
4. Push to the branch: `git push origin feature/my-new-feature`
5. Submit a pull request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Authors

- 22RP01881  Yvette UWUMUKIZA
- 22RP03951  Bertin HAKIZAYEZU

## Acknowledgments

- Laravel Framework
- Bootstrap for UI components
- XAMPP for local development environment
