# CCTF MANUAL


# Introduction

Capture the Flag (CTF) in computer security is an exercise in which "flags" are secretly hidden in purposefully-vulnerable programs or websites. Competitors steal flags either from other competitors (attack/defence-style CTFs) or from the organizers (jeopardy-style challenges). Several variations exist, including hiding flags in hardware devices. Competitions exist both online and in-person, and can be advanced or entry-level.

Security CTFs are usually designed to serve as an educational exercise to give participants experience in securing a machine, as well as conducting and reacting to the sort of attacks found in the real world.

Classic CTF activities include reverse-engineering, packet sniffing, protocol analysis, system administration, programming, cryptanalysis, and writing exploits, among others. In an attack/defence style competition, each team is given a machine (or a small network) to defend—typically on an isolated competition network. Teams are scored on both their success in defending their assigned machine(s) and on their success in attacking the other team's machines. A variation from classic flag-stealing is to "plant" own flags on opponent's machines.

# Requirements

In order to host the CTF properly in a web server the following requirements should be satisfied.

## Software requirements

1. Install 64 bit server version of Ubuntu OS.

2. Apache server should be installed and it should be in a running stage.

3. SSH service should be in a running stage.

4. PHP, phpMyAdmin, MySQL and MariaDB should also need to be installed.

5. Python3 should be installed.

## Hardware requirements

1. 64 bit CPU architecture

2. 8GB RAM

3. 500GB Hard-disk

4. Internet connectivity with low latency

# Usage

## As an admin

### Dashboard

After log in you can view the **Dashboard** page. In **Dashboard** page you can view who has solved which problem along with ranks. You can also view the count of the following:

- Verified Users
- Pending Users
- Challenges in the competition
- Categories in the competition

![image-4](https://github.com/ISS-CDACK/CCTF/assets/149384071/debf5ef8-2ba9-44f1-a192-2c57bb05f343)

###

### Leaderboard

In the **Leaderboard** tab you can view the rank of the users' along with other details such as:

- Name of the user
- Score of the user
- Number of challenges solved
- Time of last submit

![image-5](https://github.com/ISS-CDACK/CCTF/assets/149384071/54a0be82-427f-44c8-8df6-21102745d020)

### Challenges

The 3rd tab is the **Challenges** tab where you can view the currently added challenges in the competition and add any CTF challenges by clicking on the ADD button.

![image-6](https://github.com/ISS-CDACK/CCTF/assets/149384071/d5d744c9-7861-42d8-8289-6a0d21446f70)

### Categories

The next tab is the **Categories** tab where you can view the current categories and add new category to the competition.

![image-7](https://github.com/ISS-CDACK/CCTF/assets/149384071/ffa59d96-fc29-491c-badf-6c23e88c37c2)

### Visitors

The next tab is the **Visitors** tab where you can get any sort of queries or messages from the participants.


### Sudo Zone

Lastly we have **Sudo Zone** tab where other sub-tabs are also present like **Key Generate** , **Permission** , **Settings** , and **Home**.

**Key Generate**

When a participant forgets his/her password, they may reset their password using a unique key generated by an admin.

To generate the key the admin needs to visit this **Key Generate** tab and enter the email of the respective participant. Then admin can provide the key to the participant.

![image-9](https://github.com/ISS-CDACK/CCTF/assets/149384071/916aac77-af26-429a-8829-c3626a19f1e7)


![image-10](https://github.com/ISS-CDACK/CCTF/assets/149384071/4f362739-a847-4c63-a63a-f55ebbff98a5)

**Permissions**

When a participant registers for the competition he/she needs to be authenticated by an admin to play the competition. The admin may provide the authentication from the permissions tab. If a participant is found to use unfair means, his/her account may be deactivated from the permissions tab.

![image-11](https://github.com/ISS-CDACK/CCTF/assets/149384071/f3d04c56-d280-468f-91c8-1b78ea184c04)

**Settings**

The admin may change his/her name and password from the settings tab.

![image-12](https://github.com/ISS-CDACK/CCTF/assets/149384071/4fa86032-6d97-49b6-af70-fecc82ec6029)

**Home**

The home tab brings you out of the Sudo Zone.

## As an User

### Registration

The very 1st step you need to do is visit the CTF URL page and click on **Let's Go** button.

![image-13](https://github.com/ISS-CDACK/CCTF/assets/149384071/8bcf483e-c914-45e8-8c0b-6fa34aff620b)

Then you will have to register yourself by clicking on the **Sign Up** tab.

![image-14](https://github.com/ISS-CDACK/CCTF/assets/149384071/c489239b-950e-4920-8b3b-c0f31089243e)

After registration you need to wait till the admin has verified your account and given you access to login.

### Login

After your account is verified you may login to the website and start playing the competition.

![image-15](https://github.com/ISS-CDACK/CCTF/assets/149384071/6c62c043-9f3b-4b0c-b341-ba9caa7a10f5)

### Challenges

The home page may look something like this. Here you will find all the questions.

![Image to be uploaded](image-16.png)

You can submit the flag in the dialog of the question itself.

![Image to be uploaded](image-17.png)

You can view your current score, number of challenges solved, your rank and the remaining time in the left-hand side of the panel.

### Leaderboard

You may check your rank from the **Leaderboard** tab.

![image-18](https://github.com/ISS-CDACK/CCTF/assets/149384071/5838a23e-07c0-463a-ba99-d7314c5dcdaa)

### Settings

You can change your name and password from the **Settings** tab.

![image-19](https://github.com/ISS-CDACK/CCTF/assets/149384071/238774cc-3708-4247-bc85-b1f755fc795b)

### Contact Us

You can send us a message using the **Contact Us** page.

![image-20](https://github.com/ISS-CDACK/CCTF/assets/149384071/00d3e5c7-df8b-4f10-a3fb-56d78db789db)

### Forgot Password

If somehow you forget your password you need to click on the Forgot password option in the login page and contact the admin and wait till the admin has given the one-time secret key from his/her end. Then you need to enter the email-ID and the secret-key provided by admin and click submit button. After that you can easily reset your password and login.

![image-21](https://github.com/ISS-CDACK/CCTF/assets/149384071/ee882102-bbd6-4939-978b-adffa6d60fbe)

# Extra Information

There is a scoreboard in the index page which shows the leaderboard without logging in.

![image-22](https://github.com/ISS-CDACK/CCTF/assets/149384071/93ef02fb-a4b6-4844-a732-597aa8361e3e)

The start time and end time of the competition can be configured. To configure it manually go to /includes/comp\_time.php and change the start and end time variables by providing the UNIX timestamp respectively.

![image-23](https://github.com/ISS-CDACK/CCTF/assets/149384071/e27aa68e-a8f0-4f87-9820-8cd4808b8d22)

Alternatively you can also run the following pyhon3 code in using sudo privileges which will take user input and update the timestamp automatically.

Note: The **/includes/comp\_time.php** should be kept inside /var/www/html directory.
```
#!/usr/bin/python3

import os, sys

def check\_privileges():

if not os.environ.get("SUDO\_UID") and os.geteuid() != 0:

sys.exit("The Code Must be run as root")

check\_privileges()

print('NB: Put Date In 24 Hour Format Only')

print('\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\n')

start\_timestamp = ''

end\_timestamp = ''

from datetime import datetime

import math

def make\_timestamp(get\_time):

date\_time = datetime.strptime(get\_time, '%d.%m.%Y %H:%M:%S')

ts = date\_time.timestamp()

# \* 1000

floor\_value = math.floor(ts)

return floor\_value

start\_input\_date = input('Enter Ctf Start Date with time in this format ("23.02.2012 09:12:00") : ')

try:

start\_timestamp = str(make\_timestamp(start\_input\_date))

except Exception as e:

sys.exit("Wrong Ctf Start time Value")

end\_input\_date = input('Enter Ctf End Date with time in this format ("23.02.2012 09:12:00") : ')

try:

end\_timestamp = str(make\_timestamp(end\_input\_date))

except Exception as e:

sys.exit("Wrong Ctf End time Value")

content = "\<?php\n$ctf\_start\_time = "+start\_timestamp+";\n$ctf\_end\_time = "+end\_timestamp+";\n?\>"

f = open("/var/www/html/includes/comp\_time.php", "w")

f.write(content)

f.close()

print('CTF Start And Ending Time Update Successful')
```

There is a PHP file named showtime.php which shows the time remaining of the competition.

![image-24](https://github.com/ISS-CDACK/CCTF/assets/149384071/6755be31-e172-4622-9544-095a4d7a5a2f)

**RIGHT CLICK** is by default prohibited for making the competition more challenging.

# Appendix

The following links can be useful.

[https://ubuntu.com/download/server](https://ubuntu.com/download/server)

[https://magefan.com/blog/install-local-lamp-server-for-ubuntu](https://magefan.com/blog/install-local-lamp-server-for-ubuntu)

[https://magefan.com/blog/how-to-install-phpmyadmin](https://magefan.com/blog/how-to-install-phpmyadmin)

[https://www.digitalocean.com/community/tutorials/how-to-install-mariadb-on-ubuntu-20-04](https://www.digitalocean.com/community/tutorials/how-to-install-mariadb-on-ubuntu-20-04)

[Table of Contents](#_Table_of_Contents) Page 15 of 15
