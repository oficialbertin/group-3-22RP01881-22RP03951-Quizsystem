# Online Quiz System Platform

A web-based application designed to streamline the process of administering, taking, and reviewing quizzes in an academic setting. This platform facilitates both students and lecturers by providing a structured and interactive environment for online assessments.

## Features

- User Authentication with role-based access (Student/Lecturer)
- Quiz Management for Lecturers
- Quiz Taking for Students
- Instant Results and Feedback
- Performance Review
- Responsive Design

## Prerequisites

- PHP >= 8.0
- Composer
- MySQL
- Node.js and NPM
- XAMPP (or any other local server environment)

## Installation Steps

1. **Clone the repository**
   ```bash
   git clone [repository-url]
   cd quiz_system_laravel
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install JavaScript dependencies**
   ```bash
   npm install
   ```

4. **Create environment file**
   ```bash
   cp .env.example .env
   ```

5. **Generate application key**
   ```bash
   php artisan key:generate
   ```

6. **Configure database**
   - Open `.env` file
   - Update the following database settings:
     ```
     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=quiz_system
     DB_USERNAME=root
     DB_PASSWORD=
     ```

7. **Create database**
   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Create a new database named `quiz_system`

8. **Run migrations**
   ```bash
   php artisan migrate
   ```

9. **Start the development server**
   ```bash
   php artisan serve
   ```

10. **Start Vite for assets**
    ```bash
    npm run dev
    ```

## Testing the Application

1. **Register as a Lecturer**
   - Visit http://localhost:8000/register
   - Fill in the registration form
   - Select "Lecturer" as the role
   - Complete registration

2. **Register as a Student**
   - Visit http://localhost:8000/register
   - Fill in the registration form
   - Select "Student" as the role
   - Complete registration

3. **Lecturer Features**
   - Create quizzes
   - Add questions to quizzes
   - Publish/unpublish quizzes
   - View quiz results

4. **Student Features**
   - View available quizzes
   - Take quizzes
   - View results and feedback
   - Track progress

## Development Progress

The project is currently in development. Here's what has been implemented:

- ✅ Database structure and migrations
- ✅ User authentication with role-based access
- ✅ Quiz management system
- ✅ Question management system
- ✅ Quiz attempt system
- ✅ Results and feedback system
- ✅ Basic UI components

## Contributing

Feel free to contribute to this project by:
1. Forking the repository
2. Creating a new branch
3. Making your changes
4. Submitting a pull request

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
