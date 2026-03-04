# AutoLearn – E-Learning Platform

## Overview

This project was developed as part of the **PIDEV – 3rd Year Engineering Program** at **Esprit School of Engineering** (Academic Year 2025–2026).

AutoLearn is a comprehensive e-learning platform designed to enhance the educational experience through interactive courses, quizzes, events management, and community features. The platform provides both student and administrator interfaces with advanced AI-powered features for content generation and analysis.

## Features

### Course Management
- Interactive chapters with multimedia content
- Progress tracking and completion certificates
- AI-powered chapter explanations
- Multi-language support (French/English)

### Quiz System
- AI-generated quizzes using Groq API
- Multiple question types (MCQ, True/False)
- Automatic grading and feedback
- Image support for questions
- Real-time quiz passage interface

### Event Management
- Event creation and management (Hackathons, Workshops, Conferences)
- Team formation and participation
- Email notifications with QR codes
- Event calendar integration
- Feedback collection and AI-powered analytics

### Community Features
- Community creation and management
- Posts and comments system
- Reaction system
- Member management with roles

### Challenge System
- Challenge creation and tracking
- User progress monitoring
- Voting system
- Leaderboard

### User Management
- Role-based access control (Admin, Student)
- User activity tracking
- Audit logging
- Suspension management
- Password reset functionality

## Tech Stack

### Frontend
- **Twig** - Template engine
- **Bootstrap 5** - UI framework
- **JavaScript** - Client-side interactivity
- **FullCalendar** - Event calendar
- **Chart.js** - Data visualization

### Backend
- **PHP 8.2** - Programming language
- **Symfony 6.4** - Web framework
- **Doctrine ORM** - Database abstraction
- **MySQL/MariaDB** - Database

### AI Integration
- **Groq API** - AI-powered quiz generation and content analysis
- **Llama 4 Scout** - Language model for intelligent features

### Email & Notifications
- **Brevo (Sendinblue)** - Email service provider
- **Symfony Mailer** - Email handling
- **QR Code generation** - Event participation tracking

### Development Tools
- **Composer** - Dependency management
- **PHPStan** - Static analysis (Level 1 compliant)
- **Doctrine Migrations** - Database versioning
- **Symfony Messenger** - Asynchronous processing

## Architecture

```
autolearn/
├── config/              # Configuration files
├── migrations/          # Database migrations
├── public/              # Public assets
│   ├── Backoffice/     # Admin panel assets
│   └── frontoffice/    # Student interface assets
├── src/
│   ├── Bundle/         # Custom bundles
│   ├── Command/        # CLI commands
│   ├── Controller/     # Application controllers
│   ├── Entity/         # Doctrine entities
│   ├── Form/           # Form types
│   ├── Repository/     # Database repositories
│   ├── Service/        # Business logic services
│   └── EventSubscriber/ # Event listeners
├── templates/
│   ├── backoffice/     # Admin templates
│   ├── frontoffice/    # Student templates
│   └── emails/         # Email templates
└── tests/              # Unit and functional tests
```

## Contributors

**Team Brain-Up – Class 3A43**

- **Amira** - Event Management Module
- **Ilef** - Quiz Management Module
- **Yassmine** - Course Management Module
- **Nour** - Community Management Module
- **Mariem** - Challenge Management Module

## Academic Context

**Developed at Esprit School of Engineering – Tunisia**

- **Program**: PIDEV (Projet Intégré de Développement)
- **Class**: 3A43
- **Academic Year**: 2025–2026
- **Supervisor**: [Supervisor Name]

## Getting Started

### Prerequisites

- PHP 8.2 or higher
- Composer
- MySQL/MariaDB
- Node.js (for asset compilation)

### Installation

1. Clone the repository
```bash
git clone https://github.com/yassminebenslimene/Esprit-PIDEV-3A43-2026-AutoLearn.git
cd autolearn
```

2. Install dependencies
```bash
composer install
```

3. Configure environment variables
```bash
cp .env.example .env
# Edit .env with your database credentials and API keys
```

4. Create database and run migrations
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

5. Load fixtures (optional)
```bash
php bin/console doctrine:fixtures:load
```

6. Start the development server
```bash
symfony server:start
```

7. Access the application
- Frontend: http://localhost:8000
- Backend: http://localhost:8000/backoffice

### Configuration

#### Required API Keys

- **GROQ_API_KEY**: For AI-powered features
- **BREVO_API_KEY**: For email notifications
- **WEATHER_API_KEY**: For weather integration

#### Email Configuration

Configure Brevo (Sendinblue) in `.env`:
```env
MAILER_DSN=brevo+api://your-api-key@default
MAIL_FROM_EMAIL=your-verified-email@example.com
```

## Testing

Run PHPStan static analysis:
```bash
vendor/bin/phpstan analyse
```

Run unit tests:
```bash
php bin/phpunit
```

## Quality Assurance

- ✅ PHPStan Level 1 compliant (0 errors)
- ✅ Doctrine schema validated
- ✅ N+1 query optimization implemented
- ✅ Email notifications tested and functional

## Acknowledgments

Special thanks to:
- **Esprit School of Engineering** for providing the academic framework
- Our project supervisor for guidance and support
- The Symfony community for excellent documentation
- Groq for providing AI API access

---

**© 2026 AutoLearn - Esprit School of Engineering**
