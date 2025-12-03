1. Stored XSS in Patient Data
1.1 Components

Patient registration form: index.php

Admin dashboard listing patients / appointments / queries: admin-panel1.php

1.2 Baseline Behaviour (v0 – vulnerable)

Browse to index.php.

Select the Patient tab.

Fill in the registration form with valid values, but set First Name to:

<script>alert('Hacked')</script>


(any free-text field such as name or contact message can be used as long as
it appears later in the admin tables).

Submit the form to create the patient.

Log out, then log in as admin / receptionist.

Open the admin dashboard (admin-panel1.php) where patient list, appointments
or messages are displayed.

Expected v0 result

The JavaScript payload executes in the admin’s browser (e.g. alert pops).

In the original baseline, the UI can become unusable until the malicious row
is deleted from the database.

1.3 Hardened Behaviour (v1 – fixed)

Repeat the same steps on the hardened version.

Expected v1 result

The <script>...</script> text is shown as plain text in the table.

No alert appears and the admin dashboard remains fully usable.

2. Insecure Direct Object Reference (IDOR) – Appointment Cancellation
2.1 Components

Appointment view / cancellation: admin-panel.php

URL parameter: ID (appointment ID)

2.2 Preconditions

Two patient accounts exist: Patient A and Patient B.

Each has at least one booked appointment so that their appointment IDs are
different.

2.3 Baseline Behaviour (v0 – vulnerable)

Log in as Patient A.

Go to the appointments page and note Patient A’s appointment ID (e.g. 7)
from the table or by inspecting the cancel link.

Log out.

Log in as Patient B.

Either:

Click the cancel button on one of Patient B’s own appointments and then
change the ID value in the URL, or

Directly browse to:

http://localhost/Hospital-Management-System-master/admin-panel.php?cancel=update&ID=7


Confirm the cancellation.

Expected v0 result

The appointment belonging to Patient A is cancelled while logged in as
Patient B.

Data integrity and patient privacy are violated.

2.4 Hardened Behaviour (v1 – fixed)

Repeat the same steps on the hardened version.

Expected v1 result

When Patient B attempts to cancel Patient A’s appointment (by changing ID),
the system rejects the action (no change to Patient A’s appointment).

Either an error / warning is shown, or the request quietly fails.

3. Weak Password Storage – Plaintext Credentials
3.1 Components

Patient registration: index.php, func.php

Doctor creation: admin UI, func1.php

Database tables: patreg, doctb

3.2 Baseline Behaviour (v0 – vulnerable)

Register a new patient using the UI with a known password such as
TestPassword123!.

Optionally create a new doctor account via the admin interface with
another known password.

Open phpMyAdmin and select the myhmsdb database.

View the patreg and doctb tables.

Expected v0 result

Password fields contain the exact plaintext passwords you entered.

3.3 Hardened Behaviour (v1 – fixed)

Repeat the steps after deploying the hardened version.

Expected v1 result

Password fields now contain long hash strings (e.g. starting with $2y$…).

The original plaintext values are not visible anywhere in the DB.

Login still succeeds using the same username/password because
password_verify() is used.

4. Broken Access Control – Direct Admin URL Access
4.1 Components

Admin dashboard page: admin-panel1.php

Login logic: func.php, func1.php

4.2 Baseline Behaviour (v0 – vulnerable)

Open a private / incognito browser window to ensure no session exists.

Browse directly to:

http://localhost/Hospital-Management-System-master/admin-panel1.php


Expected v0 result

The full admin dashboard loads even though no user is logged in.

Patient lists, appointment history, doctor details and contact messages are
visible.

4.3 Hardened Behaviour (v1 – fixed)

Repeat the same step on the hardened version.

Expected v1 result

The request is intercepted by the session check.

The user is redirected to the login page (or a “not authorised” page).

Admin content is only shown after authenticating as an admin.

5. Login Rate Limiting / Brute Force Protection
5.1 Components

Login processing: func.php, func1.php, func3.php

Session data for login attempts

5.2 Baseline Behaviour (v0 – vulnerable)

Open the login form (patient, doctor or admin) on index.php.

Repeatedly submit incorrect passwords for a valid username.

Manually, or by using OWASP ZAP’s Fuzzer against the password field.

Observe how many times you can submit credentials.

Expected v0 result

You can send an unlimited number of incorrect attempts with no delay or
lockout.

5.3 Hardened Behaviour (v1 – fixed)

On the hardened version, again submit incorrect credentials several times
in a row (e.g. 3–5 times).

Optionally repeat using OWASP ZAP to automate multiple wrong passwords.

Expected v1 result

After the configured number of failed attempts, the login endpoint:

Shows an error such as “Too many failed attempts. Try again later”, and

Temporarily blocks further attempts for that session / IP for the lockout
time (e.g. 60 seconds).

6. Directory Browsing Exposure
6.1 Components

Web server configuration via .htaccess / httpd.conf

Static directories such as /images, /assets etc.

6.2 Baseline Behaviour (v0 – vulnerable)

In the browser, guess or navigate to a static directory, for example:

http://localhost/Hospital-Management-System-master/images/


Test other folders that might contain static content.

Expected v0 result

Apache lists all files in the directory (directory index page).

This reveals internal file names and paths.

6.3 Hardened Behaviour (v1 – fixed)

Repeat the same URLs once .htaccess and configuration changes are in place.

Expected v1 result

The directory listing is no longer shown.

Either an HTTP 403 error, a blank page, or a custom “not allowed” response is
returned and individual files cannot be listed by browsing to the folder.
