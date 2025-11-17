@echo off
REM Criar tabela OTP no banco de dados
REM Execute este arquivo: criar_tabela_otp.bat

cd /d "%~dp0"

REM Usa mysql CLI para executar o SQL
mysql -u root -p ecoleta < create_otp_table.sql

if %ERRORLEVEL% == 0 (
    echo.
    echo ============================================
    echo Tabela otp_tokens criada com sucesso!
    echo ============================================
    echo.
) else (
    echo.
    echo ============================================
    echo ERRO ao criar tabela!
    echo ============================================
    echo.
)

pause
