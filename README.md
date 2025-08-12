# SkillConnect

A modern freelancer platform built with PHP MVC architecture that connects clients with freelancers for project collaboration.

## 🚀 Features

### For Clients
- Create and manage projects
- Set project budgets and timelines
- Track project status (Pending, In Progress, Cancelled)
- View project statistics and filtering
- Secure client dashboard

### For Freelancers
- Browse available projects
- Accept/reject project offers
- Manage project workflow (Accept → In Progress → Finished)
- Track earnings and project statistics
- Personal freelancer dashboard

### Authentication System
- User registration with role selection (Client/Freelancer)
- Secure login system with password hashing
- Session management
- Role-based access control

## 🛠️ Tech Stack

- **Backend**: PHP 7.4+
- **Database**: PostgreSQL
- **Frontend**: Bootstrap 5.3.3, HTML5, CSS3, JavaScript
- **Architecture**: MVC (Model-View-Controller)
- **Dependency Management**: Composer
- **Session Management**: PHP Sessions
- **Security**: Password hashing, input validation, SQL injection prevention

## 📋 Prerequisites

Before running this project, make sure you have:

- PHP 7.4 or higher
- PostgreSQL database
- Composer
- Web server (Apache/Nginx) or PHP built-in server

## 🔧 Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd SkillConnect
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Database Setup**
   - Create a PostgreSQL database named `freelancedb`
   - Update database credentials in `src/Service/DatabaseService.php`:
     ```php
     self::$pdo = new PDO('pgsql:host=localhost;port=5432;dbname=freelancedb', 'your_username', 'your_password', [
         PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
     ]);
     ```

4. **Create Database Tables**
   
   **Users Table:**
   ```sql
   CREATE TABLE users (
       id SERIAL PRIMARY KEY,
       name VARCHAR(255) NOT NULL,
       email VARCHAR(255) UNIQUE NOT NULL,
       password VARCHAR(255) NOT NULL,
       role VARCHAR(50) NOT NULL CHECK (role IN ('client', 'freelancer')),
       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
       updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
   );
   ```

   **Projects Table:**
   ```sql
   CREATE TABLE projects (
       id SERIAL PRIMARY KEY,
       title VARCHAR(255) NOT NULL,
       description TEXT NOT NULL,
       freelancer_id INTEGER REFERENCES users(id) ON DELETE SET NULL,
       cost DECIMAL(10,2) NOT NULL,
       dev_time INTEGER NOT NULL,
       client_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
       status VARCHAR(50) NOT NULL DEFAULT 'pending' CHECK (status IN ('pending', 'in_progress', 'finished', 'cancelled', 'rejected')),
       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
       updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
   );
   ```

5. **Configure Web Server**
   - Point your web server document root to the project directory
   - Ensure `index.php` is accessible
   - For development, you can use PHP built-in server:
     ```bash
     php -S localhost:8000
     ```

## 🚀 Usage

1. **Access the Application**
   - Open your browser and navigate to your configured URL
   - You'll see the welcome page with Login/Sign Up options

2. **Create an Account**
   - Click "Sign In" to register
   - Choose your role: Client or Freelancer
   - Fill in your details and create account

3. **For Clients:**
   - Login to access the client dashboard
   - Create new projects with title, description, budget, and timeline
   - Track project status and manage your projects
   - Filter projects by status (All, Pending, In Progress, Cancelled)

4. **For Freelancers:**
   - Login to access the freelancer dashboard
   - Browse available projects in the "Available Projects" section
   - Accept projects you want to work on
   - Manage your workflow: Accept → In Progress → Finished
   - Track your earnings and project statistics

## 📁 Project Structure

```
SkillConnect/
├── src/
│   ├── Controllers/           # Handle HTTP requests and responses
│   │   ├── AuthController.php
│   │   ├── ClientController.php
│   │   └── FreelancerController.php
│   ├── Models/               # Database interaction layer
│   │   ├── projectModel.php
│   │   └── userModel.php
│   ├── Service/              # Business logic layer
│   │   ├── AuthService.php
│   │   ├── ClientService.php
│   │   ├── DatabaseService.php
│   │   └── FreelancerService.php
│   ├── Router/               # URL routing system
│   │   └── Router.php
│   └── Views/                # User interface templates
│       ├── auth/
│       ├── Client/
│       └── freelancer/
├── vendor/                   # Composer dependencies
├── index.php                # Application entry point
├── composer.json            # Project dependencies
└── README.md               # Project documentation
```

## 🔐 Security Features

- Password hashing using PHP's `password_hash()`
- SQL injection prevention using prepared statements
- Input validation and sanitization
- Session-based authentication
- Role-based access control
- CSRF protection considerations

## 🎯 API Endpoints

### Authentication
- `GET /` - Welcome page
- `GET /login` - Login form
- `GET /signin` - Registration form
- `POST /submit-login` - Process login
- `POST /submit-signin` - Process registration

### Client Routes
- `GET /client` - Client dashboard
- `POST /add-project` - Create new project
- `POST /update-project-status` - Update project status

### Freelancer Routes
- `GET /dashboard` - Freelancer dashboard
- `GET /accept-project/{id}` - Accept a project
- `GET /reject-project/{id}` - Reject a project
- `GET /finish-project/{id}` - Mark project as finished
- `GET /cancel-project/{id}` - Cancel a project

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/new-feature`)
3. Commit your changes (`git commit -am 'Add new feature'`)
4. Push to the branch (`git push origin feature/new-feature`)
5. Create a Pull Request

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 👨‍💻 Authors

- **Zakariae Azhari** - Initial work

## 🐛 Known Issues

- Session timeout configuration may need adjustment based on server settings
- Some error handling could be more user-friendly
- Email notifications are not yet implemented

## 🔮 Future Enhancements

- Real-time notifications
- File upload functionality for projects
- Payment integration
- Advanced search and filtering
- Rating and review system
- API for mobile applications
- Email notifications
- Admin panel

## 📞 Support

For support and questions, please create an issue in the repository or contact the development team.

---

**Built with ❤️ using PHP MVC Architecture**
