# Application Tracking System

The system allows users to track the progress of their submitted application. After submission, users receive regular email updates regarding the status of their application, which could be in various stages like:

- **Submitted**: The application has been successfully submitted.
- **In Review**: The application is being evaluated by the hiring team.
- **On Hold**: The application is temporarily paused for further consideration.
- **Rejected**: The application has been declined.
- **Approved**: The application has been accepted.

This system ensures that users are always informed about where their application stands, improving transparency and communication.

---

## Frontend
![My image](https://github.com/rishabhdiwan/ApplicationTrackingSystem/blob/master/assets/images/screenshot.png)

---

## Installation

1. Download the theme folder.
2. Move the theme folder to **wp-content/themes** directory.
3. Activate the theme through the WP dashboard by navigating to **Appearence > Themes** in WordPress.
4. Install **ACF**.
5. Import JSON file from **essentials/acf-data-552-wed.json**.
6. Install **WP SMTP** and configure it. Make sure that it is able to send mails using the Test Mail functionality.

---

## Features

### 1. Application Form Submission
- Users can submit their **name, email, and application document**.
- They can also upload a **profile image** (optional).

### 2. Application Processing
- Each submission creates a new **application post** in the system.
- The application is automatically marked as **"Submitted"**.

### 3. Email Notifications
- Users receive a **confirmation email** when they submit an application.
- If the application status changes, they get an **update email**.

---

## Author

ApplicationTrackingSystem is created by [Rishabh Diwan](https://rishabhdiwan.netlify.app) , a WordPress Developer with over 3 years of working experience.