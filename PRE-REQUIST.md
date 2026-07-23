Think of the frontend (HTML/CSS) as the sinks, showerheads, and toilets in a house. They look nice, but they do absolutely nothing on their own. Think of the database as the main water tank. The backend (PHP) is the plumbing—the pipes connecting the sinks to the tank. Right now, you have a bunch of team members handing you random pieces of pipe. If you just glue them together without checking, your house will flood.

Here is your exact, ruthless, step-by-step master plan for backend integration on your Ubuntu environment. Do not skip a single step.

Phase 1: Build the Water Tank (Database Foundation)
Before you look at a single line of your team's PHP code, your local Ubuntu MySQL database must be flawless.

Open phpMyAdmin on your local server.

Execute the master SQL schema I provided earlier to create the gpcms database.

Verify that all tables exist: users, complaints, complaint_photos, complaint_history, categories, notifications, feedback, and settings.  
DOCX
+ 1

Verify that the status column in the complaints table only accepts the exact vocabulary: pending, assigned, in_progress, resolved.  
DOCX
+ 2

Phase 2: Lay the Main Pipe (Global Connection)
Every module must use the exact same pipe to talk to the database.

Ensure your config/db.php file is perfect.

Ensure config/db_connect.php wraps it and exposes the $conn variable.  
DOCX
+ 1

Make sure your Ubuntu file permissions allow PHP to read these files, but keep them completely isolated from the frontend folders.

Phase 3: Set the Security Bouncers (Authentication Layer)
You are building a role-based system. You cannot let a citizen access the super admin dashboard.  
PDF

Create includes/auth_check.php.  
DOCX

This file must start with session_start();.

It must check if $_SESSION['is_logged_in'] is true. If not, redirect them immediately to login.php.  
DOCX
+ 2

It must verify role access. If the URL contains /admin/ but their $_SESSION['role_name'] is not "Gram Panchayat Admin", kick them out.  
DOCX
+ 1

Phase 4: The Ruthless Code Audit (Pre-Merge Checklist)
This is where you earn your title as Integration Lead. When a team member hands you their backend PHP file, put it in a temporary folder. Do not merge it yet. Run this exact checklist:

The First Line Test: Is <?php session_start(); the absolute first line of the file? If there is even one blank space before it, reject it.

The Inclusion Test: Does the file properly include ../config/db_connect.php and ../includes/auth_check.php using the correct relative paths?  
DOCX
+ 1

The Form Match Test: Open their PHP file and look at their $_POST variables. Do they exactly match the canonical names in the contract (e.g., $_POST['complainant_name'], $_POST['village_ward'])? If they used $_POST['name'], reject it.  
DOCX
+ 1

The Injection Test: Look at their MySQL queries. Did they use $conn->prepare()? If you see a raw query like query("INSERT INTO complaints VALUES (" . $_POST['id'] . ")"), reject it immediately. That is an SQL injection vulnerability.

The Status Vocabulary Test: Are they hardcoding the exact status values (pending, assigned, in_progress, resolved)? If a field officer module tries to update a status to "done", reject it.  
DOCX
+ 2

Phase 5: The Physical Integration
Once a module passes the ruthless audit, you integrate it.

Move the PHP file into its contracted folder (e.g., citizen/register_complaint.php).  
DOCX

Create the necessary physical folders on your Ubuntu machine for file uploads (e.g., uploads/citizen_initial/). Give them the correct write permissions (chmod 775 or 777 locally) so PHP's move_uploaded_file() doesn't crash.

Run the "Happy Path" test: Log in as a Citizen, submit a complaint, upload a photo. Check phpMyAdmin. Did the row appear? Did the photo save in the folder?  
DOCX
+ 1

Run the "Sad Path" test: Try to submit the form empty. Try to upload a 50MB PDF. Does the system throw a clean error, or does it crash violently?  
DOCX
+ 1

Only after it survives Phase 5 do you commit the code to your Git branch.

When you test the file uploads for the complaint_photos table, how are you planning to handle duplicate file names if two different citizens upload an image named pothole.jpg on the exact same day?