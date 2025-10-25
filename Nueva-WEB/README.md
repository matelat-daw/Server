# Nueva-WEB Project

## Overview
This project is a web application that consists of a frontend and an API backend. The frontend is designed with a mobile-first approach, ensuring a responsive and user-friendly experience across various devices. The API provides user authentication and product management functionalities.

## Project Structure
```
Nueva-WEB
├── api
│   ├── config
│   │   └── database.php
│   ├── controllers
│   │   ├── AuthController.php
│   │   ├── ProductController.php
│   │   └── UserController.php
│   ├── middleware
│   │   └── AuthMiddleware.php
│   ├── models
│   │   ├── User.php
│   │   └── Product.php
│   ├── routes
│   │   └── api.php
│   └── index.php
├── frontend
│   ├── components
│   │   ├── header
│   │   ├── nav
│   │   ├── product-list
│   │   ├── product-card
│   │   ├── login
│   │   ├── register
│   │   ├── user-menu
│   │   ├── cart
│   │   └── footer
│   ├── pages
│   │   ├── home
│   │   ├── products
│   │   ├── about
│   │   └── contact
│   ├── services
│   ├── styles
│   ├── app.js
│   └── index.html
├── .htaccess
└── README.md
```

## Features
- **User Authentication**: Users can register, log in, and manage their profiles.
- **Product Management**: Users can view and purchase products.
- **Responsive Design**: The application is optimized for mobile devices, tablets, and desktops.
- **Dark and Light Themes**: The application supports both dark and light themes for better user experience.

## Getting Started
1. Clone the repository to your local machine.
2. Set up the database configuration in `api/config/database.php`.
3. Run the API server and the frontend server.
4. Access the application through your web browser.

## Technologies Used
- PHP for the backend API.
- JavaScript for the frontend components.
- HTML and CSS for the structure and styling of the application.

## Future Enhancements
- Implement additional features such as product reviews and ratings.
- Enhance security measures for user authentication.
- Optimize performance for faster loading times.

## License
This project is licensed under the MIT License.