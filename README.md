# Dar Anawat — Moroccan Guesthouse Website

## 1. Project Overview

**Dar Anawat** is a Laravel web application for a Moroccan guesthouse. The project aims to provide visitors with an elegant and user-friendly experience to discover the guesthouse, explore available rooms, view room details, and access the reservation process.

The website combines a modern luxury design with Moroccan-inspired elements, using an ivory and cream background, deep green accents, and gold details.

## 2. Technologies Used

- **Backend:** PHP, Laravel 12
- **Frontend:** HTML5, CSS3, JavaScript, Laravel Blade
- **Database:** MySQL
- **Development environment:** XAMPP, Visual Studio Code
- **Dependency management:** Composer, npm
- **Version control:** Git and GitHub

## 3. Implemented Pages and Features

### 3.1 Home Page

The home page introduces Dar Anawat and highlights its main services and experiences.

Implemented sections include:

- Hero section with a main call to action.
- About section presenting the guesthouse.
- Room discovery section.
- Experiences and services.
- Discover Morocco section.
- Call-to-action section encouraging visitors to explore or plan their stay.
- Navigation links to the rooms page and other website sections.

### 3.2 Rooms Page

The rooms page presents the available room categories in a structured layout.

Features include:

- Room cards with photos.
- Room names and prices.
- Short room descriptions.
- Links to the corresponding room details.
- Navigation between the home page, rooms page, and details pages.

**Recent update:** The Size information has been requested to be removed from both the rooms listing page and the room details page.

### 3.3 Room Details Page

The details page provides more information about a selected room.

Features include:

- Room name and description.
- Price per night.
- Guest capacity.
- Bed information.
- Room photos and gallery.
- Available amenities and equipment.
- Access to the reservation process.

The room details are displayed according to the selected room type.

### 3.4 Room Categories

The website includes three room categories:

| Room | Price per night |
|---|---:|
Triple Room: 3 separate beds

Twin Room: 2 separate beds

Suite: 1 large bed + private terrace

Double Room: 1 large bed
These are the room prices previously configured in the project and may be updated as needed.

### 3.5 Multilingual Support

The website is designed to support four languages:

- French (`fr`)
- English (`en`)
- Spanish (`es`)
- Arabic (`ar`)

The project includes language-switching functionality and translation support. The interface is intended to adapt to the selected language, including right-to-left presentation for Arabic where configured.

### 3.6 Reservation Features

The project includes reservation-related functionality and routes for creating and storing reservations.

The intended visitor journey is:

1. Visit the home page.
2. Browse the available rooms.
3. Open a room details page.
4. Access the reservation process.
5. Continue through the reservation and payment flow where implemented.

The final status of payment processing and reservation confirmation should be verified against the current code before considering the complete booking workflow finished.

## 4. Application Routes

The main routes include:

| Route | Purpose |
|---|---|
| `/` | Home page |
| `/chambres` | Rooms listing |
| `/chambres/{type}` | Details of a selected room |
| Language-switching route | Change the website language |
| Reservation creation route | Display or access the reservation process |
| Reservation storage route | Submit a reservation |

The room details route uses the selected room type to display the corresponding information.

Invalid room types and unsupported locales are expected to return a 404 response. Reservation submission is configured to be rate-limited to help prevent excessive requests.

Exact route names and HTTP methods should be checked in `routes/web.php`.

## 5. User Interface and Design

The design follows a Moroccan luxury hospitality aesthetic.

Main design principles:

- Elegant and minimalist presentation.
- Ivory and cream backgrounds.
- Deep green and gold accents.
- High-quality room photography.
- Consistent navigation and footer components.
- Reusable room cards.
- Clear calls to action.
- Responsive layouts for different screen sizes.

## 6. Reusable Components

The project uses reusable Blade components and layouts to maintain consistency and reduce duplicated code.

These include:

- Navigation bar.
- Footer.
- Room cards.
- Shared page layout and common interface sections.

Blade templates use Laravel's template inheritance and component mechanisms where appropriate.

## 7. Version Control and Collaboration

Git and GitHub are used to manage source code and collaborate on the project.

Main activities include:

- Creating and working on branches.
- Staging and committing changes.
- Pulling updates from remote branches.
- Merging collaborators' work.
- Pushing changes to GitHub.
- Resolving merge conflicts when necessary.

The project has involved separate frontend and reservation-related work. Before merging or pushing, the current branch and working-tree status should be checked to avoid overwriting another contributor's changes.

## 8. Installation and Setup

### Prerequisites

Install the following tools:

- PHP compatible with Laravel 12.
- Composer.
- Node.js and npm.
- MySQL.
- XAMPP or another compatible local development environment.

### Installation Steps

Clone the repository:

```bash
git clone https://github.com/samira-2002-code/DarAnawat_stage.git
```

Open the project directory:

```bash
cd DarAnawat_stage
```

Install PHP dependencies:

```bash
composer install
```

Install frontend dependencies:

```bash
npm install
```

Create the environment file:

```bash
cp .env.example .env
```

On Windows, you can also copy `.env.example` manually and rename the copy to `.env`.

Generate the application key:

```bash
php artisan key:generate
```

Configure the database credentials in `.env` and create the corresponding MySQL database.

Run database migrations if the project's migrations are ready:

```bash
php artisan migrate
```

Start the Laravel development server:

```bash
php artisan serve
```

If the project uses Vite for frontend assets, run this command in a second terminal:

```bash
npm run dev
```

Open the local URL displayed by Laravel in your browser.

## 9. Testing and Verification

Before submitting changes, verify the following:

- The home page loads correctly.
- The rooms page displays the room cards.
- Each room links to the correct details page.
- Room names, prices, photos, and capacity are displayed correctly.
- The Size label and value no longer appear on the rooms and details pages.
- Language switching works for supported locales.
- Reservation routes behave as expected.
- No existing functionality has been broken by recent changes.

Run the automated test suite with:

```bash
php artisan test
```

The test results must be checked after execution; this README does not claim that the latest changes have already passed all tests.

## 10. Recent Changes

The latest requested frontend modification is to remove the **Size** information from:

- The rooms listing page.
- The room details page.

All other room information and reservation functionality should remain unchanged.

The removal concerns the user interface only. Room size data should not be deleted from the database or other parts of the application unless separately required.

## 11. Future Improvements

Potential improvements for future development include:

- Completing and validating the full reservation workflow.
- Integrating a payment solution or a clearly defined payment simulation.
- Adding reservation confirmation.
- Developing an administration interface for managing rooms and reservations.
- Improving responsive design and accessibility.
- Expanding automated tests.
- Completing translations across all pages.
- Preparing production deployment.

## 12. Project Objective

The objective of Dar Anawat is to provide a professional digital presence for a Moroccan guesthouse, allowing visitors to discover the property, explore room options, view detailed information, and access a convenient reservation experience.

---

**Project:** Dar Anawat  
**Framework:** Laravel 12  
**Repository:** https://github.com/samira-2002-code/DarAnawat_stage.git  
**Status:** Under development
