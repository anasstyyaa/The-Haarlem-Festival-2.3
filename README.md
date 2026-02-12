PC Reservation System

Project Overview:

The project is about a reservation system built for internet cafes and it was built using PHP.
On the system there are users and admins as roles, Users can register, log in, view available PCs and book reservations for their desired time.
Admininstrators can manage PCs and view all reservations.

User features:

- Register and log in
- View available PCs
- Book a PC for a selected date and time
- View and cancel own reservations
- Update profile information (Username and email)
- Password reset functionality

Admin features: 

- View dashboard with all reservation history
- Cancel any reservation
- Do crud operation for PCs.
- Can do all the user features.

Login Information:

To log in as admin you can use this information
Email Address: Admin@gmail.com
Password: adminadmin
To log in as user:
Email Address: User@gmail.com
Password: useruser

The application provides a simple API endpoint at: http://localhost/api/pcs
which returns a list of available PCs as json response from the database.

Docker

The application is dockerized using the setup provided in this course.
The project can be started with: Docker compose up

GDPR & WCAG Considerations

- Only neccesary personal data is stored
- Password are never plain text in the database
- Users can update their personal information (email and username) or if they happen to forget the password they can use the forgot password     functionality.