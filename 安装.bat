@echo off
chcp 437

cd /D %cd%
schtasks /create /tn "DBbackup" /ru system /tr "'%cd%\start.bat'" /sc daily /st 01:00

schtasks /query /xml /tn DBbackup > %cd%\DBbackup.xml

goto modifyAgent

:modifyAgent
cd /D %cd%
set "str1=^<WorkingDirectory^>%cd%^<^/WorkingDirectory^>"
for /f "delims=!" %%i in ('type DBbackup.xml') do (
echo %%i>>%cd%\DBbackupNew.xml
echo "%%i"|findstr "Command" >nul&&echo %str1%>>%cd%\DBbackupNew.xml)
goto import

:import
schtasks.exe /create /tn DBbackup /xml %cd%\DBbackupNew.xml /f
del DBbackup.xml
del DBbackupNew.xml

chcp 936
echo "创建成功，请检查。。。"
pause