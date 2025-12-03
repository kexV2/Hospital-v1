# Security Testing Scenarios

---

# 1. Stored XSS in Patient Data

## 1.1 Components
- Patient registration form: `index.php`
- Admin dashboard listing patients / appointments / queries: `admin-panel1.php`

---

## 1.2 Baseline Behaviour (v0 – vulnerable)

1. Browse to `index.php`.
2. Select the **Patient** tab.
3. Fill in the registration form with valid values, but set First Name to:

   ```html
   <script>alert('Hacked')</script>
(any free-text field such as name or contact message can be used as long as it appears later in the admin tables).

Submit the form to create the patient.

Log out, then log in as admin / receptionist.

Open the admin dashboard (admin-panel1.php).

Expected v0 result
The JavaScript payload executes in the admin’s browser (e.g. alert pops).

The admin UI can become unusable until the malicious row is deleted.

1.3 Hardened Behaviour (v1 – fixed)
Repeat the same steps on the hardened version.

Expected v1 result
The <script>...</script> text is displayed as plain text.

No alert or script execution occurs.

The admin dashboard remains fully usable.

2. Insecure Direct Object Reference (IDOR) – Appointment Cancellation
2.1 Components
Appointment view / cancellation: admin-panel.php

URL parameter: ID (appointment ID)

2.2 Preconditions
Two patient accounts exist: Patient A and Patient B.

Each has at least one booked appointment.

2.3 Baseline Behaviour (v0 – vulnerable)
Log in as Patient A.

Note Patient A’s appointment ID (e.g. 7).

Log out.

Log in as Patient B.

Either:

Click cancel on one of Patient B’s appointments and change ID in the URL, or

Directly visit:

pgsql
Copy code
http://localhost/Hospital-Management-System-master/admin-panel.php?cancel=update&ID=7
Confirm the cancellation.

Expected v0 result
Patient A’s appointment is cancelled by Patient B.

Data integrity and privacy are violated.

2.4 Hardened Behaviour (v1 – fixed)
Repeat on the hardened version.

Expected v1 result
Patient B cannot cancel Patient A’s appointment.

System rejects the action (error or silent failure).

3. Weak Password Storage – Plaintext Credentials
3.1 Components
Patient registration: index.php, func.php

Doctor creation: admin UI, func1.php

Database tables: patreg, doctb

3.2 Baseline Behaviour (v0 – vulnerable)
Register a new patient with a known password (e.g. TestPassword123!).

Optionally create a doctor with another known password.

Open phpMyAdmin → select myhmsdb.

View the patreg and doctb tables.

Expected v0 result
Password columns contain plaintext passwords exactly as typed.

3.3 Hardened Behaviour (v1 – fixed)
Repeat steps on hardened version.

Expected v1 result
Password fields contain hashed values (e.g. $2y$…).

Plaintext passwords are never visible.

Login still works via password_verify().

4. Broken Access Control – Direct Admin URL Access
4.1 Components
Admin dashboard page: admin-panel1.php

Login logic: func.php, func1.php

4.2 Baseline Behaviour (v0 – vulnerable)
Open a private / incognito window.

Browse to:

bash
Copy code
http://localhost/Hospital-Management-System-master/admin-panel1.php
Expected v0 result
Full admin dashboard loads without authentication.

Patient lists, appointments, doctor details and messages are visible.

4.3 Hardened Behaviour (v1 – fixed)
Repeat on hardened version.

Expected v1 result
The session check blocks access.

User is redirected to the login page.

Admin content loads only after valid login.

5. Login Rate Limiting / Brute Force Protection
5.1 Components
Login processing: func.php, func1.php, func3.php

Session data for login attempts

5.2 Baseline Behaviour (v0 – vulnerable)
Open login form on index.php.

Repeatedly submit incorrect passwords:

manually, or

using OWASP ZAP Fuzzer.

Count how many attempts are allowed.

Expected v0 result
Unlimited incorrect attempts with no delay, timeout, or lockout.

5.3 Hardened Behaviour (v1 – fixed)
Attempt incorrect logins several times (3–5 attempts).

Optionally repeat using ZAP to automate.

Expected v1 result
After too many failed attempts:

Error such as "Too many failed attempts. Try again later" is shown.

Login endpoint blocks further attempts for a set duration (e.g. 60 seconds).

6. Directory Browsing Exposure
6.1 Components
Web server configuration via .htaccess / httpd.conf

Static directories: /images, /assets, etc.

6.2 Baseline Behaviour (v0 – vulnerable)
Browse to:

bash
Copy code
http://localhost/Hospital-Management-System-master/images/
Test other directories.

Expected v0 result
Apache displays directory contents.

File names and paths are exposed.

6.3 Hardened Behaviour (v1 – fixed)
Repeat after .htaccess fixes.

Expected v1 result
Directory listing disabled.

User receives 403, blank page, or "not allowed" message.
