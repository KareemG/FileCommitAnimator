@if (@X)==(@Y) @end /* https://stackoverflow.com/questions/664957/can-i-mask-an-input-text-in-a-bat-file
@echo off
setlocal EnableDelayedExpansion

for /f "tokens=* delims=" %%v in ('dir /b /s /a:-d  /o:-n "%SystemRoot%\Microsoft.NET\Framework\*jsc.exe"') do (
   set "jsc=%%v"
)

if exist ".\bin\%~n0.exe" (
	del ".\bin\%~n0.exe"
)

if not exist ".\bin\%~n0.exe" (
    "%jsc%" /nologo /out:".\bin\%~n0.exe" "%~dpsfnx0"
)

set i=1
for /f "tokens=* delims= " %%p in ('"bin\%~n0.exe"') do (
    set "args[!i!]=%%p"
	set /a i=!i!+1
)

IF !args! == [] exit
php create.php !args[1]! !args[2]!
endlocal & exit /b %errorlevel%

*/

import System;

var pwd = "";
var usr = "";
var key;
Console.Error.WriteLine("Welcome to the FileCommitAnimator! You'll need a Github account to continue.");
Console.Error.WriteLine();
Console.Error.Write("Github Email: ");
do {
   key = Console.ReadKey(true);

   if ( (key.KeyChar.ToString().charCodeAt(0)) >= 20 && (key.KeyChar.ToString().charCodeAt(0) <= 126) ) {
	   usr=usr+(key.KeyChar.ToString());
	   Console.Error.Write(key.KeyChar.ToString());
   }
   
   if ( key.Key == ConsoleKey.Backspace && usr.Length > 0 ) {
	   usr=usr.Remove(usr.Length-1);
	   Console.Error.Write("\b \b");
   }


} while (key.Key != ConsoleKey.Enter);
Console.Error.WriteLine();
Console.WriteLine(usr);

Console.Error.Write("Password: ");
do {
   key = Console.ReadKey(true);

   if ( (key.KeyChar.ToString().charCodeAt(0)) >= 20 && (key.KeyChar.ToString().charCodeAt(0) <= 126) ) {
	  pwd=pwd+(key.KeyChar.ToString());
	  Console.Error.Write("*");
   }

   if ( key.Key == ConsoleKey.Backspace && pwd.Length > 0 ) {
	   pwd=pwd.Remove(pwd.Length-1);
	   Console.Error.Write("\b \b");
   }


} while (key.Key != ConsoleKey.Enter);
Console.Error.WriteLine();
Console.WriteLine(pwd);
Console.Error.WriteLine();