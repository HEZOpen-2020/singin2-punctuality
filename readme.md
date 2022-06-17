Singin! punctuality
=====
  
![][version] ![][license] ![][build_date]  
![][modloader]![][modloader_1]

[version]: https://img.shields.io/badge/Version-1.2.0%20WIP-violet.svg?style=flat-square
[build_date]: https://img.shields.io/badge/Build%20Date-20220617-darkblue.svg?style=flat-square
[modloader]: https://img.shields.io/badge/Base%20Technology-Apache/PHP-orange.svg?style=flat-square
[modloader_1]: https://img.shields.io/badge/C++-red.svg?style=flat-square
[side]: https://img.shields.io/badge/Side-client-yellow.svg?style=flat-square
[license]: https://img.shields.io/badge/License-MIT%20License-blue.svg?style=flat-square

## What is this project about?

It's a highly specific patcher for a game. You will certainly know what we're talking about if you know our situation.

## Why is the project name spelled wrong?

Because the project is the continuation of another project, whose name is also spelled wrong since the beginning of the development.

## Are my devices qualified?

### Preparation Environment

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

This is sort of complicated. See [setup guide](./doc/setup.md).

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

### Traditional Ability

This function is used for notifying birthdays, so we can have Arubas more precisely.

You have to set `hz2zrun_host` properly in order to use this. If you don't know how to set, refer to a portable sign-in machine in the school.

Before using, send a request `api/hz2zrun/student/sync` to get data.

Check `data/hz2zrun/student/_classes.json` and set other configurations.

Then you can see birthday info on the page.

*Enjoy, and Good Luck : )*

## You may want to ask this

**Q: Is this safe?**

A: Generally not. The patcher is highly specific and may be broken by automatic game updates (or even worse, break the updated game).

**Q: Will classroom singin be fully automated in the future?**

A: Probably not. The conditions are complicated, and full automation is likely to make deadly mistakes.
