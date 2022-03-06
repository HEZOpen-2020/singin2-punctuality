@echo off
cd /d "%~dp0"
g++ -static -m64 -std=c++17 -Wl,--stack=998244353 -lm dbquery.cpp sqlite3mc_x64.dll -o dbquery.exe
pause
