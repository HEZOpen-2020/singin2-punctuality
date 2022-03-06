Singin! punctuality
=====

## What is this project about?

It's a highly specific patcher for a game. You will certainly know what we're talking about if you know our situation.

## Why is the project name spelled wrong?

Because the project is the continuation of another project, whose name is also spelled wrong, since the beginning of the development.

## Are my devices qualified?

### Compiling (for the C++ part)

- Windows 7 or higher versions, 64 bits
- GNU C++ Compiler with C++17 support

Note: If your machine does not satisfy the requirements, or you encountered issues while compiling, you can use the already-compiled file `dbquery.exe` in this repo.

### Client

- Chromium/Webkit based browser, the last two versions

### Server

- Windows 7 (untested on other versions, but there's no choice!), 64 bits
- Windows Task scheduler support
- Apache environment, with PHP 7.1.9 (also tested with 7.4.9), which starts on boot.
- mod_rewrite support on Apache server

Note: The game might update automatically, breaking this program. You have been warned.

### Libraries

The project uses [SQLite3MultipleCiphers](https://github.com/utelle/SQLite3MultipleCiphers). The code is present in this repo, so you don't need to download it while building.

## How to setup?

### Compile C++ Part

1. Make sure you have a GNU C++ Compiler with C++17 support.
2. Head to `ext/dbmanip` directory, and run `_compile_dbquery.bat`.
3. Check the output `dbquery.exe`.

You may find that `dbquery.exe` is already in the directory. It is a precompiled version for those who didn't manage to compile it.

### Server Preparation

1. Prepare installer of the Apache environment (you can use Wampserver).
2. Download the `DesktopLaunchDaemon.zip`.
3. Put all your compiled code, along with the two files above and the installer of a code editor (you may need this) into a removable medium.
4. Plug your medium into the server (you may need some techniques to do this).
5. Unzip `DesktopLaunchDaemon.zip` to `C:\SystemComponent\DesktopLaunchDaemon\`, and set `DesktopLaunchDaemon.exe` to start on boot.
   For this, you cannot uses the task scheduler. You could put the shortcut of `DesktopLaunchDaemon.exe` into `Startup` folder.
6. Install Apache environment.
7. Configure httpd to start on boot.
8. Check the IP address of your client, and edit `httpd.conf` to make the server accept it.
9. Put all your compiled code (except `DesktopLaunchDaemon.zip`) into the web document directory (not necessarily at document root).
10. Rename `data.init` folder to `data`.
11. Remove your medium from the server.

### Server Installation

1. Open `.htaccess` using a proper editor and set `RewriteBase`.
   For example, if the web URL is `http://10.241.1.9:88/app/singin/`, you should write `RewriteBase /app/singin/`.
2. In `.htaccess`, set a proper `.htpasswd` file, or remove the first section if you don't need password protection (this is not recommended).
3. Find `localData.db` on the server and crack password (cracking the password does not need any tool, though).
4. Open `internal_config/config.php`, set the `BASIC_URL` constant and other items properly.
   `db_path` `db_password` - Information of `localData.db`.
   `proc_name` - The process name of the GS.Terminal.
   `proc_path` - The program which starts GS.Terminal, most likely GS.Terminal itself.
   `classroom_allow_revoke` - Allow revoking singin. We don't recommend turning it on under production environment as this can be easily abused.
5. Check the IP address of the server, and access the web URL using a browser on the client to see if there's any errors.

## How to use?

### Switching Launguages

Click the info icon on the navigation menu, and then you can see buttons for language switching.

### System Status

On the system status page, you can see the current status and time.

You can use the buttons at the bottom of the page to start/stop GS.Terminal, and reboot the server.

### Classroom Check-in

Once a checking-in lesson is detected, it will show up on the Classroom Check-in page.

Click the button under the name to change the state of a participant. If the icon at the lower-right corner is a tag, you could click on it to toggle check-in type.

After clicking, hover on the floating button and click the send icon. Changes will be submitted automatically. After that, you will be reminded to restart the GS.Terminal.

If something funny happens, check the output of the API using the browser Network panel.

*Enjoy, and Good Luck : )*
