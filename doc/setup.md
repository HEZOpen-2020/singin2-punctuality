**Singin! punctuality / docs**

Setup Guide
======

## Prepare carefully

These steps can be done on any computer satisfying the Preparation Environment requirements.

### Compile C++ Part

1. Make sure you have a GNU C++ Compiler with C++17 support.
2. Head to `ext/dbmanip` directory, and run `_compile_dbquery.bat`.
3. Check the output `dbquery.exe`.

You may find that `dbquery.exe` is already in the directory. It is a precompiled version for those who didn't manage to compile it. However it's not always guaranteed to work.

### Download Necessasry Software

Some softwares are required.

- The whole package of your compiled code.
- An apache environment installer with PHP 7 support (if you don't want to bother with this, WampServer may suit you).
- Database explorer supporting encrypted files, like Database 4 .NET (this is probably already present on the server).
- `DesktopLaunchDaemon.zip` archive in this repo.
- Code editor (portable ones recommended).

### Get Your USB Stick Ready

Bring a USB stick containing the softwares above, and head towards the server!

## Just do it

### Overcome Technical Difficulties

1. Locate the game server you want to patch. Find the control box on the side of the machine.
2. Find a technique to plug keyboards and USB sticks into the server.
3. Find the IP address of the server.
4. Locate game service `SmartBoard`.
5. Locate `localData.db` on the server, and crack the password (use your database explorer, and this should not prove difficult).

Extra steps if you want to use Traditional Ability or Run Singin<sup>[WIP]</sup>.

1. Find the IP of the student platform server (this can be found on a handheld sign-in device, try to borrow one).
2. Get to know the naming rule of handheld sign-in devices (probably `runhand#`, like in the sample config).

### Prepare Environment

1. Plug a keyboard into the server (find the technique by yourself).
2. Open command prompt and kill `LockMouse.exe`.
3. Create another administrator account with your custom password for remote control.
4. If you plan to change the password of the default Administrator, open `netplwiz` and reconfigure auto login after doing so, otherwise the auto boot will fail.

### Install Software

1. Plug your USB stick into the server.
2. Unzip `DesktopLaunchDaemon.zip` to `C:\SystemComponent\DesktopLaunchDaemon\`, and set `DesktopLaunchDaemon.exe` to start on boot.  
   Note: For this, you cannot uses the task scheduler. You could put the shortcut of `DesktopLaunchDaemon.exe` into `Startup` folder.
3. Install Apache environment.
4. Configure httpd to start on boot.  
   For WampServer environment, execute `net start wampapache64` on boot using task scheduler.
5. Check the IP address of your client, and edit `httpd.conf` to make the server accept it.
6. Put all your compiled code (except `DesktopLaunchDaemon.zip`) into the web document directory (not necessarily at document root).
7. Rename `data.init` folder to `data`.
8. Remove your USB stick from the server.

### Hide Apache Server

This is not required. It's about hiding your apache environment so it cannot be easily found.

1. Set the installation folder as System Protected and Hidden. Google it if you don't know how.
2. Remove start menu and desktop shortcut. **Empty the recycle bin!**
3. Check register item `HKLM\SOFTWARE\Microsoft\Windows\CurrentVersion\Uninstall\`, find the record with `{wampserver}`, and delete it.

### Server Installation

1. Open `.htaccess` using a proper editor and set `RewriteBase`.  
   For example, if the web URL is `http://10.241.1.9:88/app/singin/`, you should write `RewriteBase /app/singin/`.
2. In `.htaccess`, set a proper `.htpasswd` file, or remove the first section if you don't need password protection (this is not recommended).
3. Rename `internal_config/config.sample.php` to `config.php`, set the `BASIC_URL` constant and other items properly.  
   Check `lib/config_default.php` for more available options.
4. Check the IP address of the server, and access the web URL using a browser on the client to see if there's any errors.

### Client Auto-login

If `.htpasswd` is configured, you can create a URL shortcut on the client PC for convenience. Auto-login can be set in this way:

`http://username:password@example.com:5723/path/to/service/`
