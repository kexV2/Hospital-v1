## Running the Hospital Management System on XAMPP (Windows)

### Prerequisites

- XAMPP installed (Apache + MySQL).
- A web browser (Chrome, Firefox, etc.).

---

### 1. Get the Source Code

1. Download or clone the repository:

   ```bash
   git clone https://github.com/kishan0725/Hospital-Management-System.git
Copy the project folder into your XAMPP htdocs directory, for example:

makefile
Copy code
C:\xampp\htdocs\Hospital-Management-System-master\
(You can rename the folder if you want; just adjust the URL accordingly.)

2. Start Apache and MySQL
Open the XAMPP Control Panel.

Click Start next to Apache.

Click Start next to MySQL.

Both modules should turn green to show they are running.

3. Create the Database
In your browser, go to:
http://localhost/phpmyadmin

In the left sidebar, click New and create a database named:

nginx
Copy code
myhmsdb
With myhmsdb selected, click the Import tab.

Choose the file myhmsdb.sql from the project (it is located in the repository root).

Click Go to import the schema and sample data.

4. Configure Database Connection (if needed)
The project uses localhost, user root and an empty password by default.
If you have changed your MySQL credentials, update the connection details in the PHP configuration file (e.g. func.php / other included files) so that:

Host = localhost

Username = your MySQL user

Password = your MySQL password

Database = myhmsdb

5. Open the Application
In your browser, navigate to:

arduino
Copy code
http://localhost/Hospital-Management-System-master/
(Use the folder name you placed under htdocs.)
