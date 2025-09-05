# FullTimez Job Portal - Complete Project Plan

## Project Overview
A comprehensive job portal built with Next.js frontend and Laravel backend, featuring job posting, job searching, candidate management, and employer tools.

## Technology Stack

### Frontend (Next.js 14)
- **Framework**: Next.js 14 with App Router
- **Styling**: Tailwind CSS + Custom CSS (matching FT FULL VERSION design)
- **State Management**: Zustand
- **UI Components**: Headless UI + Custom Components
- **Icons**: Font Awesome + Custom Icons
- **Forms**: React Hook Form + Zod validation
- **HTTP Client**: Axios
- **Authentication**: NextAuth.js with Laravel Sanctum

### Backend (Laravel 11)
- **Framework**: Laravel 11
- **Database**: MySQL/PostgreSQL
- **Authentication**: Laravel Sanctum
- **API**: RESTful API with Resource Controllers
- **Validation**: Form Request Validation
- **File Storage**: Laravel Storage (local/cloud)
- **Queue**: Laravel Queue for background jobs
- **Testing**: PHPUnit + Pest

## Project Structure

### Frontend Structure (Next.js)
```
frontend/
├── app/
│   ├── (auth)/
│   │   ├── login/
│   │   ├── register/
│   │   └── forgot-password/
│   ├── (dashboard)/
│   │   ├── employer/
│   │   ├── jobseeker/
│   │   └── admin/
│   ├── jobs/
│   ├── candidates/
│   ├── companies/
│   ├── about/
│   └── contact/
├── components/
│   ├── ui/
│   ├── forms/
│   ├── layout/
│   └── shared/
├── lib/
│   ├── api/
│   ├── auth/
│   ├── utils/
│   └── validations/
├── hooks/
├── store/
└── public/
    ├── images/
    └── icons/
```

### Backend Structure (Laravel)
```
backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   ├── Requests/
│   │   ├── Resources/
│   │   └── Middleware/
│   ├── Models/
│   ├── Services/
│   ├── Repositories/
│   └── Traits/
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── factories/
├── routes/
│   └── api.php
└── config/
```

## Database Schema

### Core Tables
1. **users** - User authentication and basic info
2. **user_profiles** - Extended user information
3. **companies** - Employer company information
4. **jobs** - Job postings
5. **job_applications** - Job applications
6. **job_categories** - Job categories
7. **job_locations** - Job locations
8. **skills** - Skills for jobs and candidates
9. **experiences** - Work experience
10. **educations** - Educational background
11. **certifications** - Professional certifications

### Relationships
- User has one Profile
- User belongs to Company (if employer)
- Company has many Jobs
- Job has many Applications
- User has many Applications
- Job belongs to Category and Location
- User has many Skills, Experiences, Educations, Certifications

## Features & Functionality

### Public Features
- Homepage with hero section and service cards
- Job search and filtering
- Job listings with pagination
- Job details page
- Company profiles
- Candidate profiles
- Registration forms (Jobseeker/Employer)
- Login system
- Contact form

### Jobseeker Features
- Profile management
- CV upload and management
- Job applications
- Saved jobs
- Job alerts
- Application tracking
- Skill endorsements

### Employer Features
- Company profile management
- Job posting and management
- Candidate search and filtering
- Application management
- Interview scheduling
- Analytics dashboard
- Payment management

### Admin Features
- User management
- Job moderation
- Company verification
- System analytics
- Content management

## API Endpoints

### Authentication
- POST /api/auth/login
- POST /api/auth/register
- POST /api/auth/logout
- POST /api/auth/refresh
- POST /api/auth/forgot-password
- POST /api/auth/reset-password

### Jobs
- GET /api/jobs
- GET /api/jobs/{id}
- POST /api/jobs (employer only)
- PUT /api/jobs/{id} (employer only)
- DELETE /api/jobs/{id} (employer only)
- POST /api/jobs/{id}/apply (jobseeker only)

### Users
- GET /api/user/profile
- PUT /api/user/profile
- GET /api/users (admin only)
- PUT /api/users/{id} (admin only)

### Companies
- GET /api/companies
- GET /api/companies/{id}
- POST /api/companies (employer only)
- PUT /api/companies/{id} (employer only)

## Design System

### Color Palette
- Primary: #1f2a64 (Dark Blue)
- Secondary: #20ac4b (Green)
- Accent: #ffc000 (Yellow)
- Text: #2d344b (Dark)
- Light Text: #6d6d6d
- Background: #ffffff

### Typography
- Primary Font: Poppins
- Secondary Font: Oswald
- Custom Font: James Stroker

### Components
- Service Cards (Yellow, Green, Pink, Blue)
- Job Cards with Premium badges
- Profile Cards
- Filter Widgets
- Search Forms
- Navigation Menu
- Footer Sections

## Development Phases

### Phase 1: Project Setup & Basic Structure
- Initialize Next.js and Laravel projects
- Set up database and basic migrations
- Create basic layouts and navigation
- Implement authentication system

### Phase 2: Core Features
- Job posting and management
- Job search and filtering
- User profiles and registration
- Basic dashboard functionality

### Phase 3: Advanced Features
- Application system
- Company profiles
- Advanced search and filters
- File upload system

### Phase 4: Polish & Optimization
- UI/UX improvements
- Performance optimization
- Testing and bug fixes
- Deployment preparation

## File Structure Implementation

### Frontend Components
- Header with search functionality
- Hero section with service cards
- Job listing components
- Profile management forms
- Dashboard layouts
- Responsive navigation

### Backend Services
- UserService for user management
- JobService for job operations
- CompanyService for company operations
- ApplicationService for job applications
- FileService for file uploads
- NotificationService for alerts

## Security Considerations
- Laravel Sanctum for API authentication
- CSRF protection
- Input validation and sanitization
- File upload security
- Rate limiting
- Role-based access control

## Performance Optimization
- Database indexing
- API response caching
- Image optimization
- Lazy loading
- Code splitting
- Bundle optimization

## Testing Strategy
- Unit tests for services
- Feature tests for API endpoints
- Frontend component testing
- E2E testing for critical flows
- Performance testing

## Deployment
- Frontend: Vercel/Netlify
- Backend: DigitalOcean/AWS
- Database: Managed MySQL/PostgreSQL
- File Storage: AWS S3/Cloudinary
- CI/CD: GitHub Actions

## Maintenance & Updates
- Regular security updates
- Performance monitoring
- User feedback integration
- Feature enhancements
- Bug fixes and patches

This project will be built following modern development practices with clean architecture, proper separation of concerns, and scalable design patterns.
